<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LandlordLoginController extends Controller
{
    /**
     * Show the landlord login page.
     */
    public function create()
    {
        return view('auth.landlord-login');
    }

    /**
     * Handle landlord login.
     */
    public function store(Request $request)
    {
        $credentials = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required'],
        ]);

        $remember = $request->boolean('remember');

        if (! Auth::attempt([
            'username' => $credentials['username'],
            'password' => $credentials['password'],
        ], $remember)) {

            return back()
                ->withErrors([
                    'username' => 'Invalid username or password.',
                ])
                ->onlyInput('username');
        }

        $request->session()->regenerate();

        if (! auth()->user()->hasRole('Landlord')) {

            Auth::logout();

            return back()
                ->withErrors([
                    'username' => 'You are not authorized to access the Landlord Portal.',
                ])
                ->onlyInput('username');
        }

            return redirect()->route('landlord.dashboard');
    }
}