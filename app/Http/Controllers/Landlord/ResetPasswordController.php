<?php

namespace App\Http\Controllers\Landlord;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password as PasswordRule;
use Illuminate\Validation\ValidationException;

class ResetPasswordController extends Controller
{
    /**
     * Display the password reset view for the given token.
     *
     * @param  string  $token
     * @return \Illuminate\View\View
     */
    public function showResetForm($token)
    {
        return view('landlord.auth.reset-password', [
            'token' => $token,
            'email' => request()->input('email', ''),
        ]);
    }

    /**
     * Reset the given user's password.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     * 
     * @throws \Illuminate\Validation\ValidationException
     */
    public function reset(Request $request)
    {
        // 1. Validate the request using Laravel's password rule
        $request->validate([
            'token' => 'required|string',
            'email' => 'required|email',
            'password' => [
                'required',
                'confirmed',
                PasswordRule::defaults(),
            ],
        ]);

        // 2. Attempt to reset the password using the 'users' broker
        $status = Password::broker('users')->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                // 3. Verify the user is a landlord
                if ($user->role !== 'landlord') {
                    throw ValidationException::withMessages([
                        'email' => 'This account is not authorized to access the landlord portal.',
                    ]);
                }

                // 4. Verify the user is active
                if (!$user->is_active) {
                    throw ValidationException::withMessages([
                        'email' => 'This account is deactivated. Please contact support.',
                    ]);
                }

                // 5. Update the password and regenerate remember token
                $user->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();
            }
        );

        // 6. Handle the response
        if ($status === Password::PASSWORD_RESET) {
            return redirect()
                ->route('landlord.login')
                ->with('status', __($status));
        }

        // 7. If something went wrong, throw validation error
        throw ValidationException::withMessages([
            'email' => [trans($status)],
        ]);
    }
}