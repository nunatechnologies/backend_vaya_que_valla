<?php
// app/Notifications/VerifyApiEmail.php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Auth\Notifications\VerifyEmail as BaseVerifyEmail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\URL;

class VerifyApiEmail extends BaseVerifyEmail
{
    public function toMail($notifiable)
    {
        $verificationUrl = $this->verificationUrl($notifiable);

        return (new MailMessage)
            ->subject('Verifica tu correo')
            ->line('Haz clic en el botón para verificar tu dirección de correo electrónico.')
            ->action('Verificar Email', $verificationUrl)
            ->line('Si no creaste una cuenta, no es necesario hacer nada.');
    }

    protected function verificationUrl($notifiable)
    {
        return URL::temporarySignedRoute(
            'verification.verify',
            Carbon::now()->addMinutes(60),
            [
                'id' => $notifiable->getKey(),
                'hash' => sha1($notifiable->getEmailForVerification()),
            ]
        );
    }
}
