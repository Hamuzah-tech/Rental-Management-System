<?php

namespace App\Http\Controllers\Landlord;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
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
        // 1. Validate the email
        $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
        ]);

        // 2. Check if user is a landlord
        $user = \App\Models\User::where('email', $request->email)
            ->where('role', 'landlord')
            ->where('is_active', true)
            ->first();

        if (!$user) {
            throw ValidationException::withMessages([
                'email' => 'We cannot find a landlord account with this email address.',
            ]);
        }

        // 3. Send the password reset link using the 'users' broker
        $status = Password::broker('users')->sendResetLink(
            $request->only('email')
        );

        // 4. Handle the response
        if ($status === Password::RESET_LINK_SENT) {
            return back()->with(['status' => __($status)]);
        }

        // 5. If something went wrong, throw validation error
        throw ValidationException::withMessages([
            'email' => [trans($status)],
        ]);
    }
}