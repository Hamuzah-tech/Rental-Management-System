<?php
// app/Http/Controllers/Admin/TenantController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;

class TenantController extends Controller
{
    /**
     * Display a listing of tenants.
     */
    public function index(Request $request)
    {
        try {
            // Get all landlords (users with role 'landlord')
            $landlords = User::where('role', 'landlord')
                ->orderBy('name')
                ->get();

            // Start query for tenants with eager loading
            $query = Tenant::with(['property', 'property.landlord']);

            // Filter by landlord if selected
            if ($request->filled('landlord')) {
                $query->whereHas('property', function($q) use ($request) {
                    $q->where('landlord_id', $request->landlord);
                });
            }

            // Apply search filter if provided
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('phone', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('tenant_code', 'like', "%{$search}%")
                      ->orWhereHas('property', function($subQuery) use ($search) {
                          $subQuery->where('name', 'like', "%{$search}%");
                      });
                });
            }

            // Get paginated results
            $tenants = $query->latest()->paginate(20);

            return view('admin.tenants.index', compact('tenants', 'landlords'));

        } catch (\Exception $e) {
            Log::error('Failed to load tenants list', [
                'error' => $e->getMessage(),
                'admin_id' => auth()->id()
            ]);

            return back()->withErrors(['error' => 'Failed to load tenants. Please try again.']);
        }
    }

    /**
     * Export tenants to PDF
     */
    public function export(Request $request)
    {
        try {
            // Start query for tenants with eager loading
            $query = Tenant::with(['property', 'property.landlord']);

            // Apply the same filters as the index page
            if ($request->filled('landlord')) {
                // Only allow landlords to be selected
                $landlord = User::where('role', 'landlord')
                    ->whereKey($request->landlord)
                    ->first();

                if (!$landlord) {
                    return back()->withErrors(['error' => 'Invalid landlord selected.']);
                }

                $query->whereHas('property', function($q) use ($request) {
                    $q->where('landlord_id', $request->landlord);
                });
            }

            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('phone', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('tenant_code', 'like', "%{$search}%")
                      ->orWhereHas('property', function($subQuery) use ($search) {
                          $subQuery->where('name', 'like', "%{$search}%");
                      });
                });
            }

            // Get all tenants (no pagination for export)
            $tenants = $query->latest()->get();

            // Get filter info for the report
            $filters = [
                'landlord' => null,
                'search' => $request->filled('search') ? $request->search : null,
            ];

            if ($request->filled('landlord')) {
                $filters['landlord'] = User::where('role', 'landlord')
                    ->whereKey($request->landlord)
                    ->first();
            }

            // Generate PDF
            $pdf = Pdf::loadView('admin.tenants.export', compact('tenants', 'filters'));
            $pdf->setPaper('A4', 'landscape');

            // Log the export
            Log::info('Tenant report exported', [
                'admin_id' => auth()->id(),
                'tenant_count' => $tenants->count(),
                'filters' => [
                    'landlord_id' => $request->landlord ?? null,
                    'search' => $request->search ?? null
                ]
            ]);

            // Return the PDF for download
            return $pdf->download('tenant-report-' . date('Y-m-d') . '.pdf');

        } catch (\Exception $e) {
            Log::error('Tenant PDF export failed', [
                'admin_id' => auth()->id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()->withErrors([
                'error' => 'Unable to generate PDF. Please try again.'
            ]);
        }
    }

    /**
     * Show trashed (soft deleted) tenants.
     */
    public function trashed()
    {
        try {
            $tenants = Tenant::onlyTrashed()
                ->with(['property', 'property.landlord'])
                ->latest('deleted_at')
                ->paginate(20);

            return view('admin.trash.tenants', compact('tenants'));

        } catch (\Exception $e) {
            Log::error('Failed to load trashed tenants', [
                'error' => $e->getMessage(),
                'admin_id' => auth()->id()
            ]);

            return back()->withErrors(['error' => 'Failed to load archived tenants. Please try again.']);
        }
    }

    /**
     * Restore a soft deleted tenant.
     */
    public function restore($id)
    {
        try {
            $tenant = Tenant::onlyTrashed()
                ->where('id', $id)
                ->firstOrFail();
            
            $tenant->restore();

            Log::info('Tenant restored by admin', [
                'tenant_id' => $tenant->id,
                'tenant_name' => $tenant->name,
                'admin_id' => auth()->id()
            ]);

            return redirect()
                ->route('admin.trash.tenants')
                ->with('success', 'Tenant restored successfully.');

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::warning('Attempted to restore non-existent or non-trashed tenant', [
                'tenant_id' => $id,
                'admin_id' => auth()->id()
            ]);

            return redirect()
                ->route('admin.trash.tenants')
                ->withErrors(['error' => 'Tenant not found in archive.']);
        } catch (\Exception $e) {
            Log::error('Failed to restore tenant', [
                'tenant_id' => $id,
                'error' => $e->getMessage(),
                'admin_id' => auth()->id()
            ]);

            return redirect()
                ->route('admin.trash.tenants')
                ->withErrors(['error' => 'Failed to restore tenant. Please try again.']);
        }
    }

    /**
     * Permanently delete a tenant (Admin only).
     */
    public function forceDelete($id)
    {
        try {
            $tenant = Tenant::onlyTrashed()
                ->where('id', $id)
                ->firstOrFail();
            
            // Permanently remove all payment records belonging to this tenant
            $tenant->payments()->delete();
            
            // Permanently delete the tenant
            $tenant->forceDelete();

            Log::info('Tenant permanently deleted by admin', [
                'tenant_id' => $tenant->id,
                'tenant_name' => $tenant->name,
                'admin_id' => auth()->id()
            ]);

            return redirect()
                ->route('admin.trash.tenants')
                ->with('success', 'Tenant permanently deleted.');

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::warning('Attempted to permanently delete non-existent or non-trashed tenant', [
                'tenant_id' => $id,
                'admin_id' => auth()->id()
            ]);

            return redirect()
                ->route('admin.trash.tenants')
                ->withErrors(['error' => 'Tenant not found in archive.']);
        } catch (\Exception $e) {
            Log::error('Failed to permanently delete tenant', [
                'tenant_id' => $id,
                'error' => $e->getMessage(),
                'admin_id' => auth()->id()
            ]);

            return redirect()
                ->route('admin.trash.tenants')
                ->withErrors(['error' => 'Failed to permanently delete tenant. Please try again.']);
        }
    }

    /**
     * Soft delete the specified tenant.
     */
    public function destroy(Tenant $tenant)
    {
        try {
            $tenant->delete();

            Log::info('Tenant soft deleted by admin', [
                'tenant_id' => $tenant->id,
                'tenant_name' => $tenant->name,
                'admin_id' => auth()->id()
            ]);

            return redirect()
                ->route('admin.tenants.index')
                ->with('success', 'Tenant moved to archive.');

        } catch (\Exception $e) {
            Log::error('Failed to delete tenant', [
                'tenant_id' => $tenant->id,
                'error' => $e->getMessage(),
                'admin_id' => auth()->id()
            ]);

            return back()->withErrors(['error' => 'Failed to delete tenant. Please try again.']);
        }
    }
}