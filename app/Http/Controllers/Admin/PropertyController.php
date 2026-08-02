<?php
// app/Http/Controllers/Admin/PropertyController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StorePropertyRequest;
use App\Http\Requests\Admin\UpdatePropertyRequest;
use App\Models\Property;
use App\Models\User;
use App\Models\Tenant;
use App\Services\PropertyService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

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
        try {
            $properties = Property::with('landlord')
                ->withCount('tenants')
                ->latest()
                ->paginate(10);

            return view('admin.properties.index', compact('properties'));

        } catch (\Exception $e) {
            Log::error('Failed to load properties list', [
                'error' => $e->getMessage(),
                'admin_id' => auth()->id()
            ]);

            return back()->withErrors(['error' => 'Failed to load properties. Please try again.']);
        }
    }

    /**
     * Show trashed (soft deleted) properties
     */
    public function trashed()
    {
        try {
            $properties = Property::onlyTrashed()
                ->with('landlord')
                ->latest('deleted_at')
                ->paginate(20);

            return view('admin.trash.properties', compact('properties'));

        } catch (\Exception $e) {
            Log::error('Failed to load trashed properties', [
                'error' => $e->getMessage(),
                'admin_id' => auth()->id()
            ]);

            return back()->withErrors(['error' => 'Failed to load archived properties. Please try again.']);
        }
    }

    /**
     * Show create form.
     */
    public function create()
    {
        try {
            $landlords = User::role('Landlord')
                ->latest()
                ->get();

            return view('admin.properties.create', compact('landlords'));

        } catch (\Exception $e) {
            Log::error('Failed to load property creation form', [
                'error' => $e->getMessage(),
                'admin_id' => auth()->id()
            ]);

            return back()->withErrors(['error' => 'Failed to load form. Please try again.']);
        }
    }

    /**
     * Store property.
     */
    public function store(StorePropertyRequest $request): RedirectResponse
    {
        try {
            $property = $this->propertyService->create($request->validated());

            Log::info('Property created by admin', [
                'property_id' => $property->id,
                'property_name' => $property->name,
                'landlord_id' => $property->landlord_id,
                'admin_id' => auth()->id()
            ]);

            return redirect()
                ->route('admin.properties.index')
                ->with('success', 'Property created successfully.');

        } catch (\Exception $e) {
            Log::error('Failed to create property', [
                'error' => $e->getMessage(),
                'admin_id' => auth()->id(),
                'request_data' => $request->validated()
            ]);

            return back()
                ->withInput()
                ->withErrors(['error' => 'Failed to create property. Please try again.']);
        }
    }

    /**
     * Show property.
     */
    public function show(Property $property)
    {
        try {
            $property->load(['landlord', 'tenants']);

            return view('admin.properties.show', compact('property'));

        } catch (\Exception $e) {
            Log::error('Failed to load property details', [
                'property_id' => $property->id,
                'error' => $e->getMessage(),
                'admin_id' => auth()->id()
            ]);

            return back()->withErrors(['error' => 'Failed to load property details. Please try again.']);
        }
    }

    /**
     * Edit form.
     */
    public function edit(Property $property)
    {
        try {
            $landlords = User::role('Landlord')->get();

            return view('admin.properties.edit', compact('property', 'landlords'));

        } catch (\Exception $e) {
            Log::error('Failed to load property edit form', [
                'property_id' => $property->id,
                'error' => $e->getMessage(),
                'admin_id' => auth()->id()
            ]);

            return back()->withErrors(['error' => 'Failed to load edit form. Please try again.']);
        }
    }

    /**
     * Update property.
     */
    public function update(UpdatePropertyRequest $request, Property $property): RedirectResponse
    {
        try {
            $this->propertyService->update($property, $request->validated());

            Log::info('Property updated by admin', [
                'property_id' => $property->id,
                'property_name' => $property->name,
                'landlord_id' => $property->landlord_id,
                'admin_id' => auth()->id(),
                'changes' => $request->validated()
            ]);

            return redirect()
                ->route('admin.properties.index')
                ->with('success', 'Property updated successfully.');

        } catch (\Exception $e) {
            Log::error('Failed to update property', [
                'property_id' => $property->id,
                'error' => $e->getMessage(),
                'admin_id' => auth()->id()
            ]);

            return back()
                ->withInput()
                ->withErrors(['error' => 'Failed to update property. Please try again.']);
        }
    }

    /**
     * Soft delete property.
     */
    public function destroy(Property $property): RedirectResponse
    {
        try {
            // Check if property has active tenants before deletion
            if ($property->tenants()->whereNull('deleted_at')->count() > 0) {
                return back()->withErrors([
                    'error' => 'Cannot delete property with active tenants. Archive tenants first.'
                ]);
            }

            $this->propertyService->delete($property);

            Log::info('Property soft deleted by admin', [
                'property_id' => $property->id,
                'property_name' => $property->name,
                'landlord_id' => $property->landlord_id,
                'admin_id' => auth()->id()
            ]);

            return redirect()
                ->route('admin.properties.index')
                ->with('success', 'Property moved to archive.');

        } catch (\Exception $e) {
            Log::error('Failed to delete property', [
                'property_id' => $property->id,
                'error' => $e->getMessage(),
                'admin_id' => auth()->id()
            ]);

            return back()->withErrors(['error' => 'Failed to delete property. Please try again.']);
        }
    }

    /**
     * Restore a soft deleted property (Admin)
     */
    public function restore($id): RedirectResponse
    {
        try {
            // Use onlyTrashed() to ensure we only restore soft-deleted records
            $property = Property::onlyTrashed()
                ->where('id', $id)
                ->firstOrFail();

            $property->restore();

            Log::info('Property restored by admin', [
                'property_id' => $property->id,
                'property_name' => $property->name,
                'admin_id' => auth()->id()
            ]);

            return redirect()
                ->route('admin.trash.properties')
                ->with('success', 'Property restored successfully.');

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::warning('Attempted to restore non-existent or non-trashed property', [
                'property_id' => $id,
                'admin_id' => auth()->id()
            ]);

            return redirect()
                ->route('admin.trash.properties')
                ->withErrors(['error' => 'Property not found in archive.']);
        } catch (\Exception $e) {
            Log::error('Failed to restore property', [
                'property_id' => $id,
                'error' => $e->getMessage(),
                'admin_id' => auth()->id()
            ]);

            return redirect()
                ->route('admin.trash.properties')
                ->withErrors(['error' => 'Failed to restore property. Please try again.']);
        }
    }

    /**
     * Permanently delete a property (Admin only)
     */
    public function forceDelete($id): RedirectResponse
    {
        DB::beginTransaction();

        try {
            // Use onlyTrashed() to ensure we only delete soft-deleted records
            $property = Property::onlyTrashed()
                ->where('id', $id)
                ->firstOrFail();

            // Check if property has any tenants (including soft deleted ones)
            $tenantCount = Tenant::where('property_id', $property->id)
                ->withTrashed()
                ->count();

            if ($tenantCount > 0) {
                // If there are tenants, permanently delete them first
                // Use forceDelete() on the relationship to permanently remove all tenants
                $property->tenants()->withTrashed()->forceDelete();

                Log::info('Tenants permanently deleted before property deletion', [
                    'property_id' => $property->id,
                    'tenant_count' => $tenantCount,
                    'admin_id' => auth()->id()
                ]);
            }

            // Now permanently delete the property
            $property->forceDelete();

            DB::commit();

            Log::info('Property permanently deleted by admin', [
                'property_id' => $property->id,
                'property_name' => $property->name,
                'admin_id' => auth()->id()
            ]);

            return redirect()
                ->route('admin.trash.properties')
                ->with('success', 'Property permanently deleted.');

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            DB::rollBack();

            Log::warning('Attempted to permanently delete non-existent or non-trashed property', [
                'property_id' => $id,
                'admin_id' => auth()->id()
            ]);

            return redirect()
                ->route('admin.trash.properties')
                ->withErrors(['error' => 'Property not found in archive.']);
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Failed to permanently delete property', [
                'property_id' => $id,
                'error' => $e->getMessage(),
                'admin_id' => auth()->id()
            ]);

            return redirect()
                ->route('admin.trash.properties')
                ->withErrors(['error' => 'Failed to permanently delete property. Please try again.']);
        }
    }
}