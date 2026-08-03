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
            $q->where('landlord_id', Auth::guard('landlord')->id());
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
        $properties = Property::where('landlord_id', Auth::guard('landlord')->id())->get();

        return view('landlord.tenants.index', compact('tenants', 'properties'));
    }

    /**
     * Display the specified property with its tenants.
     */
    public function showProperty(Request $request, Property $property)
    {
        if ($property->landlord_id !== Auth::guard('landlord')->id()) {
            abort(403, 'You are not authorized to access this property.');
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
        if ($property->landlord_id !== Auth::guard('landlord')->id()) {
            abort(403, 'You are not authorized to export this property.');
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
            'landlord' => Auth::guard('landlord')->user(),
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
            $q->where('landlord_id', Auth::guard('landlord')->id());
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
        $properties = Property::where('landlord_id', Auth::guard('landlord')->id())
            ->where('status', true)
            ->get();

        return view('landlord.tenants.create', compact('properties'));
    }

    /**
     * Store a newly created tenant in storage - IMPROVED with secure tenant code generation.
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
                ->where('landlord_id', Auth::guard('landlord')->id())
                ->firstOrFail();

            // Check if property is full - IMPROVED to retain form data
            if ($property->isFull()) {
                DB::rollBack();
                
                // Get property details for the error message
                $propertyName = $property->name;
                $capacity = $property->capacity;
                $currentCount = $property->tenants()->whereNull('deleted_at')->count();
                
                // Return with all input data preserved and a clear error message
                return back()
                    ->withInput()
                    ->withErrors([
                        'property_id' => "This hostel ({$propertyName}) is full. No additional tenants can be added because the hostel has reached its maximum capacity of {$capacity} tenants. Please select another hostel or wait until a space becomes available."
                    ])
                    ->with('error', "This hostel is full. No additional tenants can be added because the hostel has reached its maximum capacity. Please select another hostel or wait until a space becomes available.");
            }

            // Generate tenant code using the secure generator (without prefix)
            // The boot method in the Tenant model will use generateUniqueTenantCode()
            $tenantData = [
                // 'tenant_code' is automatically generated in the model's boot method
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
                'tenant_code' => $tenant->tenant_code,
                'property_id' => $property->id,
                'phone' => $tenant->phone,
                'landlord_id' => Auth::guard('landlord')->id()
            ]);

            return redirect()
                ->route('landlord.properties.show', $property->id)
                ->with('success', 'Tenant created successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Error creating tenant', [
                'message' => $e->getMessage(),
                'property_id' => $validated['property_id'] ?? null,
                'landlord_id' => Auth::guard('landlord')->id()
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

        $properties = Property::where('landlord_id', Auth::guard('landlord')->id())
            ->where('status', true)
            ->get();

        return view('landlord.tenants.edit', compact('tenant', 'properties'));
    }

    /**
     * Update the specified tenant in storage.
     * IMPROVED: Removed status validation, sanitizes monthly_rent, preserves status from database.
     */
    public function update(Request $request, Tenant $tenant)
    {
        $this->authorizeTenant($tenant);

        // Sanitize monthly_rent BEFORE validation - remove commas and non-numeric characters
        if ($request->has('monthly_rent')) {
            $monthlyRent = $request->input('monthly_rent');
            // Remove all non-numeric characters (commas, spaces, currency symbols, letters)
            $cleanMonthlyRent = preg_replace('/[^0-9.]/', '', $monthlyRent);
            // Override the request input with cleaned value
            $request->merge(['monthly_rent' => $cleanMonthlyRent]);
        }

        // Validate the request - removed 'status' validation
        $validated = $request->validate([
            'property_id' => 'required|exists:properties,id',
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'monthly_rent' => 'nullable|numeric|min:0',
            'move_in_date' => 'required|date',
            // 'status' validation removed intentionally
        ]);

        try {
            DB::beginTransaction();

            // Check if property belongs to this landlord
            $property = Property::where('id', $validated['property_id'])
                ->where('landlord_id', Auth::guard('landlord')->id())
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

            // If monthly_rent is empty or null, use property's default rent
            if (empty($validated['monthly_rent']) && $validated['monthly_rent'] !== 0) {
                $numericMonthlyRent = $property->monthly_rent ?? 0;
            } else {
                $numericMonthlyRent = floatval($validated['monthly_rent']);
            }

            // Update the tenant - preserve existing status from database
            // Note: tenant_code is NOT updated - it remains the same
            $tenant->update([
                'property_id' => $validated['property_id'],
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $normalizedPhone,
                'monthly_rent' => $numericMonthlyRent,
                'move_in_date' => $validated['move_in_date'],
                // Status is NOT updated - we preserve the existing value from the database
                // This prevents the "status validation" error and is more secure
            ]);

            DB::commit();

            Log::info('Tenant updated successfully', [
                'tenant_id' => $tenant->id,
                'tenant_code' => $tenant->tenant_code,
                'property_id' => $property->id,
                'phone' => $tenant->phone,
                'landlord_id' => Auth::guard('landlord')->id()
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
            
            Log::error('Error updating tenant', [
                'message' => $e->getMessage(),
                'tenant_id' => $tenant->id,
                'property_id' => $validated['property_id'] ?? null,
                'landlord_id' => Auth::guard('landlord')->id()
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

        Log::info('Tenant soft deleted', [
            'id' => $tenant->id,
            'tenant_code' => $tenant->tenant_code,
            'landlord_id' => Auth::guard('landlord')->id()
        ]);

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

        Log::info('Tenant restored', [
            'id' => $tenant->id,
            'tenant_code' => $tenant->tenant_code,
            'landlord_id' => Auth::guard('landlord')->id()
        ]);

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
                ->where('landlord_id', Auth::guard('landlord')->id())
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
                'landlord_id' => Auth::guard('landlord')->id()
            ]);

            return response()->json([
                'success' => true,
                'link' => $registrationLink,
                'token' => $property->registration_token,
                'message' => 'Registration link generated successfully.'
            ]);

        } catch (\Exception $e) {
            Log::error('Error generating registration link', [
                'message' => $e->getMessage(),
                'property_id' => $request->property_id ?? null,
                'landlord_id' => Auth::guard('landlord')->id()
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

        Log::info('Tenant moved out', [
            'id' => $tenant->id,
            'tenant_code' => $tenant->tenant_code,
            'landlord_id' => Auth::guard('landlord')->id()
        ]);

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

        Log::info('Tenant reactivated', [
            'id' => $tenant->id,
            'tenant_code' => $tenant->tenant_code,
            'landlord_id' => Auth::guard('landlord')->id()
        ]);

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
            $q->where('landlord_id', Auth::guard('landlord')->id());
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
            'landlord' => Auth::guard('landlord')->user(),
            'generatedAt' => now()
        ]);

        $pdf->setPaper('A4', 'landscape');
        $filename = 'tenants_export_' . date('Y-m-d_H-i-s') . '.pdf';
        return $pdf->download($filename);
    }

    /**
     * Authorize that the tenant belongs to the current landlord.
     */
    private function authorizeTenant(Tenant $tenant)
    {
        $property = Property::where('id', $tenant->property_id)
            ->where('landlord_id', Auth::guard('landlord')->id())
            ->first();

        abort_if(!$property, 403, 'Unauthorized access to this tenant.');
    }
}