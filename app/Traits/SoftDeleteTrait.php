<?php
// app/Traits/SoftDeleteTrait.php

namespace App\Traits;

use Illuminate\Http\Request;

trait SoftDeleteTrait
{
    /**
     * Get trashed (soft deleted) records
     */
    public function getTrashed()
    {
        return $this->model::onlyTrashed()->get();
    }

    /**
     * Restore a soft deleted record
     */
    public function restore($id)
    {
        $record = $this->model::withTrashed()->findOrFail($id);
        
        // Check authorization
        $this->authorizeRestore($record);
        
        $record->restore();
        
        return $record;
    }

    /**
     * Permanently delete a record (Admin only)
     */
    public function forceDelete($id)
    {
        $record = $this->model::withTrashed()->findOrFail($id);
        
        // Check authorization - Admin only
        $this->authorizeForceDelete($record);
        
        $record->forceDelete();
        
        return $record;
    }

    /**
     * Authorize restore - Override in controller
     */
    protected function authorizeRestore($record)
    {
        // Default: only admins can restore
        abort_if(!auth()->user()->hasRole('Super Admin'), 403);
    }

    /**
     * Authorize force delete - Override in controller
     */
    protected function authorizeForceDelete($record)
    {
        // Only admins can force delete
        abort_if(!auth()->user()->hasRole('Super Admin'), 403);
    }
}