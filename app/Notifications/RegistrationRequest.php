<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\URL;
use App\Models\User;

class RegistrationRequest extends Notification
{
    use Queueable;

    protected User $newUser;

    public function __construct(User $newUser)
    {
        $this->newUser = $newUser;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $accountConfirmationUrl = $this->activationUrl();

        return (new MailMessage)
            ->subject('Nueva solicitud de activación de cuenta')
            ->markdown('emails.registration-request', [
                'newUser' => $this->newUser,
                'accountConfirmationUrl' => $accountConfirmationUrl,
            ]);
    }

    protected function activationUrl()
    {
        return URL::temporarySignedRoute('account.activation',Carbon::now()->addDays(30),['id' => $this->newUser->getKey(),'hash' => sha1($this->newUser->email),]);
    }
}
