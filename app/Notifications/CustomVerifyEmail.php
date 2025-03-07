<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;

class CustomVerifyEmail extends VerifyEmail implements ShouldQueue
{
    use Queueable;

    public function toMail($notifiable)
    {
        $verificationUrl = $this->verificationUrl($notifiable);

        return $this->buildMailMessage($verificationUrl)
            ->subject('Please Verify Your Email Address')
            ->markdown('emails.verify', [
                'url' => $verificationUrl,
                'user' => $notifiable
            ]);
    }
} 