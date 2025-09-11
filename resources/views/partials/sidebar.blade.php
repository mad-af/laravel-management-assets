@php
    $mainMenuItems = [
        [
            'route' => 'dashboard',
            'icon' => 'o-home',
            'label' => 'Dashboard'
        ],
        [
            'route' => 'scanners.index',
            'icon' => 'o-qr-code',
            'label' => 'QR/Barcode Scanner'
        ],
        [
            'type' => 'submenu',
            'icon' => 'o-cube',
            'label' => 'Asset Management',
            'children' => [
                [
                    'route' => 'assets.index',
                    'icon' => 'o-cube',
                    'label' => 'Assets'
                ],
                [
                    'route' => 'asset-loans.index',
                    'icon' => 'o-clipboard-document-list',
                    'label' => 'Asset Loans'
                ],
                [
                    'route' => 'asset-logs.index',
                    'icon' => 'o-document-text',
                    'label' => 'Asset Logs'
                ]
            ]
        ]
    ];

    $masterDataMenuItems = [
        [
            'route' => 'users.index',
            'icon' => 'o-users',
            'label' => 'User Management'
        ],
        [
            'route' => 'categories.index',
            'icon' => 'o-folder',
            'label' => 'Categories'
        ],
        [
            'route' => 'locations.index',
            'icon' => 'o-map-pin',
            'label' => 'Locations'
        ]
    ];
@endphp

<div class="flex flex-col h-full border-r border-base-content/10">
    {{-- Brand --}}
    <div class="p-4 border-b border-base-content/10">
        <h2 class="text-2xl font-bold text-primary">Dashboard</h2>
    </div>

    {{-- Main Menu --}}
    <x-menu activate-by-route>
        <x-menu title="Main Menu" />

        @foreach($mainMenuItems as $item)
            @if(isset($item['type']) && $item['type'] === 'submenu')
                <x-menu-sub title="{{ $item['label'] }}" icon="{{ $item['icon'] }}">
                    @foreach($item['children'] as $child)
                        <x-menu-item title="{{ $child['label'] }}" icon="{{ $child['icon'] }}"
                            link="{{ route($child['route']) }}" />
                    @endforeach
                </x-menu-sub>
            @else
                <x-menu-item title="{{ $item['label'] }}" icon="{{ $item['icon'] }}" link="{{ route($item['route']) }}" />
            @endif
        @endforeach

        <x-menu-separator title="Master Data" />

        @foreach($masterDataMenuItems as $item)
            <x-menu-item title="{{ $item['label'] }}" icon="{{ $item['icon'] }}" link="{{ route($item['route']) }}" />
        @endforeach
    </x-menu>

    {{-- User Info - Fixed at bottom --}}
    <div class="p-4 mt-auto border-base-content/10">
        @if($user = auth()->user())
            <x-menu-separator />
            <x-list-item :item="$user" value="name" sub-value="email" no-separator no-hover class="-mx-2 !-my-2 rounded">
                <x-slot:actions>
                    <x-button icon="o-power" class="btn-circle btn-ghost btn-xs" tooltip-left="Logout" no-wire-navigate
                        link="/logout" />
                </x-slot:actions>
            </x-list-item>
        @endif
    </div>
</div>