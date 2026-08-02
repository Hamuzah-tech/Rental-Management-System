<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
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

        // Step 2: Create a throttle key with transliteration
        $throttleKey = Str::transliterate(Str::lower($request->username) . '|' . $request->ip());

        // Step 3: Check if the user has exceeded the maximum attempts
        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);

            throw ValidationException::withMessages([
                'username' => "Too many login attempts. Please try again in {$seconds} seconds.",
            ]);
        }

        $loginField = filter_var($request->username, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        
        $credentials = [
            $loginField => $request->username,
            'password' => $request->password,
        ];

        if (Auth::guard('landlord')->attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            
            $user = Auth::guard('landlord')->user();
            
            if ($user->role !== 'landlord') {
                // Step 6: Count unauthorized users with 5-minute lockout
                RateLimiter::hit($throttleKey, 300);
                
                Auth::guard('landlord')->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                
                throw ValidationException::withMessages([
                    'username' => 'You are not authorized to access the Landlord Portal.',
                ]);
            }
            
            if (!$user->is_active) {
                // Step 7: Count deactivated users with 5-minute lockout
                RateLimiter::hit($throttleKey, 300);
                
                Auth::guard('landlord')->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                
                throw ValidationException::withMessages([
                    'username' => 'Your account is deactivated.',
                ]);
            }
            
            // All checks passed - clear the rate limiter
            RateLimiter::clear($throttleKey);
            
            $user->last_login_at = now();
            $user->save();

            return redirect()->intended(route('landlord.dashboard'));
        }

        // Step 5: Count failed login attempts with 5-minute lockout
        RateLimiter::hit($throttleKey, 300);

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