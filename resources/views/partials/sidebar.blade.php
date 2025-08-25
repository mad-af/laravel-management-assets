<aside class="w-80 min-h-full bg-base-100 text-base-content">
    <div class="p-4">
        <h2 class="mb-8 text-2xl font-bold text-primary">Dashboard</h2>
        
        @php
            $menuItems = [
                [
                    'route' => 'dashboard',
                    'routeCheck' => 'dashboard',
                    'icon' => 'home',
                    'label' => 'Dashboard'
                ],
                // [
                //     'route' => 'dashboard.tables',
                //     'routeCheck' => 'dashboard.tables',
                //     'icon' => 'table',
                //     'label' => 'Tables'
                // ],
                // [
                //     'route' => 'dashboard.forms',
                //     'routeCheck' => 'dashboard.forms',
                //     'icon' => 'file-text',
                //     'label' => 'Forms'
                // ],
                // [
                //     'route' => 'dashboard.components',
                //     'routeCheck' => 'dashboard.components',
                //     'icon' => 'puzzle',
                //     'label' => 'Components'
                // ],
                // [
                //     'route' => 'dashboard.settings',
                //     'routeCheck' => 'dashboard.settings',
                //     'icon' => 'settings',
                //     'label' => 'Settings'
                // ],
                [
                    'route' => 'scanners.index',
                    'routeCheck' => 'scanners.*',
                    'icon' => 'scan-line',
                    'label' => 'QR/Barcode Scanner'
                ],
                [
                    'route' => 'assets.index',
                    'routeCheck' => 'assets.*',
                    'icon' => 'package',
                    'label' => 'Asset Management'
                ],
                [
                    'route' => 'asset-logs.index',
                    'routeCheck' => 'asset-logs.*',
                    'icon' => 'file-text',
                    'label' => 'Asset Logs'
                ],
                [
                    'route' => 'users.index',
                    'routeCheck' => 'users.*',
                    'icon' => 'users',
                    'label' => 'User Management'
                ],
                [
                    'route' => 'categories.index',
                    'routeCheck' => 'categories.*',
                    'icon' => 'folder',
                    'label' => 'Categories'
                ],
                [
                    'route' => 'locations.index',
                    'routeCheck' => 'locations.*',
                    'icon' => 'map-pin',
                    'label' => 'Locations'
                ]
            ];
        @endphp

        <ul class="p-0 w-full menu">
            @foreach($menuItems as $item)
                <li>
                    <a href="{{ route($item['route']) }}" 
                       class="flex items-center gap-3 p-3 rounded-lg transition-colors {{ request()->routeIs($item['routeCheck']) ? 'bg-primary text-primary-content' : 'hover:bg-base-200' }}">
                        <i data-lucide="{{ $item['icon'] }}" class="w-5 h-5"></i>
                        <span class="font-medium">{{ $item['label'] }}</span>
                    </a>
                </li>
            @endforeach
        </ul>
        
        <div class="divider"></div>
        
        <div class="p-3">
            <div class="flex gap-3 items-center">
                @auth
                    <x-avatar initials="{{ substr(Auth::user()->name, 0, 2) }}" size="md" placeholder="true" />
                    <div>
                        <p class="font-medium">{{ Auth::user()->name }}</p>
                        <p class="text-sm opacity-70">{{ ucfirst(Auth::user()->role ?? 'User') }}</p>
                    </div>
                @else
                    <x-avatar initials="U" size="md" placeholder="true" />
                    <div>
                        <p class="font-medium">Guest</p>
                        <p class="text-sm opacity-70">Not logged in</p>
                    </div>
                @endauth
            </div>
        </div>
    </div>
</aside>