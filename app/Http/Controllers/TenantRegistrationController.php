<?php
// app/Http/Controllers/TenantRegistrationController.php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\Tenant;
use Illuminate\Http\Request;
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
        $property = Property::where('registration_token', $token)
            ->where('status', true)
            ->firstOrFail();

        // Check if property is full
        if ($property->isFull()) {
            return redirect()
                ->route('tenant.registration.full', $property)
                ->with('error', 'This property is full.');
        }

        try {
            // Validate with phone uniqueness check - PER PROPERTY ONLY
            $data = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'phone' => [
                    'required',
                    'string',
                    'max:20',
                    // Phone uniqueness check - ONLY for this specific property
                    function ($attribute, $value, $fail) use ($property) {
                        // Check if this phone is already registered to THIS property
                        $existing = Tenant::where('phone', $value)
                            ->where('property_id', $property->id)
                            ->whereNull('deleted_at')
                            ->exists();
                        
                        if ($existing) {
                            $fail('This phone number is already registered for this property.');
                        }
                        // ✅ REMOVED: The check for other properties
                        // Now phone numbers can be reused across different properties
                    }
                ],
                'monthly_rent' => 'nullable|numeric|min:0',
                'move_in_date' => 'required|date',
            ]);

            // Generate tenant code
            $data['tenant_code'] = 'TEN-' . strtoupper(Str::random(8));
            $data['property_id'] = $property->id;
            $data['status'] = 'active';

            // Check if property has monthly rent set
            if ($property->monthly_rent > 0) {
                $data['monthly_rent'] = $property->monthly_rent;
            }

            $tenant = Tenant::create($data);

            Log::info('New tenant registered', [
                'tenant_id' => $tenant->id,
                'property_id' => $property->id,
                'phone' => $tenant->phone
            ]);

            return redirect()
                ->route('tenant.registration.success', $tenant)
                ->with('success', 'Registration successful! Welcome to ' . $property->name);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            Log::error('Registration error: ' . $e->getMessage());
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

        $phone = $request->phone;
        $propertyId = $request->property_id;

        // ✅ ONLY check if phone exists for THIS SPECIFIC property
        $exists = Tenant::where('phone', $phone)
            ->where('property_id', $propertyId)
            ->whereNull('deleted_at')
            ->exists();

        if ($exists) {
            return response()->json([
                'exists' => true,
                'message' => 'This phone number is already registered for this property.'
            ]);
        }

        // ✅ Phone is available for this property
        return response()->json([
            'exists' => false,
            'message' => 'Phone number is available for this property.'
        ]);
    }
}