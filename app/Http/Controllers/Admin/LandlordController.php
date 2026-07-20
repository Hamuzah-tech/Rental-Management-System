<?php
// app/Http/Controllers/Admin/LandlordController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Property;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class LandlordController extends Controller
{
    /**
     * Display a listing of landlords (excluding soft deleted).
     */
    public function index()
    {
        $landlords = User::role('Landlord')
            ->withCount('properties')
            ->latest()
            ->paginate(20);

        return view('admin.landlords.index', compact('landlords'));
    }

    /**
     * Show trashed (soft deleted) landlords.
     */
    public function trashed()
    {
        $landlords = User::role('Landlord')
            ->onlyTrashed()
            ->withCount('properties')
            ->latest('deleted_at')
            ->paginate(20);

        return view('admin.trash.landlords', compact('landlords'));
    }

    /**
     * Show the form for creating a new landlord.
     */
    public function create()
    {
        return view('admin.landlords.create');
    }

    /**
     * Store a newly created landlord in storage.
     */
    public function store(Request $request)
    {
        try {
            $data = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'phone' => 'required|string|unique:users,phone|max:15',
                'password' => 'required|string|min:8|confirmed',
            ]);

            // Generate a username from email
            $username = explode('@', $data['email'])[0];
            
            // Check if username exists, if so append random numbers
            $baseUsername = $username;
            $counter = 1;
            while (User::where('username', $username)->exists()) {
                $username = $baseUsername . $counter;
                $counter++;
            }

            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'username' => $username,
                'phone' => $data['phone'],
                'password' => Hash::make($data['password']),
                'status' => true,
            ]);

            $user->assignRole('Landlord');

            Log::info('Landlord created by admin', [
                'landlord_id' => $user->id,
                'admin_id' => auth()->id()
            ]);

            // Store credentials to show to admin
            return redirect()
                ->route('admin.landlords.index')
                ->with('success', 'Landlord created successfully.')
                ->with('credentials', [
                    'username' => $username,
                    'password' => $data['password']
                ]);

        } catch (\Exception $e) {
            Log::error('Error creating landlord: ' . $e->getMessage());
            return back()
                ->withInput()
                ->withErrors(['error' => 'Failed to create landlord: ' . $e->getMessage()]);
        }
    }

    /**
     * Display the specified landlord.
     */
    public function show(User $landlord)
    {
        $landlord->load(['properties' => function ($q) {
            $q->withCount('tenants');
        }]);

        return view('admin.landlords.show', compact('landlord'));
    }

    /**
     * Show the form for editing the specified landlord.
     */
    public function edit(User $landlord)
    {
        return view('admin.landlords.edit', compact('landlord'));
    }

    /**
     * Update the specified landlord in storage.
     */
    public function update(Request $request, User $landlord)
    {
        try {
            $data = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email,' . $landlord->id,
                'phone' => 'required|string|unique:users,phone,' . $landlord->id . '|max:15',
                'username' => 'nullable|string|unique:users,username,' . $landlord->id,
            ]);

            // Update username if provided
            if (empty($data['username'])) {
                $data['username'] = explode('@', $data['email'])[0];
            }

            $landlord->update($data);

            Log::info('Landlord updated by admin', [
                'landlord_id' => $landlord->id,
                'admin_id' => auth()->id()
            ]);

            return redirect()
                ->route('admin.landlords.index')
                ->with('success', 'Landlord updated successfully.');

        } catch (\Exception $e) {
            Log::error('Error updating landlord: ' . $e->getMessage());
            return back()
                ->withInput()
                ->withErrors(['error' => 'Failed to update landlord: ' . $e->getMessage()]);
        }
    }

    /**
     * Soft delete the specified landlord.
     */
    public function destroy(User $landlord)
    {
        // Check if landlord has properties
        if ($landlord->properties()->count() > 0) {
            return back()->withErrors([
                'error' => 'Cannot delete landlord with active properties. Archive properties first.'
            ]);
        }

        $landlord->delete();

        Log::info('Landlord soft deleted by admin', [
            'landlord_id' => $landlord->id,
            'admin_id' => auth()->id()
        ]);

        return redirect()
            ->route('admin.landlords.index')
            ->with('success', 'Landlord moved to archive.');
    }

    /**
     * Restore a soft deleted landlord.
     */
    public function restore($id)
    {
        $landlord = User::onlyTrashed()->findOrFail($id);
        $landlord->restore();

        Log::info('Landlord restored by admin', [
            'landlord_id' => $landlord->id,
            'admin_id' => auth()->id()
        ]);

        return redirect()
            ->route('admin.trash.landlords')
            ->with('success', 'Landlord restored successfully.');
    }

    /**
     * Permanently delete a landlord (Admin only).
     */
    public function forceDelete($id)
    {
        $landlord = User::onlyTrashed()->findOrFail($id);
        
        // Delete related properties and tenants first
        foreach ($landlord->properties as $property) {
            $property->tenants()->delete();
            $property->forceDelete();
        }
        
        $landlord->forceDelete();

        Log::info('Landlord permanently deleted by admin', [
            'landlord_id' => $landlord->id,
            'admin_id' => auth()->id()
        ]);

        return redirect()
            ->route('admin.trash.landlords')
            ->with('success', 'Landlord permanently deleted.');
    }

    /**
     * Toggle landlord status.
     */
    public function toggleStatus(User $landlord)
    {
        $landlord->update([
            'status' => !$landlord->status
        ]);

        Log::info('Landlord status toggled by admin', [
            'landlord_id' => $landlord->id,
            'new_status' => $landlord->status,
            'admin_id' => auth()->id()
        ]);

        return redirect()
            ->route('admin.landlords.index')
            ->with('success', 'Landlord status updated successfully.');
    }

    /**
     * Reset landlord password.
     * This method generates a new random password and displays it to the admin.
     */
    public function resetPassword(Request $request, User $landlord)
    {
        try {
            // If password is provided in request, use it; otherwise generate random
            if ($request->filled('password')) {
                $request->validate([
                    'password' => 'required|string|min:8|confirmed',
                ]);
                $newPassword = $request->password;
            } else {
                // Generate a strong random password
                $newPassword = $this->generateRandomPassword();
            }

            // Update the password
            $landlord->update([
                'password' => Hash::make($newPassword)
            ]);

            Log::info('Landlord password reset by admin', [
                'landlord_id' => $landlord->id,
                'admin_id' => auth()->id()
            ]);

            // Store the new password in session to display
            return redirect()
                ->route('admin.landlords.index')
                ->with('success', 'Password reset successfully!')
                ->with('new_password', $newPassword)
                ->with('landlord_name', $landlord->name);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            Log::error('Error resetting password: ' . $e->getMessage());
            return back()
                ->withErrors(['error' => 'Failed to reset password: ' . $e->getMessage()]);
        }
    }

    /**
     * Generate a random secure password.
     */
    private function generateRandomPassword($length = 10)
    {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_+-=';
        $password = '';
        for ($i = 0; $i < $length; $i++) {
            $password .= $chars[random_int(0, strlen($chars) - 1)];
        }
        return $password;
    }

    /**
     * Get landlord statistics.
     */
    public function statistics()
    {
        $totalLandlords = User::role('Landlord')->count();
        $activeLandlords = User::role('Landlord')->where('status', true)->count();
        $inactiveLandlords = User::role('Landlord')->where('status', false)->count();
        $trashedLandlords = User::role('Landlord')->onlyTrashed()->count();

        return response()->json([
            'total' => $totalLandlords,
            'active' => $activeLandlords,
            'inactive' => $inactiveLandlords,
            'trashed' => $trashedLandlords,
        ]);
    }

    /**
     * Bulk action on landlords.
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'landlord_ids' => 'required|array',
            'landlord_ids.*' => 'exists:users,id',
            'action' => 'required|in:activate,deactivate,delete,restore',
        ]);

        $landlordIds = $request->landlord_ids;
        $action = $request->action;

        try {
            switch ($action) {
                case 'activate':
                    User::whereIn('id', $landlordIds)->update(['status' => true]);
                    $message = 'Landlords activated successfully.';
                    break;
                case 'deactivate':
                    User::whereIn('id', $landlordIds)->update(['status' => false]);
                    $message = 'Landlords deactivated successfully.';
                    break;
                case 'delete':
                    User::whereIn('id', $landlordIds)->delete();
                    $message = 'Landlords moved to archive.';
                    break;
                case 'restore':
                    User::whereIn('id', $landlordIds)->restore();
                    $message = 'Landlords restored successfully.';
                    break;
                default:
                    return back()->withErrors(['error' => 'Invalid action.']);
            }

            Log::info('Bulk action on landlords', [
                'action' => $action,
                'landlord_ids' => $landlordIds,
                'admin_id' => auth()->id()
            ]);

            return redirect()
                ->route('admin.landlords.index')
                ->with('success', $message);

        } catch (\Exception $e) {
            Log::error('Bulk action error: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Failed to perform bulk action.']);
        }
    }

    /**
     * Export landlords to CSV/Excel.
     */
    public function export(Request $request)
    {
        $landlords = User::role('Landlord')
            ->withCount('properties')
            ->get();

        $filename = 'landlords_export_' . date('Y-m-d_H-i-s') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($landlords) {
            $handle = fopen('php://output', 'w');
            
            // Headers
            fputcsv($handle, [
                'ID', 'Name', 'Username', 'Email', 'Phone', 
                'Properties Count', 'Status', 'Created At'
            ]);

            // Data
            foreach ($landlords as $landlord) {
                fputcsv($handle, [
                    $landlord->id,
                    $landlord->name,
                    $landlord->username ?? '',
                    $landlord->email,
                    $landlord->phone ?? '',
                    $landlord->properties_count,
                    $landlord->status ? 'Active' : 'Inactive',
                    $landlord->created_at->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Search landlords.
     */
    public function search(Request $request)
    {
        $search = $request->get('search');

        $landlords = User::role('Landlord')
            ->where(function ($query) use ($search) {
                $query->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('email', 'LIKE', "%{$search}%")
                    ->orWhere('username', 'LIKE', "%{$search}%")
                    ->orWhere('phone', 'LIKE', "%{$search}%");
            })
            ->withCount('properties')
            ->paginate(20);

        return view('admin.landlords.index', compact('landlords'));
    }
}