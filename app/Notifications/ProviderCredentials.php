<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class ProviderCredentials extends Notification
{
    use Queueable;

    protected string $password;
    protected bool $isNewAccount;

    public function __construct(string $password, bool $isNewAccount = true)
    {
        $this->password = $password;
        $this->isNewAccount = $isNewAccount;
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $loginUrl = config('vayaquevalla.admin_url');

        return (new MailMessage)
            ->subject($this->isNewAccount ? 'Bienvenido a Vaya que Valla - Datos de acceso' : 'Tus datos de acceso han sido actualizados')
            ->view('emails.provider-credentials', [
                'user' => $notifiable,
                'password' => $this->password,
                'loginUrl' => $loginUrl,
                'isNewAccount' => $this->isNewAccount,
            ]);
    }
}
