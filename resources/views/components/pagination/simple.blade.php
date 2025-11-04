@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination Navigation" class="flex justify-end items-center">
        <ul class="join">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <li>
                    <span class="join-item btn btn-sm btn-disabled" aria-disabled="true" aria-label="Prev">Prev</span>
                </li>
            @else
                <li>
                    <a href="{{ request()->fullUrlWithQuery(['page' => $paginator->currentPage() - 1]) }}" rel="prev" class="join-item btn btn-sm" aria-label="Prev">Prev</a>
                </li>
            @endif

            {{-- Pagination Elements (condensed window) --}}
            @php
                $current = $paginator->currentPage();
                $last = $paginator->lastPage();
                $window = 2; // show current ±2 pages
                $start = max(1, $current - $window);
                $end = min($last, $current + $window);
            @endphp

            {{-- First page shortcut --}}
            @if ($start > 1)
                <li>
                    <a href="{{ request()->fullUrlWithQuery(['page' => 1]) }}" class="join-item btn btn-sm">1</a>
                </li>
                @if ($start > 2)
                    <li>
                        <span class="join-item btn btn-sm btn-ghost" aria-hidden="true">&hellip;</span>
                    </li>
                @endif
            @endif

            {{-- Window around current page --}}
            @for ($page = $start; $page <= $end; $page++)
                @if ($page == $current)
                    <li>
                        <span class="join-item btn btn-sm btn-active" aria-current="page">{{ $page }}</span>
                    </li>
                @else
                    <li>
                        <a href="{{ request()->fullUrlWithQuery(['page' => $page]) }}" class="join-item btn btn-sm">{{ $page }}</a>
                    </li>
                @endif
            @endfor

            {{-- Last page shortcut --}}
            @if ($end < $last)
                @if ($end < $last - 1)
                    <li>
                        <span class="join-item btn btn-sm btn-ghost" aria-hidden="true">&hellip;</span>
                    </li>
                @endif
                <li>
                    <a href="{{ request()->fullUrlWithQuery(['page' => $last]) }}" class="join-item btn btn-sm">{{ $last }}</a>
                </li>
            @endif

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <li>
                    <a href="{{ request()->fullUrlWithQuery(['page' => $paginator->currentPage() + 1]) }}" rel="next" class="join-item btn btn-sm" aria-label="Next">Next</a>
                </li>
            @else
                <li>
                    <span class="join-item btn btn-sm btn-disabled" aria-disabled="true" aria-label="Next">Next</span>
                </li>
            @endif
        </ul>
    </nav>
@endif