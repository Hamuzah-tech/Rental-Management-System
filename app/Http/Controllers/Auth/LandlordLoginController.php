<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LandlordLoginController extends Controller
{
    public function create()
    {
        return view('auth.landlord-login');
    }

    public function store(Request $request)
    {
        $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $loginField = filter_var($request->username, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        
        $credentials = [
            $loginField => $request->username,
            'password' => $request->password,
        ];

        if (Auth::guard('landlord')->attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            
            $user = Auth::guard('landlord')->user();
            
            if ($user->role !== 'landlord') {
                Auth::guard('landlord')->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                
                throw ValidationException::withMessages([
                    'username' => 'You are not authorized to access the Landlord Portal.',
                ]);
            }
            
            if (!$user->is_active) {
                Auth::guard('landlord')->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                
                throw ValidationException::withMessages([
                    'username' => 'Your account is deactivated.',
                ]);
            }
            
            $user->last_login_at = now();
            $user->save();

            return redirect()->intended(route('landlord.dashboard'));
        }

        throw ValidationException::withMessages([
            'username' => trans('auth.failed'),
        ]);
    }

    public function destroy(Request $request)
    {
        Auth::guard('landlord')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('landlord.login');
    }
}