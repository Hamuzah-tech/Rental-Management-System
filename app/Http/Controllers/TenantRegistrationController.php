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
            // Validate with rent option logic
            $rules = [
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'phone' => [
                    'required',
                    'string',
                    'max:20',
                    function ($attribute, $value, $fail) use ($property) {
                        $existing = Tenant::where('phone', $value)
                            ->where('property_id', $property->id)
                            ->whereNull('deleted_at')
                            ->exists();
                        
                        if ($existing) {
                            $fail('This phone number is already registered for this property.');
                        }
                    }
                ],
                'rent_option' => 'required|in:default,custom',
                'move_in_date' => 'required|date',
            ];

            // Add conditional validation for custom rent
            if ($request->rent_option === 'custom') {
                $rules['custom_monthly_rent'] = 'required|numeric|min:0';
            }

            $data = $request->validate($rules);

            // Determine monthly rent
            if ($data['rent_option'] === 'default') {
                $monthlyRent = $property->monthly_rent ?? 0;
            } else {
                $monthlyRent = $data['custom_monthly_rent'];
            }

            // Generate tenant code
            $tenantData = [
                'tenant_code' => 'TEN-' . strtoupper(Str::random(8)),
                'property_id' => $property->id,
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'],
                'monthly_rent' => $monthlyRent,
                'move_in_date' => $data['move_in_date'],
                'status' => 'active',
            ];

            $tenant = Tenant::create($tenantData);

            Log::info('New tenant registered', [
                'tenant_id' => $tenant->id,
                'property_id' => $property->id,
                'monthly_rent' => $tenant->monthly_rent,
                'rent_option' => $data['rent_option'],
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

        return response()->json([
            'exists' => false,
            'message' => 'Phone number is available for this property.'
        ]);
    }
}