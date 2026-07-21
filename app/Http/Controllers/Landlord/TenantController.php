<?php
// app/Http/Controllers/Landlord/TenantController.php

namespace App\Http\Controllers\Landlord;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;

class TenantController extends Controller
{
    /**
     * Display a listing of tenants.
     */
    public function index(Request $request)
    {
        $query = Tenant::whereHas('property', function ($q) {
            $q->where('landlord_id', Auth::id());
        });

        // Filter by property
        if ($request->filled('property_id')) {
            $query->where('property_id', $request->property_id);
        }

        // Filter by tenant status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by payment status (paid/unpaid)
        if ($request->filled('payment_status')) {
            $paymentStatus = $request->payment_status;
            
            if ($paymentStatus === 'paid') {
                $query->whereHas('payments', function ($q) {
                    $q->where('status', 'Approved');
                });
            } elseif ($paymentStatus === 'unpaid') {
                $query->whereDoesntHave('payments', function ($q) {
                    $q->where('status', 'Approved');
                });
            }
        }

        // Filter by specific month
        if ($request->filled('month')) {
            $month = $request->month;
            $query->whereHas('payments', function ($q) use ($month) {
                $q->where('payment_month', 'LIKE', $month . '%')
                  ->orWhere('payment_month', 'LIKE', '%,' . $month . '%')
                  ->orWhere('payment_month', 'LIKE', '%,' . $month . ',%');
            });
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%")
                  ->orWhere('phone', 'LIKE', "%{$search}%")
                  ->orWhere('tenant_code', 'LIKE', "%{$search}%");
            });
        }

        $tenants = $query->latest()->paginate(10);
        $properties = Property::where('landlord_id', Auth::id())->get();

        return view('landlord.tenants.index', compact('tenants', 'properties'));
    }

    /**
     * Show trashed (soft deleted) tenants
     */
    public function trashed()
    {
        $tenants = Tenant::whereHas('property', function ($q) {
            $q->where('landlord_id', Auth::id());
        })
        ->onlyTrashed()
        ->with('property')
        ->latest('deleted_at')
        ->paginate(10);

        return view('landlord.tenants.trashed', compact('tenants'));
    }

    /**
     * Show the form for creating a new tenant.
     */
    public function create()
    {
        $properties = Property::where('landlord_id', Auth::id())
            ->where('status', true)
            ->get();

        return view('landlord.tenants.create', compact('properties'));
    }

    /**
     * Store a newly created tenant in storage.
     */
    public function store(Request $request)
    {
        try {
            $data = $request->validate([
                'property_id' => 'required|exists:properties,id',
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'phone' => 'required|string|max:20',
                'monthly_rent' => 'required|numeric|min:0',
                'move_in_date' => 'required|date',
                'status' => 'sometimes|boolean',
            ]);

            // Check if property belongs to this landlord
            $property = Property::where('id', $data['property_id'])
                ->where('landlord_id', Auth::id())
                ->firstOrFail();

            // Check if property is full
            if ($property->isFull()) {
                return back()
                    ->withInput()
                    ->withErrors(['property_id' => 'This property has reached maximum tenant capacity.']);
            }

            // Generate tenant code
            $data['tenant_code'] = 'TEN-' . strtoupper(Str::random(8));
            $data['status'] = 'active';

            $tenant = Tenant::create($data);

            Log::info('Tenant created successfully', [
                'id' => $tenant->id,
                'monthly_rent' => $tenant->monthly_rent
            ]);

            return redirect()
                ->route('landlord.tenants.index')
                ->with('success', 'Tenant created successfully.');

        } catch (\Exception $e) {
            Log::error('Error creating tenant: ' . $e->getMessage());
            return back()
                ->withInput()
                ->withErrors(['error' => 'Failed to create tenant: ' . $e->getMessage()]);
        }
    }

    /**
     * Display the specified tenant.
     */
    public function show(Tenant $tenant)
    {
        $this->authorizeTenant($tenant);
        $tenant->load(['property', 'payments' => function ($q) {
            $q->latest()->limit(10);
        }]);

        return view('landlord.tenants.show', compact('tenant'));
    }

    /**
     * Show the form for editing the specified tenant.
     */
    public function edit(Tenant $tenant)
    {
        $this->authorizeTenant($tenant);

        $properties = Property::where('landlord_id', Auth::id())
            ->where('status', true)
            ->get();

        return view('landlord.tenants.edit', compact('tenant', 'properties'));
    }

    /**
     * Update the specified tenant in storage.
     */
    public function update(Request $request, Tenant $tenant)
    {
        $this->authorizeTenant($tenant);

        try {
            $data = $request->validate([
                'property_id' => 'required|exists:properties,id',
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'phone' => 'required|string|max:20',
                'monthly_rent' => 'required|numeric|min:0',
                'move_in_date' => 'required|date',
                'status' => 'required|in:active,inactive,moved_out',
            ]);

            // Check if property belongs to this landlord
            Property::where('id', $data['property_id'])
                ->where('landlord_id', Auth::id())
                ->firstOrFail();

            $tenant->update($data);

            Log::info('Tenant updated successfully', [
                'id' => $tenant->id,
                'monthly_rent' => $tenant->monthly_rent
            ]);

            return redirect()
                ->route('landlord.tenants.index')
                ->with('success', 'Tenant updated successfully.');

        } catch (\Exception $e) {
            Log::error('Error updating tenant: ' . $e->getMessage());
            return back()
                ->withInput()
                ->withErrors(['error' => 'Failed to update tenant: ' . $e->getMessage()]);
        }
    }

    /**
     * Soft delete the specified tenant (move to archive).
     */
    public function destroy(Tenant $tenant)
    {
        $this->authorizeTenant($tenant);
        $tenant->delete();

        Log::info('Tenant soft deleted', ['id' => $tenant->id]);

        return redirect()
            ->route('landlord.tenants.index')
            ->with('success', 'Tenant moved to archive.');
    }

    /**
     * Restore a soft deleted tenant.
     */
    public function restore($id)
    {
        $tenant = Tenant::onlyTrashed()->findOrFail($id);
        $this->authorizeTenant($tenant);
        
        $tenant->restore();

        Log::info('Tenant restored', ['id' => $tenant->id]);

        return redirect()
            ->route('landlord.tenants.trashed')
            ->with('success', 'Tenant restored successfully.');
    }

    /**
     * Generate a registration link for a tenant.
     */
    public function generateRegistrationLink(Request $request)
    {
        try {
            $request->validate([
                'property_id' => 'required|exists:properties,id',
            ]);

            $property = Property::where('id', $request->property_id)
                ->where('landlord_id', Auth::id())
                ->firstOrFail();

            if ($property->isFull()) {
                return response()->json([
                    'success' => false,
                    'message' => 'This property is full. Cannot generate registration link.'
                ], 422);
            }

            if (empty($property->registration_token)) {
                $property->update([
                    'registration_token' => Str::random(40)
                ]);
            }

            $registrationLink = route('tenant.registration', [
                'token' => $property->registration_token
            ]);

            Log::info('Registration link generated', [
                'property_id' => $property->id,
                'landlord_id' => Auth::id()
            ]);

            return response()->json([
                'success' => true,
                'link' => $registrationLink,
                'token' => $property->registration_token,
                'message' => 'Registration link generated successfully.'
            ]);

        } catch (\Exception $e) {
            Log::error('Error generating registration link: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate registration link: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Move out a tenant.
     */
    public function moveOut(Tenant $tenant)
    {
        $this->authorizeTenant($tenant);

        $tenant->update([
            'status' => 'moved_out',
            'move_out_date' => now(),
        ]);

        $tenant->delete();

        Log::info('Tenant moved out', ['id' => $tenant->id]);

        return redirect()
            ->route('landlord.tenants.index')
            ->with('success', 'Tenant moved out successfully.');
    }

    /**
     * Reactivate a tenant.
     */
    public function reactivate(Tenant $tenant)
    {
        $this->authorizeTenant($tenant);

        $tenant->update([
            'status' => 'active',
            'move_out_date' => null,
        ]);

        Log::info('Tenant reactivated', ['id' => $tenant->id]);

        return back()->with('success', 'Tenant reactivated successfully.');
    }

    /**
     * Export tenants to PDF.
     */
    public function exportPdf(Request $request)
    {
        // Get filter parameters
        $paymentStatus = $request->payment_status ?? 'all';
        $month = $request->month ?? null;

        // Build query with filters
        $query = Tenant::whereHas('property', function ($q) {
            $q->where('landlord_id', Auth::id());
        });

        // Filter by payment status
        if ($paymentStatus === 'paid') {
            $query->whereHas('payments', function ($q) {
                $q->where('status', 'Approved');
            });
        } elseif ($paymentStatus === 'unpaid') {
            $query->whereDoesntHave('payments', function ($q) {
                $q->where('status', 'Approved');
            });
        }

        // Filter by month
        if ($month) {
            $query->whereHas('payments', function ($q) use ($month) {
                $q->where('payment_month', 'LIKE', $month . '%')
                  ->orWhere('payment_month', 'LIKE', '%,' . $month . '%')
                  ->orWhere('payment_month', 'LIKE', '%,' . $month . ',%');
            });
        }

        $tenants = $query->with(['property', 'payments' => function ($q) {
            $q->where('status', 'Approved');
        }])->get();

        // Generate PDF
        $pdf = Pdf::loadView('exports.tenants-pdf', [
            'tenants' => $tenants,
            'paymentStatus' => $paymentStatus,
            'month' => $month,
            'landlord' => Auth::user(),
            'generatedAt' => now()
        ]);

        // Set paper size and orientation
        $pdf->setPaper('A4', 'landscape');

        // Download the PDF
        $filename = 'tenants_export_' . date('Y-m-d_H-i-s') . '.pdf';
        return $pdf->download($filename);
    }

    /**
     * Export tenants to Excel.
     */
    public function exportExcel()
    {
        // Implementation for Excel export
    }

    /**
     * Authorize that the tenant belongs to the current landlord.
     */
    private function authorizeTenant(Tenant $tenant)
    {
        $property = Property::where('id', $tenant->property_id)
            ->where('landlord_id', Auth::id())
            ->first();

        abort_if(!$property, 403, 'Unauthorized access to this tenant.');
    }
}