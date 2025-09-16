<!-- Alert Container - Fixed position at top right -->
<div class="fixed top-4 right-4 z-50 space-y-2" style="max-width: 400px;">
    @foreach($alerts as $alert)
        <div 
            wire:key="alert-{{ $alert['id'] }}"
            x-data="{ show: false, alertId: '{{ $alert['id'] }}' }"
            x-init="
                show = true;
                @if($autoHide)
                    setTimeout(() => {
                        show = false;
                        setTimeout(() => {
                            $wire.removeAlert('{{ $alert['id'] }}');
                        }, 300);
                    }, {{ $hideDelay }});
                @endif
            "
            x-show="show"
            x-transition:enter="transform ease-out duration-300 transition"
            x-transition:enter-start="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
            x-transition:enter-end="translate-y-0 opacity-100 sm:translate-x-0"
            x-transition:leave="transition ease-in duration-100"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="alert {{ $this->getAlertClass($alert['type']) }} shadow-lg border border-opacity-20"
        >
            <!-- Alert Icon -->
            <div class="flex-shrink-0">
                <i data-lucide="{{ $this->getAlertIcon($alert['type']) }}" class="w-5 h-5"></i>
            </div>

            <!-- Alert Content -->
            <div class="flex-1 min-w-0">
                @if($alert['title'])
                    <div class="font-semibold text-sm">
                        {{ $alert['title'] }}
                    </div>
                @endif
                <div class="text-sm {{ $alert['title'] ? 'mt-1' : '' }}">
                    {{ $alert['message'] }}
                </div>
            </div>

            <!-- Close Button -->
            <div class="flex-shrink-0">
                <button 
                    @click="
                        show = false;
                        setTimeout(() => {
                            $wire.removeAlert('{{ $alert['id'] }}');
                        }, 300);
                    "
                    class="btn btn-ghost btn-xs btn-circle"
                    aria-label="Close alert"
                >
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>
        </div>
    @endforeach
</div>

<!-- Auto-hide script for alerts -->
<script>
    document.addEventListener('livewire:init', () => {
        Livewire.on('auto-hide-alert', (event) => {
            const { id, delay } = event;
            setTimeout(() => {
                Livewire.dispatch('hideAlert', { alertId: id });
            }, delay);
        });
    });
</script>