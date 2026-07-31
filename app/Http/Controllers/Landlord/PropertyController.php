<?php
// app/Http/Controllers/Landlord/PropertyController.php

namespace App\Http\Controllers\Landlord;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\Property;
use App\Models\Tenant;
use App\Models\Payment;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

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
            // Validate - address and description are now optional
            $data = $request->validate([
                'name' => 'required|string|max:255',
                'address' => 'nullable|string|max:255',
                'description' => 'nullable|string',
                'monthly_rent' => 'required|numeric|min:0',
                'max_tenants' => 'required|integer|min:1',
            ]);

            // Set default values for nullable fields if not provided
            $data['address'] = $data['address'] ?? '';
            $data['description'] = $data['description'] ?? '';
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
        
        // Start with base query for tenants
        $query = Tenant::where('property_id', $property->id);

        $search = $request->search;
        

        if ($request->filled('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('tenant_code', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }
        
        // Apply filters based on payment status and month
        if ($paymentStatus !== 'all' && $month) {
            // PAID: Tenants who have a payment record for the selected month
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
            } 
            // UNPAID: Tenants who DO NOT have a payment record for the selected month
            elseif ($paymentStatus === 'unpaid') {
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
        } elseif ($paymentStatus !== 'all' && !$month) {
            // If no month selected, use current month
            $currentMonth = date('Y-m');
            
            if ($paymentStatus === 'paid') {
                $query->whereHas('payments', function ($q) use ($currentMonth) {
                    $q->where('status', 'Approved')
                      ->where(function ($subQuery) use ($currentMonth) {
                          $subQuery->where('payment_month', 'LIKE', $currentMonth . ',%')
                                   ->orWhere('payment_month', 'LIKE', '%,' . $currentMonth . ',%')
                                   ->orWhere('payment_month', 'LIKE', '%,' . $currentMonth)
                                   ->orWhere('payment_month', '=', $currentMonth);
                      });
                });
            } elseif ($paymentStatus === 'unpaid') {
                $query->whereDoesntHave('payments', function ($q) use ($currentMonth) {
                    $q->where('status', 'Approved')
                      ->where(function ($subQuery) use ($currentMonth) {
                          $subQuery->where('payment_month', 'LIKE', $currentMonth . ',%')
                                   ->orWhere('payment_month', 'LIKE', '%,' . $currentMonth . ',%')
                                   ->orWhere('payment_month', 'LIKE', '%,' . $currentMonth)
                                   ->orWhere('payment_month', '=', $currentMonth);
                      });
                });
            }
        } elseif ($paymentStatus === 'all' && $month) {
            // Month only filter - show tenants who have paid for this month
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
        
        // Get tenants with their payments - with pagination
        $tenants = $query->with(['payments' => function ($q) {
            $q->where('status', 'Approved')->orderBy('created_at', 'desc');
        }])->paginate(25);
        
        // Generate month options for dropdown - Show 2026 and 2027 months
        $months = [];
        $startDate = Carbon::create(2026, 1, 1);
        $endDate = Carbon::create(2027, 12, 1);
        
        while ($startDate <= $endDate) {
            $months[$startDate->format('Y-m')] = $startDate->format('F Y');
            $startDate->addMonth();
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
        
        // Start with base query for tenants
        $query = Tenant::where('property_id', $property->id);
        
        // Apply filters based on payment status and month
        if ($paymentStatus !== 'all' && $month) {
            // PAID: Tenants who have a payment record for the selected month
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
            } 
            // UNPAID: Tenants who DO NOT have a payment record for the selected month
            elseif ($paymentStatus === 'unpaid') {
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
        } elseif ($paymentStatus !== 'all' && !$month) {
            // If no month selected, use current month
            $currentMonth = date('Y-m');
            
            if ($paymentStatus === 'paid') {
                $query->whereHas('payments', function ($q) use ($currentMonth) {
                    $q->where('status', 'Approved')
                      ->where(function ($subQuery) use ($currentMonth) {
                          $subQuery->where('payment_month', 'LIKE', $currentMonth . ',%')
                                   ->orWhere('payment_month', 'LIKE', '%,' . $currentMonth . ',%')
                                   ->orWhere('payment_month', 'LIKE', '%,' . $currentMonth)
                                   ->orWhere('payment_month', '=', $currentMonth);
                      });
                });
            } elseif ($paymentStatus === 'unpaid') {
                $query->whereDoesntHave('payments', function ($q) use ($currentMonth) {
                    $q->where('status', 'Approved')
                      ->where(function ($subQuery) use ($currentMonth) {
                          $subQuery->where('payment_month', 'LIKE', $currentMonth . ',%')
                                   ->orWhere('payment_month', 'LIKE', '%,' . $currentMonth . ',%')
                                   ->orWhere('payment_month', 'LIKE', '%,' . $currentMonth)
                                   ->orWhere('payment_month', '=', $currentMonth);
                      });
                });
            }
        } elseif ($paymentStatus === 'all' && $month) {
            // Month only filter - show tenants who have paid for this month
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
        
        // Get tenants with their payments
        $tenants = $query->with(['payments' => function ($q) {
            $q->where('status', 'Approved');
        }])->get();
        
        // Generate PDF
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

            // Set default values for nullable fields if not provided
            $data['address'] = $data['address'] ?? '';
            $data['description'] = $data['description'] ?? '';

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