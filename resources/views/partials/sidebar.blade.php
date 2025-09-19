@php
    $mainMenuItems = [
        [
            'route' => 'dashboard',
            'icon' => 'o-home',
            'label' => 'Dashboard'
        ],
        [
            'route' => 'maintenances.index',
            'icon' => 'o-wrench-screwdriver',
            'label' => 'Asset Maintenance'
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
                    'route' => 'asset-transfers.index',
                    'icon' => 'o-arrow-path',
                    'label' => 'Asset Transfers'
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
                ],
                [
                    'route' => 'vehicles.index',
                    'icon' => 'o-truck',
                    'label' => 'Vehicles'
                ]
            ]
        ],
        [
            'route' => 'scanners.index',
            'icon' => 'o-qr-code',
            'label' => 'QR/Barcode Scanner'
        ],
    ];

    $masterDataMenuItems = [
        [
            'route' => 'users.index',
            'icon' => 'o-users',
            'label' => 'User Management'
        ],
        [
            'route' => 'companies.index',
            'icon' => 'o-building-office',
            'label' => 'Companies'
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

<div class="flex flex-col h-full border-r border-base-content/10 bg-base-100">
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
                        onclick="logout()" />
                </x-slot:actions>
            </x-list-item>
        @endif
    </div>
</div>


<script>
    function logout() {
        // Create a form dynamically and submit it
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("logout") }}';

        // Add CSRF token
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        form.appendChild(csrfToken);

        // Add to body and submit
        document.body.appendChild(form);
        form.submit();
    }
</script>