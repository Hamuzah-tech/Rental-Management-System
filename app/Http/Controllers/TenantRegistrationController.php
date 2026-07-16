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

        return view('tenant.registration', compact('property', 'token'));
    }

    public function store(Request $request, $token)
    {
        $property = Property::where('registration_token', $token)->firstOrFail();

        $request->validate([
            'name'  => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
        ]);

        $tenant = Tenant::create([
            'tenant_code' => 'TNT-' . strtoupper(Str::random(6)),
            'property_id' => $property->id,
            'name'        => $request->name,
            'phone'       => $request->phone,
            'email'       => $request->email,
            'monthly_rent'=> $property->monthly_rent ?? 0,
            'move_in_date'=> now(),
            'status'      => 'Active',
        ]);

        return redirect()
            ->route('tenant.registration.success', $tenant)
            ->with('success', 'Registration successful!');
    }

    public function success(Tenant $tenant)
    {
        return view('tenant.registration-success', compact('tenant'));
    }
}