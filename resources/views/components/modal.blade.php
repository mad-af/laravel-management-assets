@props([
    'id' => 'modal',
    'title' => null,
    'size' => 'md',
    'backdrop' => true,
    'closable' => true,
    'open' => false,
    'class' => ''
])

@php
    $modalClasses = ['modal'];
    if ($open) $modalClasses[] = 'modal-open';
    if ($class) $modalClasses[] = $class;
    
    $boxClasses = ['modal-box'];
    if ($size === 'sm') $boxClasses[] = 'w-11/12 max-w-md';
    elseif ($size === 'lg') $boxClasses[] = 'w-11/12 max-w-5xl';
    elseif ($size === 'xl') $boxClasses[] = 'w-11/12 max-w-7xl';
    elseif ($size === 'full') $boxClasses[] = 'w-11/12 max-w-none h-full';
@endphp

<dialog id="{{ $id }}" class="{{ implode(' ', $modalClasses) }}">
    <div class="{{ implode(' ', $boxClasses) }}">
        @if($title || $closable)
            <div class="flex justify-between items-center mb-4">
                @if($title)
                    <h3 class="font-bold text-lg">{{ $title }}</h3>
                @endif
                
                @if($closable)
                    <form method="dialog">
                        <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
                    </form>
                @endif
            </div>
        @endif
        
        <div class="modal-content">
            {{ $slot }}
        </div>
        
        @isset($actions)
            <div class="modal-action">
                {{ $actions }}
            </div>
        @endisset
    </div>
    
    @if($backdrop)
        <form method="dialog" class="modal-backdrop">
            <button>close</button>
        </form>
    @endif
</dialog>

@push('scripts')
<script>
    function openModal(modalId) {
        document.getElementById(modalId).showModal();
    }
    
    function closeModal(modalId) {
        document.getElementById(modalId).close();
    }
</script>
@endpush