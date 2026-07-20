<?php
// app/Http/Controllers/Landlord/PropertyController.php

namespace App\Http\Controllers\Landlord;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\Property;

class PropertyController extends Controller
{
    public function index()
    {
        // Only show non-deleted properties
        $properties = Property::where('landlord_id', Auth::id())
            ->latest()
            ->paginate(10);

        return view('landlord.properties.index', compact('properties'));
    }

    /**
     * Show trashed (soft deleted) properties
     */
    public function trashed()
    {
        $properties = Property::where('landlord_id', Auth::id())
            ->onlyTrashed()
            ->latest('deleted_at')
            ->paginate(10);

        return view('landlord.properties.trashed', compact('properties'));
    }

    public function create()
    {
        return view('landlord.properties.create');
    }

    public function store(Request $request)
    {
        Log::info('Property store called', $request->all());
        
        try {
            $data = $request->validate([
                'name' => 'required|string|max:255',
                'address' => 'nullable|string|max:255',
                'description' => 'nullable|string',
                'monthly_rent' => 'required|numeric|min:0',
                'max_tenants' => 'required|integer|min:1',
            ]);

            $data['landlord_id'] = Auth::id();
            $data['status'] = true;
            $data['registration_token'] = \Illuminate\Support\Str::random(40);

            $property = Property::create($data);

            Log::info('Property created successfully', ['id' => $property->id]);

            return redirect()
                ->route('landlord.properties.index')
                ->with('success', 'Property created successfully.');

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation failed', ['errors' => $e->errors()]);
            return back()
                ->withErrors($e->errors())
                ->withInput();
                
        } catch (\Exception $e) {
            Log::error('Error creating property: ' . $e->getMessage());
            return back()
                ->withInput()
                ->withErrors(['error' => 'Failed to create property: ' . $e->getMessage()]);
        }
    }

    public function edit(Property $property)
    {
        $this->authorizeProperty($property);
        return view('landlord.properties.edit', compact('property'));
    }

    public function update(Request $request, Property $property)
    {
        $this->authorizeProperty($property);

        try {
            $data = $request->validate([
                'name' => 'required|string|max:255',
                'address' => 'nullable|string|max:255',
                'description' => 'nullable|string',
                'monthly_rent' => 'required|numeric|min:0',
                'max_tenants' => 'required|integer|min:1',
            ]);

            $property->update($data);

            Log::info('Property updated successfully', ['id' => $property->id]);

            return redirect()
                ->route('landlord.properties.index')
                ->with('success', 'Property updated successfully.');

        } catch (\Exception $e) {
            Log::error('Error updating property: ' . $e->getMessage());
            return back()
                ->withInput()
                ->withErrors(['error' => 'Failed to update property: ' . $e->getMessage()]);
        }
    }

    /**
     * Soft delete property (moves to archive)
     */
    public function destroy(Property $property)
    {
        $this->authorizeProperty($property);
        $property->delete();

        Log::info('Property soft deleted', ['id' => $property->id]);

        return redirect()
            ->route('landlord.properties.index')
            ->with('success', 'Property moved to archive.');
    }

    /**
     * Restore a soft deleted property
     */
    public function restore($id)
    {
        $property = Property::withTrashed()->findOrFail($id);
        $this->authorizeProperty($property);
        
        $property->restore();

        Log::info('Property restored', ['id' => $property->id]);

        return redirect()
            ->route('landlord.properties.trashed')
            ->with('success', 'Property restored successfully.');
    }

    public function toggleStatus(Property $property)
    {
        $this->authorizeProperty($property);

        $property->update([
            'status' => !$property->status
        ]);

        return back()->with('success', 'Property status updated.');
    }

    private function authorizeProperty(Property $property)
    {
        abort_if(
            $property->landlord_id !== Auth::id(),
            403
        );
    }
}