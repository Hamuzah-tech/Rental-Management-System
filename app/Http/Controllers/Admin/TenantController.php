<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\Property;
use App\Models\User;
use Illuminate\Http\Request;

class TenantController extends Controller
{
    /**
     * Display tenants
     */
    public function index(Request $request)
    {
        // Get all landlords for the filter
        $landlords = User::role('Landlord')
            ->orderBy('name')
            ->get();

        $tenants = Tenant::with(['property.landlord']);

        // Search by tenant name
        if ($request->filled('search')) {
            $tenants->where('name', 'like', '%' . $request->search . '%');
        }

        // Filter by landlord
        if ($request->filled('landlord')) {
            $tenants->whereHas('property', function ($query) use ($request) {
                $query->where('landlord_id', $request->landlord);
            });
        }

        $tenants = $tenants
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.tenants.index', compact(
            'tenants',
            'landlords'
        ));
    }

    /**
     * Show create form
     */
    public function create()
    {
        $properties = Property::with('landlord')->get();

        return view('admin.tenants.create', compact('properties'));
    }

    /**
     * Store tenant
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'property_id' => [
                'required',
                'exists:properties,id'
            ],
            'name' => [
                'required',
                'string',
                'max:255'
            ],
            'phone' => [
                'required',
                'string',
                'max:50'
            ],
            'email' => [
                'nullable',
                'email'
            ],
            'move_in_date' => [
                'required',
                'date'
            ],
        ]);

        Tenant::create($data);

        return redirect()
            ->route('admin.tenants.index')
            ->with('success', 'Tenant created successfully.');
    }

    /**
     * Show edit form
     */
    public function edit(Tenant $tenant)
    {
        $properties = Property::with('landlord')->get();

        return view(
            'admin.tenants.edit',
            compact(
                'tenant',
                'properties'
            )
        );
    }

    /**
     * Update tenant
     */
    public function update(Request $request, Tenant $tenant)
    {
        $data = $request->validate([
            'property_id' => [
                'required',
                'exists:properties,id'
            ],
            'name' => [
                'required',
                'string',
                'max:255'
            ],
            'phone' => [
                'required',
                'string',
                'max:50'
            ],
            'email' => [
                'nullable',
                'email'
            ],
            'move_in_date' => [
                'required',
                'date'
            ],
        ]);

        $tenant->update($data);

        return redirect()
            ->route('admin.tenants.index')
            ->with('success', 'Tenant updated successfully.');
    }

    /**
     * Delete tenant
     */
    public function destroy(Tenant $tenant)
    {
        $tenant->delete();

        return redirect()
            ->route('admin.tenants.index')
            ->with('success', 'Tenant deleted successfully.');
    }
}