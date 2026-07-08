<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminLoginController extends Controller
{
    /**
     * Show the admin login page.
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
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $remember = $request->boolean('remember');

        if (! Auth::attempt($credentials, $remember)) {
            return back()
                ->withErrors([
                    'email' => 'Invalid login credentials.',
                ])
                ->onlyInput('email');
        }

        $request->session()->regenerate();

        if (! auth()->user()->hasRole('Super Admin')) {

            Auth::logout();

            return back()->withErrors([
                'email' => 'You are not authorized to access the Admin Portal.',
            ]);
        }

        return redirect('/admin/dashboard');
    }
}