<?php

namespace App\Console\Commands;

use App\Mail\FeedbackSubmitted;
use App\Models\Feedback;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendFeedbackSubmittedMailable extends Command
{
    /**
     * The name and signature of the console command.
     *
     * Usage:
     *  php artisan feedback:send-mailable {feedback_id?} {--queued}
     */
    protected $signature = 'feedback:send-mailable {feedback_id?} {--queued : Queue the email instead of sending synchronously}';

    /**
     * The console command description.
     */
    protected $description = 'Kirim Mailable FeedbackSubmitted untuk menguji pengiriman email end-to-end';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $feedbackId = $this->argument('feedback_id');

        $to = config('mail.feedback_receiver') ?? config('mail.from.address');
        if (empty($to)) {
            $this->error('Konfigurasi mail.feedback_receiver atau mail.from.address tidak ditemukan. Set FEEDBACK_RECEIVER_EMAIL atau MAIL_FROM_ADDRESS di .env.');

            return self::FAILURE;
        }

        $feedback = null;
        if ($feedbackId) {
            $feedback = Feedback::query()->with('user')->find($feedbackId);
            if (! $feedback) {
                $this->error("Feedback dengan ID {$feedbackId} tidak ditemukan.");

                return self::FAILURE;
            }
        } else {
            // Buat instance dummy di memori (tanpa menyimpan ke DB) agar aman dari constraint unik
            $user = User::query()->first();
            $feedback = new Feedback([
                'user_id' => $user?->id,
                'period' => 'TEST-'.now()->format('YmdHis'),
                'rating' => 5,
                'message' => 'Pengujian pengiriman email FeedbackSubmitted (dummy)',
            ]);
            if ($user) {
                $feedback->setRelation('user', $user);
            }
            $feedback->created_at = now();
        }

        $mailable = new FeedbackSubmitted($feedback);

        $subjectPreview = 'Feedback Baru: '.($feedback->period ?? '');

        try {
            if ($this->option('queued')) {
                Mail::to($to)->queue($mailable);
                $this->info("Email diantrikan ke {$to} dengan subject: '{$subjectPreview}'. Jalankan queue worker untuk memproses.");
            } else {
                Mail::to($to)->send($mailable);
                $this->info("Email dikirim ke {$to} dengan subject: '{$subjectPreview}'.");
            }
        } catch (\Exception $e) {
            dd($e);
            $this->error("Gagal mengirim email ke {$to} dengan subject: '{$subjectPreview}'. Error: {$e->getMessage()}");

            return self::FAILURE;
        }

        return self::SUCCESS;
    }
}
