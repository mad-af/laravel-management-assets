<?php

namespace App\Livewire;

use Livewire\Attributes\On;
use Livewire\Component;
use Mary\Traits\Toast;

class ToastListener extends Component
{
    use Toast;

    public function mount(): void
    {
        // Trigger toast from session flashes
        if (session()->has('success')) {
            $this->success(session('success'));
        }
        
        if (session()->has('error')) {
            $message = session('error');
            $this->error($message);
        }
    }

    #[On('toast')]
    public function handleToast($payloadOrType, $message = null, $title = null, $description = null, $position = null, $icon = null, $css = null, $timeout = null, $redirectTo = null): void
    {
        // Support both array payload and positional params
        if (is_array($payloadOrType)) {
            $p = $payloadOrType;
            $type = $p['type'] ?? 'info';
            $title = $p['title'] ?? ($p['message'] ?? '');
            $description = $p['description'] ?? null;
            $position = $p['position'] ?? null;
            $icon = $p['icon'] ?? null;
            $css = $p['css'] ?? null;
            $timeout = $p['timeout'] ?? null;
            $redirectTo = $p['redirectTo'] ?? null;
            $this->triggerToast($type, $title, $description, $position, $icon, $css, $timeout, $redirectTo);

            return;
        }

        // Positional params: type, message, title
        $type = $payloadOrType ?? 'info';
        $title = $message ?? $title ?? '';
        $this->triggerToast($type, $title, $description, $position, $icon, $css, $timeout, $redirectTo);
    }

    #[On('showAlert')]
    public function handleShowAlert($payloadOrType, $message = null, $title = null): void
    {
        // Accept array payload or positional params for compatibility
        if (is_array($payloadOrType)) {
            $type = $payloadOrType['type'] ?? 'info';
            $msg = $payloadOrType['message'] ?? '';
            $ttl = $payloadOrType['title'] ?? $msg;
            $this->triggerToast($type, $ttl);

            return;
        }

        $type = (string) $payloadOrType;
        $ttl = $title ?? $message ?? '';
        $this->triggerToast($type, $ttl);
    }

    private function triggerToast(string $type, string $title, ?string $description = null, ?string $position = null, ?string $icon = null, ?string $css = null, ?int $timeout = null, ?string $redirectTo = null): void
    {
        // Normalisasi ikon agar tidak pernah null (Mary Toast mengharuskan string)
        $iconDefaults = [
            'success' => 'o-check',
            'error' => 'o-exclamation-triangle',
            'warning' => 'o-exclamation-triangle',
            'info' => 'o-information-circle',
        ];
        $normalizedType = strtolower($type);
        $icon = is_string($icon) && $icon !== ''
            ? $icon
            : ($iconDefaults[$normalizedType] ?? 'o-information-circle');

        switch (strtolower($type)) {
            case 'success':
                $this->success($title, $description, $position, $icon, $css, $timeout, $redirectTo);
                break;
            case 'error':
                $this->error($title, $description, $position, $icon, $css, $timeout, $redirectTo);
                break;
            case 'warning':
                $this->warning($title, $description, $position, $icon, $css, $timeout, $redirectTo);
                break;
            default:
                $this->info($title, $description, $position, $icon, $css, $timeout, $redirectTo);
                break;
        }
    }

    public function render()
    {
        return view('livewire.toast-listener');
    }
}
