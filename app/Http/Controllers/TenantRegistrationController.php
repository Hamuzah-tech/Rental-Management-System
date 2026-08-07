<?php
// app/Http/Controllers/TenantRegistrationController.php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class TenantRegistrationController extends Controller
{
    /**
     * Show the registration form for a property.
     */
    public function show($token)
    {
        $property = Property::where('registration_token', $token)
            ->where('status', true)
            ->first();

        // Check if property exists
        if (!$property) {
            abort(404);
        }

        // Check if property is active (already checked in query)
        // Check if registration is open
        if (!$property->isRegistrationOpen()) {
            return view('tenant.registration.closed', compact('property'));
        }

        // Check if property is full
        if ($property->isFull()) {
            return view('tenant.registration.full', compact('property'));
        }

        return view('tenant.registration.show', compact('property'));
    }

    /**
     * Store the tenant registration.
     */
    public function store(Request $request, $token)
    {
        // Rate limiting: 3 attempts per 10 minutes per IP
        $throttleKey = 'registration_' . $request->ip();
        
        if (RateLimiter::tooManyAttempts($throttleKey, 3)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            return back()
                ->withInput()
                ->withErrors(['error' => "Too many registration attempts. Please try again in {$seconds} seconds."]);
        }

        try {
            // Lock the property row to prevent race conditions
            $property = Property::where('registration_token', $token)
                ->where('status', true)
                ->lockForUpdate()
                ->firstOrFail();

            // Check if registration is still open (with lock in place)
            if (!$property->isRegistrationOpen()) {
                RateLimiter::hit($throttleKey, 600);
                return back()->with('error', 'Registration has been closed by the landlord.');
            }

            // Double-check property isn't full with the lock in place
            if ($property->isFull()) {
                RateLimiter::hit($throttleKey, 600);
                return back()->with('error', 'This property is full.');
            }

            // Remove commas from the custom rent before validation
            $request->merge([
                'custom_monthly_rent' => $request->filled('custom_monthly_rent')
                    ? str_replace(',', '', $request->custom_monthly_rent)
                    : null,
            ]);

            // Validate the request with custom messages
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255|unique:tenants,email,NULL,id,deleted_at,NULL',
                'phone' => 'required|string|max:20',
                'custom_monthly_rent' => 'nullable|numeric|min:0',
                'move_in_date' => 'required|date|after_or_equal:today|before_or_equal:' . now()->addYears(2)->format('Y-m-d'),
            ], [
                'name.required' => 'Please enter your full name.',
                'name.max' => 'Name cannot exceed 255 characters.',
                'email.required' => 'Please enter your email address.',
                'email.email' => 'Please enter a valid email address.',
                'email.max' => 'Email cannot exceed 255 characters.',
                'email.unique' => 'This email is already registered.',
                'phone.required' => 'Please enter your phone number.',
                'phone.max' => 'Phone number cannot exceed 20 characters.',
                'move_in_date.required' => 'Please select a move-in date.',
                'move_in_date.after_or_equal' => 'Move-in date must be today or a future date.',
                'move_in_date.before_or_equal' => 'Move-in date cannot be more than 2 years in the future.',
                'custom_monthly_rent.numeric' => 'Rent amount must be a number.',
                'custom_monthly_rent.min' => 'Rent amount cannot be negative.',
            ]);

            // Normalize email
            $validated['email'] = strtolower(trim($validated['email']));

            // Check phone format BEFORE checking duplicate
            if (!Tenant::isValidMalawiPhone($validated['phone'])) {
                RateLimiter::hit($throttleKey, 600);
                return back()
                    ->withInput()
                    ->withErrors(['phone' => 'Please enter a valid Malawi phone number.']);
            }

            // Normalize phone for duplicate check
            $normalizedPhone = Tenant::normalizePhoneNumber($validated['phone']);

            // Check for duplicate phone in the same property
            $existingTenant = Tenant::where('phone', $normalizedPhone)
                ->where('property_id', $property->id)
                ->whereNull('deleted_at')
                ->exists();

            if ($existingTenant) {
                RateLimiter::hit($throttleKey, 600);
                return back()
                    ->withInput()
                    ->withErrors(['phone' => 'A tenant with this phone number already exists in this property.']);
            }

            DB::beginTransaction();

            // Re-lock the property inside the transaction to ensure consistency
            $property = Property::where('id', $property->id)
                ->lockForUpdate()
                ->first();

            // Final check - property might have filled up between the first lock and now
            if ($property->isFull()) {
                DB::rollBack();
                RateLimiter::hit($throttleKey, 600);
                return back()->with('error', 'This property is now full. Registration closed.');
            }

            // Check registration status one more time
            if (!$property->isRegistrationOpen()) {
                DB::rollBack();
                RateLimiter::hit($throttleKey, 600);
                return back()->with('error', 'Registration has been closed by the landlord.');
            }

            // Determine monthly rent
            $monthlyRent = $property->monthly_rent ?? 0;
            
            // If custom rent was provided and has a value, use it
            if (!empty($validated['custom_monthly_rent']) && $validated['custom_monthly_rent'] > 0) {
                $monthlyRent = $validated['custom_monthly_rent'];
            }

            // Generate tenant code
            $tenantData = [
                'tenant_code' => 'TEN-' . strtoupper(Str::random(8)),
                'property_id' => $property->id,
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $normalizedPhone,
                'monthly_rent' => $monthlyRent,
                'move_in_date' => $validated['move_in_date'],
                'status' => 'active', // Keep consistent - lowercase
            ];

            $tenant = Tenant::create($tenantData);

            DB::commit();

            // Clear rate limiter on success
            RateLimiter::clear($throttleKey);

            // Safe logging - no sensitive data
            Log::info('New tenant self-registered successfully', [
                'tenant_id' => $tenant->id,
                'property_id' => $property->id,
                'property_name' => $property->name,
                'monthly_rent' => $monthlyRent,
                'has_custom_rent' => !empty($validated['custom_monthly_rent']),
            ]);

            return redirect()
                ->route('tenant.registration.success', $tenant)
                ->with('success', 'Registration successful! Welcome to ' . $property->name);

        } catch (\Illuminate\Validation\ValidationException $e) {
            RateLimiter::hit($throttleKey, 600);
            return back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            RateLimiter::hit($throttleKey, 600);
            
            // Safe logging - no sensitive data
            Log::error('Error during tenant self-registration', [
                'property_id' => $property->id ?? null,
                'error' => $e->getMessage(),
                'ip' => $request->ip()
            ]);
            
            return back()
                ->withInput()
                ->withErrors(['error' => 'Registration failed. Please try again.']);
        }
    }

    /**
     * Show registration success page.
     */
    public function success(Tenant $tenant)
    {
        $tenant->load('property');
        return view('tenant.registration.success', compact('tenant'));
    }

    /**
     * Show property full page.
     */
    public function full(Property $property)
    {
        return view('tenant.registration.full', compact('property'));
    }

    /**
     * Show registration closed page.
     */
    public function closed(Property $property)
    {
        return view('tenant.registration.closed', compact('property'));
    }

    /**
     * Check if phone number is already registered - PER PROPERTY ONLY
     * Rate limited to prevent enumeration attacks.
     */
    public function checkPhone(Request $request)
    {
        // Rate limiting: 10 attempts per minute per IP
        $throttleKey = 'phone_check_' . $request->ip();
        
        if (RateLimiter::tooManyAttempts($throttleKey, 10)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            return response()->json([
                'exists' => false,
                'valid' => false,
                'message' => "Too many attempts. Please try again in {$seconds} seconds.",
                'throttled' => true
            ], 429);
        }

        try {
            $request->validate([
                'phone' => 'required|string|max:20',
                'property_id' => 'required|exists:properties,id'
            ]);

            // Validate phone format
            if (!Tenant::isValidMalawiPhone($request->phone)) {
                RateLimiter::hit($throttleKey, 60);
                return response()->json([
                    'exists' => false,
                    'valid' => false,
                    'message' => 'Please enter a valid Malawi phone number.'
                ]);
            }

            $phone = Tenant::normalizePhoneNumber($request->phone);
            $propertyId = $request->property_id;

            $exists = Tenant::where('phone', $phone)
                ->where('property_id', $propertyId)
                ->whereNull('deleted_at')
                ->exists();

            // Clear rate limiter on successful response
            RateLimiter::clear($throttleKey);

            if ($exists) {
                return response()->json([
                    'exists' => true,
                    'valid' => true,
                    'message' => 'A tenant with this phone number already exists in this property.'
                ]);
            }

            return response()->json([
                'exists' => false,
                'valid' => true,
                'message' => 'Phone number is available for this property.'
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            RateLimiter::hit($throttleKey, 60);
            return response()->json([
                'exists' => false,
                'valid' => false,
                'message' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            RateLimiter::hit($throttleKey, 60);
            
            Log::error('Phone check error', [
                'property_id' => $request->property_id ?? null,
                'error' => $e->getMessage(),
                'ip' => $request->ip()
            ]);
            
            return response()->json([
                'exists' => false,
                'valid' => false,
                'message' => 'An error occurred. Please try again.'
            ], 500);
        }
    }
}