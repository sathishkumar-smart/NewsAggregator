<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResetPasswordNotification extends Notification
{
    use Queueable;

    public string $token;

    /**
     * Create a new notification instance.
     *
     * @param string $token
     */
    public function __construct(string $token)
    {
        $this->token = $token;
    }

    /**
     * Channels to deliver notification through.
     */
    public function via($notifiable): array
    {
        return ['mail'];
    }

    /**
     * Build the password reset email for API-based clients.
     */
    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Your Password Reset Token')
            ->line('You requested to reset your password.')
            ->line('Use the token below along with your email and new password in the API call:')
            ->line("**Token:** `$this->token`")
            ->line('If you did not request this, please ignore this email.');
    }

    /**
     * Optional array payload (unused here).
     */
    public function toArray($notifiable): array
    {
        return [];
    }
}
