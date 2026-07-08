<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StorePropertyRequest;
use App\Http\Requests\Admin\UpdatePropertyRequest;
use App\Models\Property;
use App\Models\User;
use App\Services\PropertyService;
use Illuminate\Http\RedirectResponse;

class PropertyController extends Controller
{

    public function __construct(
        protected PropertyService $propertyService
    ) {
    }


    /**
     * Display properties.
     */
    public function index()
    {
        $properties = Property::with('landlord')
            ->latest()
            ->paginate(10);


        return view(
            'admin.properties.index',
            compact('properties')
        );
    }



    /**
     * Show create form.
     */
    public function create()
    {

        $landlords = User::role('Landlord')
            ->latest()
            ->get();


        return view(
            'admin.properties.create',
            compact('landlords')
        );
    }



    /**
     * Store property.
     */
    public function store(StorePropertyRequest $request): RedirectResponse
    {

        $this->propertyService->create(
            $request->validated()
        );


        return redirect()
            ->route('admin.properties.index')
            ->with(
                'success',
                'Property created successfully.'
            );

    }



    /**
     * Show property.
     */
    public function show(Property $property)
    {

        $property->load([
            'landlord',
            'tenants'
        ]);


        return view(
            'admin.properties.show',
            compact('property')
        );

    }



    /**
     * Edit form.
     */
    public function edit(Property $property)
    {

        $landlords = User::role('Landlord')
            ->get();


        return view(
            'admin.properties.edit',
            compact(
                'property',
                'landlords'
            )
        );

    }




    /**
     * Update property.
     */
    public function update(
        UpdatePropertyRequest $request,
        Property $property
    ): RedirectResponse {


        $this->propertyService->update(
            $property,
            $request->validated()
        );


        return redirect()
            ->route('admin.properties.index')
            ->with(
                'success',
                'Property updated successfully.'
            );

    }




    /**
     * Delete property.
     */
    public function destroy(Property $property): RedirectResponse
    {

        $this->propertyService->delete($property);


        return redirect()
            ->route('admin.properties.index')
            ->with(
                'success',
                'Property deleted successfully.'
            );

    }

}