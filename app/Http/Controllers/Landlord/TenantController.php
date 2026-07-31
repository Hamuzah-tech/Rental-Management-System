<?php
// app/Http/Controllers/Landlord/TenantController.php

namespace App\Http\Controllers\Landlord;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\Tenant;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
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

        // Filter by payment status (paid/unpaid) for specific month/year
        if ($request->filled('payment_status') && $request->payment_status !== 'all') {
            $paymentStatus = $request->payment_status;
            $month = $request->filled('month') ? $request->month : date('Y-m');
            
            if ($paymentStatus === 'paid') {
                $query->whereHas('payments', function ($q) use ($month) {
                    $q->where('status', 'Approved')
                      ->where(function ($subQuery) use ($month) {
                          $subQuery->where('payment_month', 'LIKE', $month . ',%')
                                   ->orWhere('payment_month', 'LIKE', '%,' . $month . ',%')
                                   ->orWhere('payment_month', 'LIKE', '%,' . $month)
                                   ->orWhere('payment_month', '=', $month);
                      });
                });
            } elseif ($paymentStatus === 'unpaid') {
                $query->whereDoesntHave('payments', function ($q) use ($month) {
                    $q->where('status', 'Approved')
                      ->where(function ($subQuery) use ($month) {
                          $subQuery->where('payment_month', 'LIKE', $month . ',%')
                                   ->orWhere('payment_month', 'LIKE', '%,' . $month . ',%')
                                   ->orWhere('payment_month', 'LIKE', '%,' . $month)
                                   ->orWhere('payment_month', '=', $month);
                      });
                });
            }
        } else {
            if ($request->filled('month')) {
                $month = $request->month;
                $query->whereHas('payments', function ($q) use ($month) {
                    $q->where('status', 'Approved')
                      ->where(function ($subQuery) use ($month) {
                          $subQuery->where('payment_month', 'LIKE', $month . ',%')
                                   ->orWhere('payment_month', 'LIKE', '%,' . $month . ',%')
                                   ->orWhere('payment_month', 'LIKE', '%,' . $month)
                                   ->orWhere('payment_month', '=', $month);
                      });
                });
            }
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
     * Display the specified property with its tenants.
     */
    public function showProperty(Request $request, Property $property)
    {
        if ($property->landlord_id !== Auth::id()) {
            abort(403);
        }

        $query = Tenant::where('property_id', $property->id);

        // Search by name, email, phone, or tenant_code
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%")
                  ->orWhere('phone', 'LIKE', "%{$search}%")
                  ->orWhere('tenant_code', 'LIKE', "%{$search}%");
            });
        }

        // Filter by payment status (paid/unpaid) for specific month/year
        if ($request->filled('payment_status') && $request->payment_status !== 'all') {
            $paymentStatus = $request->payment_status;
            $month = $request->filled('month') ? $request->month : date('Y-m');
            
            if ($paymentStatus === 'paid') {
                $query->whereHas('payments', function ($q) use ($month) {
                    $q->where('status', 'Approved')
                      ->where(function ($subQuery) use ($month) {
                          $subQuery->where('payment_month', 'LIKE', $month . ',%')
                                   ->orWhere('payment_month', 'LIKE', '%,' . $month . ',%')
                                   ->orWhere('payment_month', 'LIKE', '%,' . $month)
                                   ->orWhere('payment_month', '=', $month);
                      });
                });
            } elseif ($paymentStatus === 'unpaid') {
                $query->whereDoesntHave('payments', function ($q) use ($month) {
                    $q->where('status', 'Approved')
                      ->where(function ($subQuery) use ($month) {
                          $subQuery->where('payment_month', 'LIKE', $month . ',%')
                                   ->orWhere('payment_month', 'LIKE', '%,' . $month . ',%')
                                   ->orWhere('payment_month', 'LIKE', '%,' . $month)
                                   ->orWhere('payment_month', '=', $month);
                      });
                });
            }
        } else {
            if ($request->filled('month')) {
                $month = $request->month;
                $query->whereHas('payments', function ($q) use ($month) {
                    $q->where('status', 'Approved')
                      ->where(function ($subQuery) use ($month) {
                          $subQuery->where('payment_month', 'LIKE', $month . ',%')
                                   ->orWhere('payment_month', 'LIKE', '%,' . $month . ',%')
                                   ->orWhere('payment_month', 'LIKE', '%,' . $month)
                                   ->orWhere('payment_month', '=', $month);
                      });
                });
            }
        }

        // Eager load payments and get results
        $tenants = $query->with(['payments' => function ($q) {
            $q->where('status', 'Approved')->orderBy('created_at', 'desc');
        }])->get();

        // Generate months for dropdown (last 12 months)
        $months = [];
        for ($i = 0; $i < 12; $i++) {
            $date = now()->subMonths($i);
            $months[$date->format('Y-m')] = $date->format('F Y');
        }

        return view('landlord.properties.show', compact('property', 'tenants', 'months'));
    }

    /**
     * Export property tenants to PDF.
     */
    public function exportPropertyPdf(Request $request, Property $property)
    {
        if ($property->landlord_id !== Auth::id()) {
            abort(403);
        }

        $paymentStatus = $request->payment_status ?? 'all';
        $month = $request->month ?? null;
        $search = $request->search ?? null;

        $query = Tenant::where('property_id', $property->id);

        // Search filter
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%")
                  ->orWhere('phone', 'LIKE', "%{$search}%")
                  ->orWhere('tenant_code', 'LIKE', "%{$search}%");
            });
        }

        // Filter by payment status (paid/unpaid) for specific month/year
        if ($paymentStatus !== 'all') {
            if ($paymentStatus === 'paid') {
                $query->whereHas('payments', function ($q) use ($month) {
                    $q->where('status', 'Approved')
                      ->where(function ($subQuery) use ($month) {
                          $subQuery->where('payment_month', 'LIKE', $month . ',%')
                                   ->orWhere('payment_month', 'LIKE', '%,' . $month . ',%')
                                   ->orWhere('payment_month', 'LIKE', '%,' . $month)
                                   ->orWhere('payment_month', '=', $month);
                      });
                });
            } elseif ($paymentStatus === 'unpaid') {
                $query->whereDoesntHave('payments', function ($q) use ($month) {
                    $q->where('status', 'Approved')
                      ->where(function ($subQuery) use ($month) {
                          $subQuery->where('payment_month', 'LIKE', $month . ',%')
                                   ->orWhere('payment_month', 'LIKE', '%,' . $month . ',%')
                                   ->orWhere('payment_month', 'LIKE', '%,' . $month)
                                   ->orWhere('payment_month', '=', $month);
                      });
                });
            }
        } else {
            if ($month) {
                $query->whereHas('payments', function ($q) use ($month) {
                    $q->where('status', 'Approved')
                      ->where(function ($subQuery) use ($month) {
                          $subQuery->where('payment_month', 'LIKE', $month . ',%')
                                   ->orWhere('payment_month', 'LIKE', '%,' . $month . ',%')
                                   ->orWhere('payment_month', 'LIKE', '%,' . $month)
                                   ->orWhere('payment_month', '=', $month);
                      });
                });
            }
        }

        $tenants = $query->with(['payments' => function ($q) {
            $q->where('status', 'Approved');
        }])->get();

        $pdf = Pdf::loadView('exports.property-tenants-pdf', [
            'property' => $property,
            'tenants' => $tenants,
            'paymentStatus' => $paymentStatus,
            'month' => $month,
            'search' => $search,
            'landlord' => Auth::user(),
            'generatedAt' => now()
        ]);

        $pdf->setPaper('A4', 'landscape');
        $filename = 'property_tenants_' . $property->id . '_' . date('Y-m-d_H-i-s') . '.pdf';
        return $pdf->download($filename);
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
     * Store a newly created tenant in storage - FIXED to match self-registration behavior
     */
    public function store(Request $request)
    {
        // Validate the request with custom messages (matching self-registration)
        $validated = $request->validate([
            'property_id' => 'required|exists:properties,id',
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'move_in_date' => 'required|date|after_or_equal:today',
            'status' => 'sometimes|boolean',
        ], [
            'property_id.required' => 'Please select a property.',
            'property_id.exists' => 'The selected property is invalid.',
            'name.required' => 'Please enter the tenant\'s full name.',
            'name.max' => 'Name cannot exceed 255 characters.',
            'email.required' => 'Please enter the tenant\'s email address.',
            'email.email' => 'Please enter a valid email address.',
            'email.max' => 'Email cannot exceed 255 characters.',
            'phone.required' => 'Please enter the tenant\'s phone number.',
            'phone.max' => 'Phone number cannot exceed 20 characters.',
            'move_in_date.required' => 'Please select a move-in date.',
            'move_in_date.after_or_equal' => 'Move-in date must be today or a future date.',
        ]);

        // Check phone format BEFORE checking duplicate
        if (!Tenant::isValidMalawiPhone($validated['phone'])) {
            return back()
                ->withInput()
                ->withErrors(['phone' => 'Please enter a valid Malawi phone number.']);
        }

        // Normalize phone for duplicate check
        $normalizedPhone = Tenant::normalizePhoneNumber($validated['phone']);

        // Check for duplicate phone in the same property
        $existingTenant = Tenant::where('phone', $normalizedPhone)
            ->where('property_id', $validated['property_id'])
            ->whereNull('deleted_at')
            ->exists();

        if ($existingTenant) {
            return back()
                ->withInput()
                ->withErrors(['phone' => 'A tenant with this phone number already exists in this property.']);
        }

        try {
            DB::beginTransaction();

            // Check if property belongs to this landlord
            $property = Property::where('id', $validated['property_id'])
                ->where('landlord_id', Auth::id())
                ->firstOrFail();

            // Check if property is full
            if ($property->isFull()) {
    return back()
        ->withInput()
        ->with('error', "The selected hostel ({$property->name}) is already full. No more tenants can be added.")
        ->withErrors([
            'property_id' => 'This hostel has reached its maximum capacity.'
        ]);
}
            // Generate tenant code
            $tenantData = [
                'tenant_code' => 'TEN-' . strtoupper(Str::random(8)),
                'property_id' => $property->id,
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $normalizedPhone,
                'move_in_date' => $validated['move_in_date'],
                'status' => 'active',
                'monthly_rent' => $property->monthly_rent ?? 0,
            ];

            $tenant = Tenant::create($tenantData);

            DB::commit();

            Log::info('Tenant created successfully by landlord', [
                'tenant_id' => $tenant->id,
                'property_id' => $property->id,
                'phone' => $tenant->phone,
                'landlord_id' => Auth::id()
            ]);

            return redirect()
                ->route('landlord.properties.show', $property->id)
                ->with('success', 'Tenant created successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Error creating tenant: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->except(['_token'])
            ]);
            
            return back()
                ->withInput()
                ->withErrors(['error' => 'Failed to create tenant. Please try again.']);
        }
    }

    /**
     * Display the specified tenant.
     */
    public function show(Request $request, Tenant $tenant)
    {
        $this->authorizeTenant($tenant);
        
        // Start query for payments
        $query = $tenant->payments();
        
        // Search by payment month or amount
        if ($request->filled('search_payment')) {
            $search = $request->search_payment;
            $query->where(function($q) use ($search) {
                $q->where('payment_month', 'LIKE', "%{$search}%")
                  ->orWhere('amount', 'LIKE', "%{$search}%")
                  ->orWhere('status', 'LIKE', "%{$search}%");
            });
        }
        
        // Get payments with pagination
        $payments = $query->latest()->paginate(10);
        
        $tenant->load('property');

        return view('landlord.tenants.show', compact('tenant', 'payments'));
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

        // Validate the request
        $validated = $request->validate([
            'property_id' => 'required|exists:properties,id',
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'monthly_rent' => 'nullable|numeric|min:0',
            'move_in_date' => 'required|date',
            'status' => 'required|in:active,inactive,moved_out',
        ]);

        try {
            DB::beginTransaction();

            // Check if property belongs to this landlord
            $property = Property::where('id', $validated['property_id'])
                ->where('landlord_id', Auth::id())
                ->firstOrFail();

            // Validate phone number format (Malawi)
            if (!Tenant::isValidMalawiPhone($validated['phone'])) {
                return back()
                    ->withInput()
                    ->withErrors(['phone' => 'Please enter a valid Malawi phone number.']);
            }

            // Check for duplicate phone in the same property (excluding current tenant)
            $normalizedPhone = Tenant::normalizePhoneNumber($validated['phone']);
            $existingTenant = Tenant::where('phone', $normalizedPhone)
                ->where('property_id', $property->id)
                ->where('id', '!=', $tenant->id)
                ->whereNull('deleted_at')
                ->exists();

            if ($existingTenant) {
                return back()
                    ->withInput()
                    ->withErrors(['phone' => 'A tenant with this phone number already exists in this property.']);
            }

            // If monthly_rent is not provided, use property's default rent
            if (empty($validated['monthly_rent'])) {
                $validated['monthly_rent'] = $property->monthly_rent ?? 0;
            }

            // Update the tenant
            $tenant->update([
                'property_id' => $validated['property_id'],
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $normalizedPhone,
                'monthly_rent' => $validated['monthly_rent'],
                'move_in_date' => $validated['move_in_date'],
                'status' => $validated['status'],
            ]);

            DB::commit();

            Log::info('Tenant updated successfully', [
                'tenant_id' => $tenant->id,
                'property_id' => $property->id,
                'phone' => $tenant->phone
            ]);

            return redirect()
                ->route('landlord.properties.show', $property->id)
                ->with('success', 'Tenant updated successfully!');

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Error updating tenant: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'tenant_id' => $tenant->id,
                'request_data' => $request->except(['_token'])
            ]);
            
            return back()
                ->withInput()
                ->withErrors(['error' => 'Failed to update tenant. Please try again.']);
        }
    }

    /**
     * Soft delete the specified tenant (move to archive).
     */
    public function destroy(Tenant $tenant)
    {
        $this->authorizeTenant($tenant);
        
        $propertyId = $tenant->property_id;
        $tenant->delete();

        Log::info('Tenant soft deleted', ['id' => $tenant->id]);

        return redirect()
            ->route('landlord.properties.show', $propertyId)
            ->with('success', 'Tenant moved to archive.');
    }

    /**
     * Restore a soft deleted tenant.
     */
    public function restore($id)
    {
        $tenant = Tenant::onlyTrashed()->findOrFail($id);
        $this->authorizeTenant($tenant);
        
        $propertyId = $tenant->property_id;
        $tenant->restore();

        Log::info('Tenant restored', ['id' => $tenant->id]);

        return redirect()
            ->route('landlord.properties.show', $propertyId)
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
            Log::error('Error generating registration link: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate registration link. Please try again.'
            ], 500);
        }
    }

    /**
     * Move out a tenant.
     */
    public function moveOut(Tenant $tenant)
    {
        $this->authorizeTenant($tenant);

        $propertyId = $tenant->property_id;

        $tenant->update([
            'status' => 'moved_out',
            'move_out_date' => now(),
        ]);

        $tenant->delete();

        Log::info('Tenant moved out', ['id' => $tenant->id]);

        return redirect()
            ->route('landlord.properties.show', $propertyId)
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

        return redirect()
            ->route('landlord.properties.show', $tenant->property_id)
            ->with('success', 'Tenant reactivated successfully.');
    }

    /**
     * Export tenants to PDF.
     */
    public function exportPdf(Request $request)
    {
        $paymentStatus = $request->payment_status ?? 'all';
        $month = $request->month ?? null;
        $search = $request->search ?? null;

        $query = Tenant::whereHas('property', function ($q) {
            $q->where('landlord_id', Auth::id());
        });

        // Search filter
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%")
                  ->orWhere('phone', 'LIKE', "%{$search}%")
                  ->orWhere('tenant_code', 'LIKE', "%{$search}%");
            });
        }

        // Filter by payment status (paid/unpaid) for specific month/year
        if ($paymentStatus !== 'all') {
            if ($paymentStatus === 'paid') {
                $query->whereHas('payments', function ($q) use ($month) {
                    $q->where('status', 'Approved')
                      ->where(function ($subQuery) use ($month) {
                          $subQuery->where('payment_month', 'LIKE', $month . ',%')
                                   ->orWhere('payment_month', 'LIKE', '%,' . $month . ',%')
                                   ->orWhere('payment_month', 'LIKE', '%,' . $month)
                                   ->orWhere('payment_month', '=', $month);
                      });
                });
            } elseif ($paymentStatus === 'unpaid') {
                $query->whereDoesntHave('payments', function ($q) use ($month) {
                    $q->where('status', 'Approved')
                      ->where(function ($subQuery) use ($month) {
                          $subQuery->where('payment_month', 'LIKE', $month . ',%')
                                   ->orWhere('payment_month', 'LIKE', '%,' . $month . ',%')
                                   ->orWhere('payment_month', 'LIKE', '%,' . $month)
                                   ->orWhere('payment_month', '=', $month);
                      });
                });
            }
        } else {
            if ($month) {
                $query->whereHas('payments', function ($q) use ($month) {
                    $q->where('status', 'Approved')
                      ->where(function ($subQuery) use ($month) {
                          $subQuery->where('payment_month', 'LIKE', $month . ',%')
                                   ->orWhere('payment_month', 'LIKE', '%,' . $month . ',%')
                                   ->orWhere('payment_month', 'LIKE', '%,' . $month)
                                   ->orWhere('payment_month', '=', $month);
                      });
                });
            }
        }

        $tenants = $query->with(['property', 'payments' => function ($q) {
            $q->where('status', 'Approved');
        }])->get();

        $pdf = Pdf::loadView('exports.tenants-pdf', [
            'tenants' => $tenants,
            'paymentStatus' => $paymentStatus,
            'month' => $month,
            'search' => $search,
            'landlord' => Auth::user(),
            'generatedAt' => now()
        ]);

        $pdf->setPaper('A4', 'landscape');
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