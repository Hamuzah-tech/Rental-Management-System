<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\Property;
use Illuminate\Http\Request;

class TenantController extends Controller
{
    /**
     * Display tenants
     */
    public function index()
    {
        $tenants = Tenant::with('property')
            ->latest()
            ->paginate(10);

        return view('admin.tenants.index', compact('tenants'));
    }


    /**
     * Show create form
     */
    public function create()
    {
        $properties = Property::all();

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
            ->with('success','Tenant created successfully.');
    }



    /**
     * Show edit form
     */
    public function edit(Tenant $tenant)
    {
        $properties = Property::all();


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
            ->with('success','Tenant updated successfully.');

    }




    /**
     * Delete tenant
     */
    public function destroy(Tenant $tenant)
    {

        $tenant->delete();


        return redirect()
            ->route('admin.tenants.index')
            ->with('success','Tenant deleted successfully.');

    }
}