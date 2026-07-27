<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AdminLoginController extends Controller
{
    /**
     * Show the admin login form.
     */
    public function create()
    {
        return view('auth.admin-login');
    }

    /**
     * Handle admin login.
     */
    public function store(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (!Auth::guard('admin')->attempt([
            'email' => $request->email,
            'password' => $request->password,
            'role' => 'super_admin',
            'is_active' => 1,
        ], $request->boolean('remember'))) {

            throw ValidationException::withMessages([
                'email' => 'Invalid administrator credentials.',
            ]);
        }

        $request->session()->regenerate();

        $user = Auth::guard('admin')->user();
        $user->last_login_at = now();
        $user->save();

        return redirect()->route('admin.dashboard');
    }

    /**
     * Logout admin.
     */
    public function destroy(Request $request)
    {
        Auth::guard('admin')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }
}