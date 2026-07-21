<?php
// app/Http/Controllers/Landlord/PropertyController.php

namespace App\Http\Controllers\Landlord;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\Property;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf; // ADD THIS

class PropertyController extends Controller
{
    /**
     * Display a listing of properties.
     */
    public function index()
    {
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

    /**
     * Show the form for creating a new property.
     */
    public function create()
    {
        return view('landlord.properties.create');
    }

    /**
     * Store a newly created property in storage.
     */
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

    /**
     * Display the specified property with its tenants.
     */
    public function show(Request $request, Property $property)
    {
        $this->authorizeProperty($property);
        
        // Get filter parameters
        $month = $request->month ?? null;
        $paymentStatus = $request->payment_status ?? 'all';
        
        // Load tenants with payments
        $property->load(['tenants' => function ($q) {
            $q->with(['payments' => function ($pq) {
                $pq->where('status', 'Approved');
            }]);
        }]);
        
        // Filter tenants by month and payment status
        $tenants = $property->tenants;
        
        // Filter by month
        if ($month) {
            $tenants = $tenants->filter(function ($tenant) use ($month) {
                $hasPaymentForMonth = $tenant->payments->filter(function ($payment) use ($month) {
                    $months = explode(',', $payment->payment_month);
                    return in_array($month, array_map('trim', $months));
                })->count() > 0;
                
                return $hasPaymentForMonth;
            });
        }
        
        // Filter by payment status
        if ($paymentStatus === 'paid') {
            $tenants = $tenants->filter(function ($tenant) {
                return $tenant->payments->count() > 0;
            });
        } elseif ($paymentStatus === 'unpaid') {
            $tenants = $tenants->filter(function ($tenant) {
                return $tenant->payments->count() === 0;
            });
        }
        
        // Generate month options: August 2026 to December 2027
        $months = [];
        $startDate = Carbon::createFromDate(2026, 8, 1);
        $endDate = Carbon::createFromDate(2027, 12, 1);
        
        for ($date = clone $startDate; $date <= $endDate; $date->addMonth()) {
            $months[$date->format('Y-m')] = $date->format('F Y');
        }
        
        return view('landlord.properties.show', compact('property', 'tenants', 'months', 'month', 'paymentStatus'));
    }

    /**
     * Export property tenants to PDF.
     */
    public function exportPdf(Request $request, Property $property)
    {
        $this->authorizeProperty($property);
        
        // Get filter parameters
        $month = $request->month ?? null;
        $paymentStatus = $request->payment_status ?? 'all';
        
        // Load tenants with payments
        $property->load(['tenants' => function ($q) {
            $q->with(['payments' => function ($pq) {
                $pq->where('status', 'Approved');
            }]);
        }]);
        
        // Filter tenants by month and payment status
        $tenants = $property->tenants;
        
        if ($month) {
            $tenants = $tenants->filter(function ($tenant) use ($month) {
                $hasPaymentForMonth = $tenant->payments->filter(function ($payment) use ($month) {
                    $months = explode(',', $payment->payment_month);
                    return in_array($month, array_map('trim', $months));
                })->count() > 0;
                return $hasPaymentForMonth;
            });
        }
        
        if ($paymentStatus === 'paid') {
            $tenants = $tenants->filter(function ($tenant) {
                return $tenant->payments->count() > 0;
            });
        } elseif ($paymentStatus === 'unpaid') {
            $tenants = $tenants->filter(function ($tenant) {
                return $tenant->payments->count() === 0;
            });
        }
        
        // Generate PDF using existing view
        $pdf = Pdf::loadView('exports.tenants-pdf', [
            'tenants' => $tenants,
            'property' => $property,
            'paymentStatus' => $paymentStatus,
            'month' => $month,
            'landlord' => Auth::user(),
            'generatedAt' => now()
        ]);
        
        $pdf->setPaper('A4', 'landscape');
        
        $filename = 'property_tenants_' . $property->id . '_' . date('Y-m-d') . '.pdf';
        return $pdf->download($filename);
    }

    /**
     * Show the form for editing the specified property.
     */
    public function edit(Property $property)
    {
        $this->authorizeProperty($property);
        return view('landlord.properties.edit', compact('property'));
    }

    /**
     * Update the specified property in storage.
     */
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

    /**
     * Toggle property status (Active/Inactive).
     */
    public function toggleStatus(Property $property)
    {
        $this->authorizeProperty($property);

        $property->update([
            'status' => !$property->status
        ]);

        return back()->with('success', 'Property status updated.');
    }

    /**
     * Authorize that the property belongs to the current landlord.
     */
    private function authorizeProperty(Property $property)
    {
        abort_if(
            $property->landlord_id !== Auth::id(),
            403
        );
    }
}