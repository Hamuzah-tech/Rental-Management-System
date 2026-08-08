<?php
// app/Http/Controllers/Landlord/PropertyController.php

namespace App\Http\Controllers\Landlord;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
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
        $properties = Property::where('landlord_id', Auth::guard('landlord')->id())
            ->latest()
            ->paginate(10);

        return view('landlord.properties.index', compact('properties'));
    }

    /**
     * Show trashed (soft deleted) properties
     */
    public function trashed()
    {
        $properties = Property::where('landlord_id', Auth::guard('landlord')->id())
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
            $data['landlord_id'] = Auth::guard('landlord')->id();
            $data['status'] = true;
            $data['registration_open'] = true;
            $data['registration_token'] = \Illuminate\Support\Str::random(40);

            $property = Property::create($data);

            Log::info('Property created successfully', [
                'id' => $property->id,
                'name' => $property->name,
                'landlord_id' => Auth::guard('landlord')->id()
            ]);

            return redirect()
                ->route('landlord.properties.index')
                ->with('success', 'Property created successfully.');

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation failed', ['errors' => $e->errors()]);
            return back()
                ->withErrors($e->errors())
                ->withInput();
                
        } catch (\Exception $e) {
            Log::error('Error creating property', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()
                ->withInput()
                ->withErrors(['error' => 'Failed to create property. Please try again.']);
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
        
        // Apply payment filters using the extracted method
        $this->applyPaymentFilters($query, $paymentStatus, $month);
        
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
        
        // Apply payment filters using the extracted method
        $this->applyPaymentFilters($query, $paymentStatus, $month);
        
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
            'landlord' => Auth::guard('landlord')->user(),
            'generatedAt' => now()
        ]);
        
        $pdf->setPaper('A4', 'landscape');
        
        // Use public_id or slug for better filename
        $filename = Str::slug($property->name) . '.pdf';
        // Alternative: $filename = 'property_tenants_' . $property->public_id . '_' . date('Y-m-d') . '.pdf';
        
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

            Log::info('Property updated successfully', [
                'id' => $property->id,
                'name' => $property->name,
                'landlord_id' => Auth::guard('landlord')->id()
            ]);

            return redirect()
                ->route('landlord.properties.index')
                ->with('success', 'Property updated successfully.');

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation failed during property update', ['errors' => $e->errors()]);
            return back()
                ->withErrors($e->errors())
                ->withInput();
                
        } catch (\Exception $e) {
            Log::error('Error updating property', [
                'message' => $e->getMessage(),
                'property_id' => $property->id,
                'trace' => $e->getTraceAsString()
            ]);
            return back()
                ->withInput()
                ->withErrors(['error' => 'Failed to update property. Please try again.']);
        }
    }

    /**
     * Toggle registration open/closed for the property.
     */
    public function toggleRegistration(Request $request, Property $property)
    {
        $this->authorizeProperty($property);

        try {
            $property->update([
                'registration_open' => !$property->registration_open
            ]);

            $status = $property->registration_open ? 'opened' : 'closed';

            Log::info('Property registration toggled', [
                'id' => $property->id,
                'name' => $property->name,
                'registration_open' => $property->registration_open,
                'landlord_id' => Auth::guard('landlord')->id()
            ]);

            return back()->with('success', "Registration {$status} successfully.");

        } catch (\Exception $e) {
            Log::error('Error toggling registration status', [
                'message' => $e->getMessage(),
                'property_id' => $property->id
            ]);
            return back()->withErrors(['error' => 'Failed to update registration status. Please try again.']);
        }
    }

    /**
     * Soft delete property (moves to archive)
     */
    public function destroy(Property $property)
    {
        $this->authorizeProperty($property);
        
        try {
            $property->delete();

            Log::info('Property soft deleted', [
                'id' => $property->id,
                'name' => $property->name,
                'landlord_id' => Auth::guard('landlord')->id()
            ]);

            return redirect()
                ->route('landlord.properties.index')
                ->with('success', 'Property moved to archive.');
                
        } catch (\Exception $e) {
            Log::error('Error deleting property', [
                'message' => $e->getMessage(),
                'property_id' => $property->id
            ]);
            return back()->withErrors(['error' => 'Failed to delete property. Please try again.']);
        }
    }

    /**
     * Restore a soft deleted property using public_id.
     */
    public function restore($public_id)
    {
        try {
            $property = Property::withTrashed()
                ->where('public_id', $public_id)
                ->firstOrFail();
            
            $this->authorizeProperty($property);
            
            $property->restore();

            Log::info('Property restored', [
                'id' => $property->id,
                'public_id' => $property->public_id,
                'name' => $property->name,
                'landlord_id' => Auth::guard('landlord')->id()
            ]);

            return redirect()
                ->route('landlord.properties.trashed')
                ->with('success', 'Property restored successfully.');
                
        } catch (\Exception $e) {
            Log::error('Error restoring property', [
                'message' => $e->getMessage(),
                'public_id' => $public_id
            ]);
            return back()->withErrors(['error' => 'Failed to restore property. Please try again.']);
        }
    }

    /**
     * Toggle property status (Active/Inactive).
     */
    public function toggleStatus(Property $property)
    {
        $this->authorizeProperty($property);

        try {
            $property->update([
                'status' => !$property->status
            ]);

            Log::info('Property status toggled', [
                'id' => $property->id,
                'new_status' => $property->status,
                'landlord_id' => Auth::guard('landlord')->id()
            ]);

            return back()->with('success', 'Property status updated.');
            
        } catch (\Exception $e) {
            Log::error('Error toggling property status', [
                'message' => $e->getMessage(),
                'property_id' => $property->id
            ]);
            return back()->withErrors(['error' => 'Failed to update property status. Please try again.']);
        }
    }

    /**
     * Authorize that the property belongs to the current landlord.
     */
    private function authorizeProperty(Property $property)
    {
        abort_if(
            $property->landlord_id !== Auth::guard('landlord')->id(),
            403,
            'You are not authorized to access this property.'
        );
    }

    /**
     * Apply payment filters to the tenant query.
     * This method extracts the duplicated payment filtering logic for maintainability.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $paymentStatus
     * @param string|null $month
     * @return void
     */
    private function applyPaymentFilters($query, string $paymentStatus, ?string $month): void
    {
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
    }
}