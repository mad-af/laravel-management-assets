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
                    <button type="button" wire:click="previousPage('{{ $paginator->getPageName() }}')" wire:loading.attr="disabled" rel="prev" class="join-item btn btn-sm" aria-label="Prev">Prev</button>
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
                    <button type="button" wire:click="gotoPage(1, '{{ $paginator->getPageName() }}')" wire:loading.attr="disabled" class="join-item btn btn-sm">1</button>
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
                        <button type="button" wire:click="gotoPage({{ $page }}, '{{ $paginator->getPageName() }}')" wire:loading.attr="disabled" class="join-item btn btn-sm">{{ $page }}</button>
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
                    <button type="button" wire:click="gotoPage({{ $last }}, '{{ $paginator->getPageName() }}')" wire:loading.attr="disabled" class="join-item btn btn-sm">{{ $last }}</button>
                </li>
            @endif

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <li>
                    <button type="button" wire:click="nextPage('{{ $paginator->getPageName() }}')" wire:loading.attr="disabled" rel="next" class="join-item btn btn-sm" aria-label="Next">Next</button>
                </li>
            @else
                <li>
                    <span class="join-item btn btn-sm btn-disabled" aria-disabled="true" aria-label="Next">Next</span>
                </li>
            @endif
        </ul>
    </nav>
@endif