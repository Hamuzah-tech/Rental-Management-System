<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreLandlordRequest;
use App\Models\User;
use App\Services\LandlordService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class LandlordController extends Controller
{
    public function __construct(
        protected LandlordService $landlordService
    ) {
    }


    /**
     * Display all landlords.
     */
    public function index()
    {
        $landlords = User::role('Landlord')
            ->latest()
            ->paginate(10);

        return view('admin.landlords.index', compact('landlords'));
    }


    /**
     * Show create landlord form.
     */
    public function create()
    {
        return view('admin.landlords.create');
    }


    /**
     * Store a new landlord.
     */
    public function store(StoreLandlordRequest $request): RedirectResponse
    {
        $credentials = $this->landlordService->create(
            $request->validated()
        );

        return redirect()
            ->route('admin.landlords.index')
            ->with('success', 'Landlord created successfully.')
            ->with('credentials', $credentials);
    }


    /**
     * Display landlord details.
     */
    public function show(User $landlord)
    {
        return view('admin.landlords.show', compact('landlord'));
    }


    /**
     * Show edit landlord form.
     */
    public function edit(User $landlord)
    {
        return view('admin.landlords.edit', compact('landlord'));
    }


    /**
     * Update landlord.
     */
    public function update(Request $request, User $landlord): RedirectResponse
    {
        $validated = $request->validate([

            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'username' => [
                'required',
                'string',
                'max:50',
                'unique:users,username,' . $landlord->id,
            ],

            'email' => [
                'required',
                'email',
                'unique:users,email,' . $landlord->id,
            ],

            'phone' => [
                'required',
                'string',
                'max:20',
            ],

            'second_phone' => [
                'nullable',
                'string',
                'max:20',
            ],

        ]);


        $landlord->update($validated);


        return redirect()
            ->route('admin.landlords.index')
            ->with('success', 'Landlord updated successfully.');
    }


    /**
     * Delete landlord.
     */
    public function destroy(User $landlord): RedirectResponse
    {
        $landlord->delete();


        return redirect()
            ->route('admin.landlords.index')
            ->with('success', 'Landlord deleted successfully.');
    }


    /**
     * Activate / Suspend landlord.
     */
    public function toggleStatus(User $landlord): RedirectResponse
    {
        $landlord->update([
            'status' => $landlord->status ? 0 : 1,
        ]);


        return back()
            ->with('success', 'Landlord status updated.');
    }


    /**
     * Reset landlord password.
     */
    public function resetPassword(User $landlord): RedirectResponse
    {
        $newPassword = str()->random(10);


        $landlord->update([
            'password' => Hash::make($newPassword),
        ]);


        return back()
            ->with('success', 'Password reset successfully.')
            ->with('new_password', $newPassword);
    }
}
