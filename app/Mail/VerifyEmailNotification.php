<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VerifyEmailNotification extends Mailable
{
    use Queueable, SerializesModels;

    public User $user;
    public string $verificationUrl;

    /**
     * Create a new message instance.
     */
    public function __construct(User $user, string $verificationUrl)
    {
        $this->user = $user;
        $this->verificationUrl = $verificationUrl;
    }

    /**
     * Build the message.
     */
    public function build(): static
    {
        $subject = 'Verifikasi Email Akun - '.$this->user->name;

        return $this->subject($subject)
            ->view('emails.auth.verify-email')
            ->with([
                'user' => $this->user,
                'verificationUrl' => $this->verificationUrl,
            ]);
    }
}