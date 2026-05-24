<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;

class VerifyEmailNotification extends VerifyEmail
{
    public function toMail($notifiable): MailMessage
    {
        $verificationUrl = $this->verificationUrl($notifiable);

        return (new MailMessage)
            ->subject('Confirme seu e-mail na Shopla')
            ->view([
                'html' => 'emails.auth.verify-email',
                'text' => 'emails.auth.verify-email-text',
            ], [
                'user' => $notifiable,
                'verificationUrl' => $verificationUrl,
            ]);
    }
}
