<?php

namespace App\Mail;

use App\Models\Feedback;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class FeedbackSubmitted extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public Feedback $feedback;

    /**
     * Create a new message instance.
     */
    public function __construct(Feedback $feedback)
    {
        $this->feedback = $feedback;
    }

    /**
     * Build the message.
     */
    public function build(): self
    {
        return $this->subject('Feedback Baru: '.$this->feedback->period)
            ->view('emails.feedback-submitted')
            ->with([
                'feedback' => $this->feedback,
            ]);
    }
}