<div wire:key="simple-{{ $current }}-{{ $last }}">
    @if ($last > 1)
        <nav role="navigation" aria-label="Pagination Navigation" class="flex justify-end items-center">
            <ul class="join">
                {{-- Previous Page Link --}}
                @if ($current <= 1)
                    <li>
                        <span class="join-item btn btn-sm btn-disabled" aria-disabled="true" aria-label="Prev">Prev</span>
                    </li>
                @else
                    <li>
                        <a href="{{ '?'.http_build_query(array_merge(request()->query(), ['page' => $current - 1])) }}" rel="prev" class="join-item btn btn-sm" aria-label="Prev">Prev</a>
                    </li>
                @endif

                {{-- First page shortcut --}}
                @if ($start > 1)
                    <li>
                        <a href="{{ '?'.http_build_query(array_merge(request()->query(), ['page' => 1])) }}" class="join-item btn btn-sm">1</a>
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
                            <a href="{{ '?'.http_build_query(array_merge(request()->query(), ['page' => $page])) }}" class="join-item btn btn-sm">{{ $page }}</a>
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
                        <a href="{{ '?'.http_build_query(array_merge(request()->query(), ['page' => $last])) }}" class="join-item btn btn-sm">{{ $last }}</a>
                    </li>
                @endif

                {{-- Next Page Link --}}
                @if ($current < $last)
                    <li>
                        <a href="{{ '?'.http_build_query(array_merge(request()->query(), ['page' => $current + 1])) }}" rel="next" class="join-item btn btn-sm" aria-label="Next">Next</a>
                    </li>
                @else
                    <li>
                        <span class="join-item btn btn-sm btn-disabled" aria-disabled="true" aria-label="Next">Next</span>
                    </li>
                @endif
            </ul>
        </nav>
    @endif
</div>