<?php

namespace App\Http\Controllers\Landlord;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
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
        // 1. Validate the request
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|confirmed|min:8',
        ]);

        // 2. Attempt to reset the password using the 'users' broker
        $status = Password::broker('users')->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                // 3. Update the password
                $user->forceFill([
                    'password' => Hash::make($password),
                ])->save();
            }
        );

        // 4. Handle the response
        if ($status === Password::PASSWORD_RESET) {
            return redirect()
                ->route('landlord.login')
                ->with('status', __($status));
        }

        // 5. If something went wrong, throw validation error
        throw ValidationException::withMessages([
            'email' => [trans($status)],
        ]);
    }
}