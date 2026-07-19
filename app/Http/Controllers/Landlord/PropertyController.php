<?php

namespace App\Http\Controllers\Landlord;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Property;

class PropertyController extends Controller
{

    public function index()
    {
        $properties = Property::where(
            'landlord_id',
            Auth::id()
        )
        ->latest()
        ->paginate(10);

        return view(
            'landlord.properties.index',
            compact('properties')
        );
    }

    public function create()
    {
        return view(
            'landlord.properties.create'
        );
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'address' => 'nullable|string',
            'description' => 'nullable|string',
            'monthly_rent' => 'required|numeric|min:0',
            'max_tenants' => 'required|integer|min:1',
        ]);

        $data['landlord_id'] = Auth::id();
        $data['status'] = true;

        Property::create($data);

        return redirect()
            ->route('landlord.properties.index')
            ->with(
                'success',
                'Property created successfully.'
            );
    }

    public function edit(Property $property)
    {
        $this->authorizeProperty($property);

        return view(
            'landlord.properties.edit',
            compact('property')
        );
    }

    public function update(Request $request, Property $property)
    {
        $this->authorizeProperty($property);

        $data = $request->validate([
            'name' => 'required|string',
            'address' => 'nullable|string',
            'description' => 'nullable|string',
            'monthly_rent' => 'required|numeric|min:0',
            'max_tenants' => 'required|integer|min:1',
        ]);

        $property->update($data);

        return redirect()
            ->route('landlord.properties.index')
            ->with(
                'success',
                'Property updated successfully.'
            );
    }

    public function destroy(Property $property)
    {
        $this->authorizeProperty($property);
        $property->delete();

        return back()
            ->with(
                'success',
                'Property deleted.'
            );
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