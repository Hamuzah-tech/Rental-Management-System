<?php
// app/Http/Controllers/TenantRegistrationController.php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class TenantRegistrationController extends Controller
{
    /**
     * Show the registration form for a property.
     */
    public function show($token)
    {
        $property = Property::where('registration_token', $token)
            ->where('status', true)
            ->firstOrFail();

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
        $property = Property::where('registration_token', $token)
            ->where('status', true)
            ->firstOrFail();

        if ($property->isFull()) {
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
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'custom_monthly_rent' => 'nullable|numeric|min:0',
            'move_in_date' => 'required|date|after_or_equal:today',
        ], [
            'name.required' => 'Please enter your full name.',
            'name.max' => 'Name cannot exceed 255 characters.',
            'email.required' => 'Please enter your email address.',
            'email.email' => 'Please enter a valid email address.',
            'email.max' => 'Email cannot exceed 255 characters.',
            'phone.required' => 'Please enter your phone number.',
            'phone.max' => 'Phone number cannot exceed 20 characters.',
            'move_in_date.required' => 'Please select a move-in date.',
            'move_in_date.after_or_equal' => 'Move-in date must be today or a future date.',
            'custom_monthly_rent.numeric' => 'Rent amount must be a number.',
            'custom_monthly_rent.min' => 'Rent amount cannot be negative.',
        ]);

        // Check phone format BEFORE checking duplicate
        if (!Tenant::isValidMalawiPhone($validated['phone'])) {
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
            return back()
                ->withInput()
                ->withErrors(['phone' => 'A tenant with this phone number already exists in this property.']);
        }

        try {
            DB::beginTransaction();

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
                'status' => 'active',
            ];

            $tenant = Tenant::create($tenantData);

            // Clear the registration token after successful registration
            $property->update(['registration_token' => null]);

            DB::commit();

            Log::info('New tenant self-registered successfully', [
                'tenant_id' => $tenant->id,
                'property_id' => $property->id,
                'monthly_rent' => $tenant->monthly_rent,
                'custom_monthly_rent' => $request->custom_monthly_rent,
                'property_default_rent' => $property->monthly_rent,
                'phone' => $tenant->phone,
                'token' => $token
            ]);

            return redirect()
                ->route('tenant.registration.success', $tenant)
                ->with('success', 'Registration successful! Welcome to ' . $property->name);

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Error during tenant self-registration: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'token' => $token,
                'request_data' => $request->except(['_token'])
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
     * Check if phone number is already registered - PER PROPERTY ONLY
     */
    public function checkPhone(Request $request)
    {
        $request->validate([
            'phone' => 'required|string|max:20',
            'property_id' => 'required|exists:properties,id'
        ]);

        // Validate phone format
        if (!Tenant::isValidMalawiPhone($request->phone)) {
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
    }
}