@php
    $segments = request()->segments();
    $breadcrumbs = [];
    
    // Add Home breadcrumb
    $breadcrumbs[] = ['name' => 'Home', 'url' => route('dashboard'), 'active' => false];
    
    $currentPath = '';
    foreach ($segments as $index => $segment) {
        $currentPath .= '/' . $segment;
        $name = ucfirst($segment);
        
        // Skip adding "Dashboard" to breadcrumbs since Home already points to dashboard
        if (strtolower($segment) !== 'dashboard') {
            $isLast = ($index === count($segments) - 1);
            $breadcrumbs[] = [
                'name' => $name,
                'url' => $isLast ? null : url($currentPath),
                'active' => $isLast
            ];
        }
    }
@endphp

<div class="breadcrumbs text-sm">
    <ul>
        @foreach ($breadcrumbs as $index => $crumb)
            <li>
                @if ($crumb['active'] || !$crumb['url'])
                    <!-- Current page - not clickable -->
                    <span class="inline-flex items-center gap-2">
                        @if ($index === 0)
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                            </svg>
                        @endif
                        {{ $crumb['name'] }}
                    </span>
                @else
                    <!-- Clickable breadcrumb -->
                    <a href="{{ $crumb['url'] }}" class="inline-flex items-center gap-2 hover:text-primary transition-colors">
                        @if ($index === 0)
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                            </svg>
                        @endif
                        {{ $crumb['name'] }}
                    </a>
                @endif
            </li>
        @endforeach
    </ul>
</div>