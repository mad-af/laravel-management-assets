<?php

namespace App\Livewire;

use App\Models\Feedback;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class UserFeedbackBanner extends Component
{
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
        $this->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'message' => ['nullable', 'string', 'max:2000'],
        ]);

        $user = Auth::user();
        if (! $user) {
            return; // no-op if not authenticated
        }

        Feedback::create([
            'user_id' => $user->id,
            'period' => $this->period,
            'rating' => $this->rating,
            'message' => $this->message,
        ]);

        // Hide banner once submitted
        $this->showBanner = false;
        $this->showModal = false;

        // Show success toast via browser event
        $this->dispatchBrowserEvent('alert', [
            'type' => 'success',
            'message' => 'Terima kasih atas feedback Anda!',
        ]);
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
