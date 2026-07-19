<?php

namespace App\Http\Controllers\Landlord;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\Tenant;
use App\Models\Property;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\TenantsExport;
use Barryvdh\DomPDF\Facade\Pdf;

class TenantController extends Controller
{

    /**
     * Display tenants belonging to logged landlord
     */
    public function index(Request $request)
    {
        $filter = $request->get('payment_status', 'all');

        $query = Tenant::with(['property', 'payments'])
            ->whereHas('property', function ($q) {
                $q->where('landlord_id', Auth::id());
            });

        if ($filter == 'paid') {
            $query->whereHas('payments', function ($q) {
                $q->where('status', 'Approved');
            });
        }

        if ($filter == 'unpaid') {
            $query->whereDoesntHave('payments', function ($q) {
                $q->where('status', 'Approved');
            });
        }

        $tenants = $query
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view(
            'landlord.tenants.index',
            compact(
                'tenants',
                'filter'
            )
        );
    }

    /**
     * Generate registration link via AJAX
     */
    public function generateRegistrationLink(Request $request)
    {
        try {
            // Validate the request
            $validated = $request->validate([
                'property_id' => 'required|exists:properties,id'
            ]);

            // Find the property and ensure it belongs to the authenticated landlord
            $property = Property::where('id', $validated['property_id'])
                ->where('landlord_id', Auth::id())
                ->first();

            if (!$property) {
                return response()->json([
                    'success' => false,
                    'message' => 'Property not found or you do not have permission.'
                ], 404);
            }

            // Ensure property has a registration token
            if (empty($property->registration_token)) {
                $property->registration_token = Str::random(40);
                $property->save();
            }

            // Generate the registration link
            $link = route('tenant.registration', ['token' => $property->registration_token]);

            return response()->json([
                'success' => true,
                'link' => $link,
                'token' => $property->registration_token
            ]);

        } catch (\Exception $e) {
            Log::error('Error generating registration link: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate registration link. Please try again.'
            ], 500);
        }
    }

    public function registrationLink(Property $property)
    {
        return view(
            'landlord.registration-link',
            compact('property')
        );
    }

    /**
     * Show create tenant page
     */
    public function create()
    {
        $properties = Property::where(
            'landlord_id',
            Auth::id()
        )
        ->get();

        return view(
            'landlord.tenants.create',
            compact('properties')
        );
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
                'string'
            ],
            'phone' => [
                'required',
                'string'
            ],
            'email' => [
                'nullable',
                'email'
            ],
            'monthly_rent' => [
                'required',
                'numeric'
            ],
            'move_in_date' => [
                'required',
                'date'
            ],
        ]);

        /*
        |--------------------------------------------------------------------------
        | Make sure landlord owns this property
        |--------------------------------------------------------------------------
        */
        Property::where('id', $data['property_id'])
            ->where('landlord_id', Auth::id())
            ->firstOrFail();

        /*
        |--------------------------------------------------------------------------
        | Generate permanent tenant code
        |--------------------------------------------------------------------------
        */
        do {
            $code = 'TNT-' . strtoupper(
                Str::random(6)
            );
        } while (
            Tenant::where(
                'tenant_code',
                $code
            )->exists()
        );

        $data['tenant_code'] = $code;
        $data['status'] = 'Active';

        Tenant::create($data);

        return redirect()
            ->route('landlord.tenants.index')
            ->with(
                'success',
                'Tenant added successfully. Code: ' . $code
            );
    }

    /**
     * Edit tenant
     */
    public function edit(Tenant $tenant)
    {
        $this->checkOwner($tenant);

        $properties = Property::where(
            'landlord_id',
            Auth::id()
        )
        ->get();

        return view(
            'landlord.tenants.edit',
            compact(
                'tenant',
                'properties'
            )
        );
    }

    /**
     * Update tenant
     */
    public function update(
        Request $request,
        Tenant $tenant
    ) {
        $this->checkOwner($tenant);

        $data = $request->validate([
            'property_id' => [
                'required',
                'exists:properties,id'
            ],
            'name' => 'required|string',
            'phone' => 'required|string',
            'email' => 'nullable|email',
            'monthly_rent' => 'required|numeric',
        ]);

        $tenant->update($data);

        return redirect()
            ->route('landlord.tenants.index')
            ->with(
                'success',
                'Tenant updated successfully.'
            );
    }

    /**
     * Delete tenant
     */
    public function destroy(Tenant $tenant)
    {
        $this->checkOwner($tenant);
        $tenant->delete();

        return back()
            ->with(
                'success',
                'Tenant deleted.'
            );
    }

    /**
     * Move out tenant
     */
    public function moveOut(Tenant $tenant)
    {
        $this->checkOwner($tenant);
        $tenant->update([
            'status' => 'Moved Out'
        ]);

        return back()
            ->with(
                'success',
                'Tenant moved out.'
            );
    }

    /**
     * Reactivate tenant
     */
    public function reactivate(Tenant $tenant)
    {
        $this->checkOwner($tenant);
        $tenant->update([
            'status' => 'Active'
        ]);

        return back()
            ->with(
                'success',
                'Tenant reactivated.'
            );
    }

    /**
     * Export tenants to Excel - Only tenants belonging to the logged-in landlord
     */
    public function exportExcel(Request $request)
    {
        try {
            $filter = $request->get('payment_status', 'all');

            $query = Tenant::with(['property', 'payments'])
                ->whereHas('property', function ($q) {
                    $q->where('landlord_id', Auth::id());
                });

            // Apply the same filter as the index page
            if ($filter == 'paid') {
                $query->whereHas('payments', function ($q) {
                    $q->where('status', 'Approved');
                });
            }

            if ($filter == 'unpaid') {
                $query->whereDoesntHave('payments', function ($q) {
                    $q->where('status', 'Approved');
                });
            }

            $tenants = $query->get();

            if ($tenants->isEmpty()) {
                return back()->with('error', 'No tenants found to export.');
            }

            return Excel::download(new TenantsExport($tenants), 'tenants_' . date('Y-m-d') . '.xlsx');
        } catch (\Exception $e) {
            Log::error('Excel Export Error: ' . $e->getMessage());
            return back()->with('error', 'Failed to export tenants to Excel. Please try again.');
        }
    }

    /**
     * Export tenants to PDF - Only tenants belonging to the logged-in landlord
     */
    public function exportPdf(Request $request)
    {
        try {
            $filter = $request->get('payment_status', 'all');

            $query = Tenant::with(['property', 'payments'])
                ->whereHas('property', function ($q) {
                    $q->where('landlord_id', Auth::id());
                });

            // Apply the same filter as the index page
            if ($filter == 'paid') {
                $query->whereHas('payments', function ($q) {
                    $q->where('status', 'Approved');
                });
            }

            if ($filter == 'unpaid') {
                $query->whereDoesntHave('payments', function ($q) {
                    $q->where('status', 'Approved');
                });
            }

            $tenants = $query->get();

            if ($tenants->isEmpty()) {
                return back()->with('error', 'No tenants found to export.');
            }

            $pdf = Pdf::loadView('exports.tenants-pdf', compact('tenants'));
            return $pdf->download('tenants_' . date('Y-m-d') . '.pdf');
        } catch (\Exception $e) {
            Log::error('PDF Export Error: ' . $e->getMessage());
            return back()->with('error', 'Failed to export tenants to PDF. Please try again.');
        }
    }

    /**
     * Security check
     */
    private function checkOwner(Tenant $tenant)
    {
        abort_unless(
            $tenant->property->landlord_id === Auth::id(),
            403
        );
    }
}