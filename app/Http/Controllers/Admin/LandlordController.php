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
use App\Mail\LandlordWelcomeMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules\Password;

class LandlordController extends Controller
{
    /**
     * Display a listing of landlords (excluding soft deleted).
     */
    public function index()
    {
        $landlords = User::where('role', 'landlord')
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
        $landlords = User::where('role', 'landlord')
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
        $data = $request->validate([
            'name'         => 'required|string|max:255',
            'username'     => 'required|string|max:255|unique:users,username',
            'email'        => 'required|email|unique:users,email',
            'phone'        => 'required|string|max:15|unique:users,phone',
            'second_phone' => 'nullable|string|max:15',
        ]);

        try {
            // Generate a secure temporary password
            $plainPassword = Str::random(10);

            $user = User::create([
                'name'         => $data['name'],
                'username'     => $data['username'],
                'email'        => $data['email'],
                'phone'        => $data['phone'],
                'second_phone' => $data['second_phone'],
                'password'     => Hash::make($plainPassword),
                'role'         => 'landlord',
                'status'       => true,
                'is_active'    => true,
            ]);

            // Commit before sending email to avoid keeping transaction open
            DB::commit();

            // Send welcome email (send immediately)
            Mail::to($user->email)->send(new LandlordWelcomeMail($user, $plainPassword));

            Log::info('Landlord created successfully by admin', [
                'landlord_id' => $user->id,
                'admin_id' => auth()->id()
            ]);

            return redirect()
                ->route('admin.landlords.index')
                ->with('success', 'Landlord created successfully and login details emailed.');

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Landlord creation failed', [
                'error' => $e->getMessage(),
                'admin_id' => auth()->id()
            ]);

            return back()
                ->withInput()
                ->withErrors([
                    'error' => 'Failed to create landlord. Please try again.'
                ]);
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
            Log::error('Error updating landlord', [
                'landlord_id' => $landlord->id,
                'error' => $e->getMessage(),
                'admin_id' => auth()->id()
            ]);

            return back()
                ->withInput()
                ->withErrors([
                    'error' => 'Failed to update landlord. Please try again.'
                ]);
        }
    }

    /**
     * Soft delete the specified landlord.
     */
    public function destroy(User $landlord)
    {
        if ($landlord->properties()->count() > 0) {
            return back()->withErrors([
                'error' => 'This landlord cannot be deleted because they currently own ' .
                    $landlord->properties()->count() .
                    ' propert' . ($landlord->properties()->count() == 1 ? 'y' : 'ies') .
                    '. Please transfer or remove the properties before deleting the landlord.'
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
        try {
            $landlord = User::onlyTrashed()
                ->where('role', 'landlord')
                ->findOrFail($id);
            
            $landlord->restore();

            Log::info('Landlord restored by admin', [
                'landlord_id' => $landlord->id,
                'admin_id' => auth()->id()
            ]);

            return redirect()
                ->route('admin.trash.landlords')
                ->with('success', 'Landlord restored successfully.');

        } catch (\Exception $e) {
            Log::error('Error restoring landlord', [
                'landlord_id' => $id,
                'error' => $e->getMessage(),
                'admin_id' => auth()->id()
            ]);

            return redirect()
                ->route('admin.trash.landlords')
                ->withErrors(['error' => 'Failed to restore landlord. Please try again.']);
        }
    }

    /**
     * Permanently delete a landlord (Admin only).
     */
    public function forceDelete($id)
    {
        try {
            $landlord = User::onlyTrashed()
                ->where('role', 'landlord')
                ->findOrFail($id);
            
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

        } catch (\Exception $e) {
            Log::error('Error permanently deleting landlord', [
                'landlord_id' => $id,
                'error' => $e->getMessage(),
                'admin_id' => auth()->id()
            ]);

            return redirect()
                ->route('admin.trash.landlords')
                ->withErrors(['error' => 'Failed to permanently delete landlord. Please try again.']);
        }
    }

    /**
     * Toggle landlord status.
     */
    public function toggleStatus(User $landlord)
    {
        try {
            $landlord->update([
                'status' => !$landlord->status,
                'is_active' => !$landlord->is_active // Keep both status fields in sync
            ]);

            Log::info('Landlord status toggled by admin', [
                'landlord_id' => $landlord->id,
                'new_status' => $landlord->status,
                'new_is_active' => $landlord->is_active,
                'admin_id' => auth()->id()
            ]);

            return redirect()
                ->route('admin.landlords.index')
                ->with('success', 'Landlord status updated successfully.');

        } catch (\Exception $e) {
            Log::error('Error toggling landlord status', [
                'landlord_id' => $landlord->id,
                'error' => $e->getMessage(),
                'admin_id' => auth()->id()
            ]);

            return back()->withErrors(['error' => 'Failed to update landlord status. Please try again.']);
        }
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
                    'password' => ['required', 'confirmed', Password::defaults()],
                ]);
                $newPassword = $request->password;
            } else {
                // Generate a strong random password
                $newPassword = $this->generateRandomPassword();
            }

            // Update the password and regenerate remember token
            $landlord->update([
                'password' => Hash::make($newPassword),
                'remember_token' => Str::random(60),
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
            Log::error('Error resetting password', [
                'landlord_id' => $landlord->id,
                'error' => $e->getMessage(),
                'admin_id' => auth()->id()
            ]);
            return back()
                ->withErrors(['error' => 'Failed to reset password. Please try again.']);
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
        $totalLandlords = User::where('role', 'landlord')->count();
        $activeLandlords = User::where('role', 'landlord')->where('status', true)->count();
        $inactiveLandlords = User::where('role', 'landlord')->where('status', false)->count();
        $trashedLandlords = User::where('role', 'landlord')->onlyTrashed()->count();

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
                    User::where('role', 'landlord')
                        ->whereIn('id', $landlordIds)
                        ->update([
                            'status' => true,
                            'is_active' => true
                        ]);
                    $message = 'Landlords activated successfully.';
                    break;
                case 'deactivate':
                    User::where('role', 'landlord')
                        ->whereIn('id', $landlordIds)
                        ->update([
                            'status' => false,
                            'is_active' => false
                        ]);
                    $message = 'Landlords deactivated successfully.';
                    break;
                case 'delete':
                    User::where('role', 'landlord')
                        ->whereIn('id', $landlordIds)
                        ->delete();
                    $message = 'Landlords moved to archive.';
                    break;
                case 'restore':
                    User::where('role', 'landlord')
                        ->whereIn('id', $landlordIds)
                        ->restore();
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
            Log::error('Bulk action error', [
                'action' => $action,
                'error' => $e->getMessage(),
                'admin_id' => auth()->id()
            ]);
            return back()->withErrors(['error' => 'Failed to perform bulk action. Please try again.']);
        }
    }

    /**
     * Export landlords to CSV/Excel.
     */
    public function export(Request $request)
    {
        $landlords = User::where('role', 'landlord')
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
                // Sanitize CSV fields to prevent CSV injection
                $fields = [
                    $this->sanitizeCsvField($landlord->id),
                    $this->sanitizeCsvField($landlord->name),
                    $this->sanitizeCsvField($landlord->username ?? ''),
                    $this->sanitizeCsvField($landlord->email),
                    $this->sanitizeCsvField($landlord->phone ?? ''),
                    $this->sanitizeCsvField($landlord->properties_count),
                    $this->sanitizeCsvField($landlord->status ? 'Active' : 'Inactive'),
                    $this->sanitizeCsvField($landlord->created_at->format('Y-m-d H:i:s')),
                ];
                
                fputcsv($handle, $fields);
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

        $landlords = User::where('role', 'landlord')
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

    /**
     * Sanitize CSV field to prevent CSV injection.
     */
    private function sanitizeCsvField($field)
    {
        if ($field === null) {
            return '';
        }
        
        $field = (string) $field;
        
        // If field starts with =, +, -, @, prefix with a single quote
        if (in_array(substr($field, 0, 1), ['=', '+', '-', '@'])) {
            return "'" . $field;
        }
        
        return $field;
    }
}