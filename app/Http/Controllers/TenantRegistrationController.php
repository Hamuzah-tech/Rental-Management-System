<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TenantRegistrationController extends Controller
{
    public function show($token)
    {
        $property = Property::where('registration_token', $token)->firstOrFail();

        // Check if property is full
        if ($property->isFull()) {
            return view('tenant.registration-full', compact('property'));
        }

        return view('tenant.registration', compact('property'));
    }

    public function store(Request $request, $token)
    {
        $property = Property::where('registration_token', $token)->firstOrFail();

        // Check if property is full
        if ($property->isFull()) {
            return redirect()
                ->route('tenant.registration.full', $property)
                ->with('error', 'This property has reached maximum capacity.');
        }

        $request->validate([
            'name'  => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
        ]);

        // Generate unique tenant code
        do {
            $code = 'TNT-' . strtoupper(Str::random(6));
        } while (Tenant::where('tenant_code', $code)->exists());

        // Create tenant with monthly rent from property
        $tenant = Tenant::create([
            'tenant_code'   => $code,
            'property_id'   => $property->id,
            'name'          => $request->name,
            'phone'         => $request->phone,
            'email'         => $request->email,
            'monthly_rent'  => $property->monthly_rent ?? 0,
            'move_in_date'  => now(),
            'status'        => 'Active',
        ]);

        return redirect()
            ->route('tenant.registration.success', $tenant)
            ->with('success', 'Registration successful!');
    }

    public function success(Tenant $tenant)
    {
        return view('tenant.registration-success', compact('tenant'));
    }

    public function full(Property $property)
    {
        return view('tenant.registration-full', compact('property'));
    }
}