<?php

namespace App\Http\Controllers\Landlord;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class ForgotPasswordController extends Controller
{
    /**
     * Display the form to request a password reset link.
     *
     * @return \Illuminate\View\View
     */
    public function showLinkRequestForm()
    {
        return view('landlord.auth.forgot-password');
    }

    /**
     * Send a reset link to the given user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     * 
     * @throws \Illuminate\Validation\ValidationException
     */
    public function sendResetLinkEmail(Request $request)
    {
        // 1. Validate the email - no exists validation to prevent enumeration
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        // 2. Create a rate limiter key
        $throttleKey = Str::transliterate(Str::lower($request->email) . '|' . $request->ip());

        // 3. Check if rate limit is exceeded (5 attempts per 10 minutes)
        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            
            // Always return the same success message to prevent enumeration
            return back()->with([
                'status' => 'If an account exists with this email, we have sent a password reset link.'
            ]);
        }

        // 4. Check if user exists and is a landlord (silent check - no feedback)
        $user = User::where('email', $request->email)
            ->where('role', 'landlord')
            ->where('is_active', true)
            ->first();

        // 5. Only send reset link if user exists and meets criteria
        $status = null;
        
        if ($user) {
            // Send the password reset link using the 'users' broker
            $status = Password::broker('users')->sendResetLink(
                $request->only('email')
            );
        }

        // 6. Always hit the rate limiter after processing
        RateLimiter::hit($throttleKey, 600); // 600 seconds = 10 minutes

        // 7. Always return the same success message
        // This prevents attackers from knowing if an email exists or not
        return back()->with([
            'status' => 'If an account exists with this email, we have sent a password reset link.'
        ]);
    }
}