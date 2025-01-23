<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\URL;

class VerifyEmailNotification extends Notification
{
    use Queueable;

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $expire = Carbon::now()->addMinutes(Config::get('auth.verification.expire', 60));
        $url = $this->verificationUrl($notifiable, $expire);

        return (new MailMessage)
            ->subject('Подтверждение регистрации')
            ->view('email.verify-email', [
                'url' => $url,
                'expire' => $expire->
                    locale(config('app.locale'))->
                    timezone('Europe/Moscow')->
                    translatedFormat("j F Y, H:i:s"),
            ]);

    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }

    protected function verificationUrl($notifiable, $expire)
    {
        return URL::temporarySignedRoute(
            'verification.verify',
            $expire,
            [
                'id' => $notifiable->getKey(),
                'hash' => sha1($notifiable->getEmailForVerification()),
            ]
        );
    }
}
