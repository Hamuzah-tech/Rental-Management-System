<?php
// app/Http/Controllers/Admin/TenantController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\User;
use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;
use Spatie\Permission\Models\Role;
use Barryvdh\DomPDF\Facade\Pdf;

class TenantController extends Controller
{
    /**
     * Display a listing of tenants.
     */
    public function index(Request $request)
    {
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
    }

    /**
     * Export tenants to PDF
     */
    public function export(Request $request)
    {
        // Start query for tenants with eager loading
        $query = Tenant::with(['property', 'property.landlord']);

        // Apply the same filters as the index page
        if ($request->filled('landlord')) {
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
            'landlord' => $request->filled('landlord') ? User::find($request->landlord) : null,
            'search' => $request->filled('search') ? $request->search : null,
        ];

        // Generate PDF
        $pdf = Pdf::loadView('admin.tenants.export', compact('tenants', 'filters'));
        $pdf->setPaper('A4', 'landscape');

        // Return the PDF for download
        return $pdf->download('tenant-report-' . date('Y-m-d') . '.pdf');
    }

    /**
     * Show trashed (soft deleted) tenants.
     */
    public function trashed()
    {
        $tenants = Tenant::onlyTrashed()
            ->with(['property', 'property.landlord'])
            ->latest('deleted_at')
            ->paginate(20);

        return view('admin.trash.tenants', compact('tenants'));
    }

    /**
     * Restore a soft deleted tenant.
     */
    public function restore($id)
    {
        $tenant = Tenant::onlyTrashed()->findOrFail($id);
        $tenant->restore();

        Log::info('Tenant restored by admin', [
            'tenant_id' => $tenant->id,
            'admin_id' => auth()->id()
        ]);

        return redirect()
            ->route('admin.trash.tenants')
            ->with('success', 'Tenant restored successfully.');
    }

    /**
     * Permanently delete a tenant (Admin only).
     */
    public function forceDelete($id)
    {
        $tenant = Tenant::onlyTrashed()->findOrFail($id);
        
        // Delete related payments first
        $tenant->payments()->delete();
        
        $tenant->forceDelete();

        Log::info('Tenant permanently deleted by admin', [
            'tenant_id' => $tenant->id,
            'admin_id' => auth()->id()
        ]);

        return redirect()
            ->route('admin.trash.tenants')
            ->with('success', 'Tenant permanently deleted.');
    }

    /**
     * Soft delete the specified tenant.
     */
    public function destroy(Tenant $tenant)
    {
        $tenant->delete();

        Log::info('Tenant soft deleted by admin', [
            'tenant_id' => $tenant->id,
            'admin_id' => auth()->id()
        ]);

        return redirect()
            ->route('admin.tenants.index')
            ->with('success', 'Tenant moved to archive.');
    }

    /**
     * Show the form for editing the specified tenant.
     */
    public function edit(Tenant $tenant)
    {
        // This is a placeholder - implement as needed
        return view('admin.tenants.edit', compact('tenant'));
    }

    /**
     * Update the specified tenant in storage.
     */
    public function update(Request $request, Tenant $tenant)
    {
        // This is a placeholder - implement as needed
        // For now, just redirect back
        return redirect()->route('admin.tenants.index')
            ->with('success', 'Tenant updated successfully.');
    }
}