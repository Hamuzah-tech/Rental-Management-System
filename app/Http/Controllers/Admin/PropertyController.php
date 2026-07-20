<?php
// app/Http/Controllers/Admin/PropertyController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StorePropertyRequest;
use App\Http\Requests\Admin\UpdatePropertyRequest;
use App\Models\Property;
use App\Models\User;
use App\Services\PropertyService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PropertyController extends Controller
{
    public function __construct(
        protected PropertyService $propertyService
    ) {
    }

    /**
     * Display properties (excluding soft deleted)
     */
    public function index()
    {
        $properties = Property::with('landlord')
            ->withCount('tenants')
            ->latest()
            ->paginate(10);

        return view('admin.properties.index', compact('properties'));
    }

    /**
     * Show trashed (soft deleted) properties
     */
    public function trashed()
    {
        $properties = Property::onlyTrashed()
            ->with('landlord')
            ->latest('deleted_at')
            ->paginate(20);

        return view('admin.trash.properties', compact('properties'));
    }

    /**
     * Show create form.
     */
    public function create()
    {
        $landlords = User::role('Landlord')
            ->latest()
            ->get();

        return view('admin.properties.create', compact('landlords'));
    }

    /**
     * Store property.
     */
    public function store(StorePropertyRequest $request): RedirectResponse
    {
        $this->propertyService->create($request->validated());

        return redirect()
            ->route('admin.properties.index')
            ->with('success', 'Property created successfully.');
    }

    /**
     * Show property.
     */
    public function show(Property $property)
    {
        $property->load(['landlord', 'tenants']);

        return view('admin.properties.show', compact('property'));
    }

    /**
     * Edit form.
     */
    public function edit(Property $property)
    {
        $landlords = User::role('Landlord')->get();

        return view('admin.properties.edit', compact('property', 'landlords'));
    }

    /**
     * Update property.
     */
    public function update(UpdatePropertyRequest $request, Property $property): RedirectResponse
    {
        $this->propertyService->update($property, $request->validated());

        return redirect()
            ->route('admin.properties.index')
            ->with('success', 'Property updated successfully.');
    }

    /**
     * Soft delete property.
     */
    public function destroy(Property $property): RedirectResponse
    {
        $this->propertyService->delete($property);

        return redirect()
            ->route('admin.properties.index')
            ->with('success', 'Property moved to archive.');
    }

    /**
     * Restore a soft deleted property (Admin)
     */
    public function restore($id): RedirectResponse
    {
        $property = Property::withTrashed()->findOrFail($id);
        $property->restore();

        Log::info('Property restored by admin', [
            'property_id' => $property->id,
            'admin_id' => auth()->id()
        ]);

        return redirect()
            ->route('admin.trash.properties')
            ->with('success', 'Property restored successfully.');
    }

    /**
     * Permanently delete a property (Admin only)
     */
    public function forceDelete($id): RedirectResponse
    {
        $property = Property::withTrashed()->findOrFail($id);
        
        // Delete related tenants first
        $property->tenants()->delete();
        
        $property->forceDelete();

        Log::info('Property permanently deleted by admin', [
            'property_id' => $property->id,
            'admin_id' => auth()->id()
        ]);

        return redirect()
            ->route('admin.trash.properties')
            ->with('success', 'Property permanently deleted.');
    }
}