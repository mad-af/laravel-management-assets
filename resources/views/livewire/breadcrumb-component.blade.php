<div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
    <!-- Page Title Section -->
    @if($pageTitle)
        <div class="flex gap-4 items-center">
            @if($backRoute)
                <a href="{{ $backRoute }}" class="btn btn-ghost btn-sm">
                    <x-icon name="o-arrow-left" class="mr-2 w-4 h-4" />
                </a>
            @endif
            <div>
                <h1 class="text-3xl font-bold text-base-content">{{ $pageTitle }}</h1>
                @if($pageDescription)
                    <p class="mt-1 text-base-content/70">{{ $pageDescription }}</p>
                @endif
            </div>
        </div>
    @endif

    <!-- Breadcrumbs Section -->
    @if($showBreadcrumbs)
        <div class="breadcrumbs text-sm">
            <ul>
                @foreach ($breadcrumbs as $index => $crumb)
                    <li>
                        @if ($crumb['active'] || !$crumb['url'])
                            <!-- Current page - not clickable -->
                            <span class="inline-flex items-center gap-2">
                                @if ($index === 0)
                                    <x-icon name="o-home" class="h-4 w-4" />
                                @endif
                                {{ $crumb['name'] }}
                            </span>
                        @else
                            <!-- Clickable breadcrumb -->
                            <a href="{{ $crumb['url'] }}" class="inline-flex items-center gap-2 hover:text-primary transition-colors">
                                @if ($index === 0)
                                    <x-icon name="o-home" class="h-4 w-4" />
                                @endif
                                {{ $crumb['name'] }}
                            </a>
                        @endif
                    </li>
                @endforeach
            </ul>
        </div>
    @endif
</div>
