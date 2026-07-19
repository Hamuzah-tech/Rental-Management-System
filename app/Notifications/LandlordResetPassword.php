<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;

class LandlordResetPassword extends ResetPassword
{
    public function toMail($notifiable)
    {
        $url = route('landlord.password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ]);

        return (new MailMessage)
            ->subject('Reset Your Landlord Password')
            ->line('You are receiving this email because we received a password reset request for your landlord account.')
            ->action('Reset Password', $url)
            ->line('This password reset link will expire in ' . config('auth.passwords.landlords.expire') . ' minutes.')
            ->line('If you did not request a password reset, no further action is required.');
    }
}