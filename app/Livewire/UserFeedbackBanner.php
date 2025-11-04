<?php

namespace App\Livewire;

use App\Mail\FeedbackSubmitted;
use App\Models\Feedback;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Livewire\Component;
use Mary\Traits\Toast;

class UserFeedbackBanner extends Component
{
    use Toast;

    public bool $showBanner = false;

    public bool $showModal = false;

    public int $rating = 0;

    public string $message = '';

    public string $period = '';

    public function mount(): void
    {
        $this->period = $this->currentPeriod();
        $user = Auth::user();
        if ($user) {
            $hasFeedback = Feedback::where('user_id', $user->id)
                ->where('period', $this->period)
                ->exists();
            $this->showBanner = ! $hasFeedback;
        }
    }

    public function openModal(): void
    {
        $this->showModal = true;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
    }

    public function submit(): void
    {
        try {
            $this->validate([
                'rating' => ['required', 'integer', 'min:1', 'max:5'],
                'message' => ['nullable', 'string', 'max:2000'],
            ]);

        $user = Auth::user();
        if (! $user) {
            return; // no-op if not authenticated
        }

        $feedback = Feedback::create([
            'user_id' => $user->id,
            'period' => $this->period,
            'rating' => $this->rating,
            'message' => $this->message,
        ]);

        // Queue email to admin/owner
        $receiver = config('mail.feedback_receiver') ?? config('mail.from.address');
        if ($receiver) {
            Mail::to($receiver)->queue(new FeedbackSubmitted($feedback));
        }

        $this->success('Terima kasih atas feedback Anda!');
        } catch (\Exception $e) {
            $this->error('Gagal mengirim feedback.');
        } finally {
            $this->showBanner = false;
            $this->showModal = false;
        }
    }

    private function currentPeriod(): string
    {
        $now = now();
        $quarter = (int) ceil($now->month / 3);

        return $now->year.'-Q'.$quarter; // e.g., 2025-Q4
    }

    public function render()
    {
        return view('livewire.user-feedback-banner');
    }
}
