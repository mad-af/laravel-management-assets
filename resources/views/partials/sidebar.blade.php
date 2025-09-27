@php
    $mainMenuItems = [
        [
            'route' => 'dashboard',
            'icon' => 'o-home',
            'label' => 'Beranda'
        ],
        [
            'route' => 'scanners.index',
            'icon' => 'o-qr-code',
            'label' => 'Pemindai QR/Barcode'
        ],
        [
            'type' => 'submenu',
            'icon' => 'o-cube',
            'label' => 'Manajemen Aset',
            'children' => [
                [
                    'route' => 'assets.index',
                    'icon' => 'o-cube',
                    'label' => 'Daftar Aset'
                ],
                [
                    'route' => 'asset-transfers.index',
                    'icon' => 'o-arrow-path',
                    'label' => 'Transfer Aset'
                ],
                [
                    'route' => 'asset-loans.index',
                    'icon' => 'o-clipboard-document-list',
                    'label' => 'Peminjaman Aset'
                ],
                [
                    'route' => 'asset-logs.index',
                    'icon' => 'o-document-text',
                    'label' => 'Log Aset'
                ],
            ]
        ],
        [
            'route' => 'maintenances.index',
            'icon' => 'o-wrench-screwdriver',
            'label' => 'Perawatan'
        ],
        [
            'route' => 'vehicles.index',
            'icon' => 'o-truck',
            'label' => 'Kendaraan'
        ],
    ];

    $masterDataMenuItems = [
        [
            'route' => 'companies.index',
            'icon' => 'o-building-office',
            'label' => 'Perusahaan'
        ],
        [
            'route' => 'branches.index',
            'icon' => 'o-building-office-2',
            'label' => 'Cabang'
        ],
        [
            'route' => 'users.index',
            'icon' => 'o-users',
            'label' => 'Manajemen Pengguna'
        ],
        [
            'route' => 'categories.index',
            'icon' => 'o-folder',
            'label' => 'Kategori'
        ],
    ];
@endphp

<div class="flex flex-col h-full border-r border-base-content/10 bg-base-100">
    {{-- Brand --}}
    <div class="p-4 border-b border-base-content/10">
        <h2 class="text-2xl font-bold text-primary">Dashboard</h2>
    </div>

    {{-- Menu Utama --}}
    <x-menu activate-by-route>
        <x-menu title="Menu Utama" />

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

        <x-menu-separator title="Data Master" />

        @foreach($masterDataMenuItems as $item)
            <x-menu-item title="{{ $item['label'] }}" icon="{{ $item['icon'] }}" link="{{ route($item['route']) }}" />
        @endforeach
    </x-menu>

    {{-- Info Pengguna - Tetap di bawah --}}
    <div class="p-4 mt-auto border-base-content/10">
        @if($user = auth()->user())
            <x-menu-separator />
            <x-list-item :item="$user" value="name" sub-value="email" no-separator no-hover class="-mx-2 !-my-2 rounded">
                <x-slot:actions>
                    <x-button icon="o-power" class="btn-circle btn-ghost btn-xs" tooltip-left="Keluar" no-wire-navigate
                        onclick="logout()" />
                </x-slot:actions>
            </x-list-item>
        @endif
    </div>
</div>

<script>
    function logout() {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("logout") }}';

        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        form.appendChild(csrfToken);

        document.body.appendChild(form);
        form.submit();
    }
</script>
