<?php
// app/Http/Controllers/Admin/TenantController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TenantController extends Controller
{
    /**
     * Display a listing of tenants.
     */
    public function index()
    {
        $tenants = Tenant::with('property')
            ->latest()
            ->paginate(20);

        return view('admin.tenants.index', compact('tenants'));
    }

    /**
     * Show trashed (soft deleted) tenants.
     */
    public function trashed()
    {
        $tenants = Tenant::onlyTrashed()
            ->with('property')
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
}