<aside class="w-80 min-h-full bg-base-100 text-base-content">
    <div class="p-4">
        <h2 class="mb-8 text-2xl font-bold text-primary">Dashboard</h2>
        
        @php
            // Helper function to check multiple route patterns
            function isActiveRoute($routePatterns) {
                $patterns = explode('|', $routePatterns);
                foreach ($patterns as $pattern) {
                    if (request()->routeIs(trim($pattern))) {
                        return true;
                    }
                }
                return false;
            }
            
            $mainMenuItems = [
                [
                    'route' => 'dashboard',
                    'routeCheck' => 'dashboard',
                    'icon' => 'home',
                    'label' => 'Dashboard'
                ],
                [
                    'route' => 'scanners.index',
                    'routeCheck' => 'scanners.*',
                    'icon' => 'scan-line',
                    'label' => 'QR/Barcode Scanner'
                ],
                [
                    'type' => 'dropdown',
                    'icon' => 'package',
                    'label' => 'Asset Management',
                    'routeCheck' => 'assets.*|asset-loans.*|asset-logs.*',
                    'children' => [
                        [
                            'route' => 'assets.index',
                            'routeCheck' => 'assets.*',
                            'icon' => 'package',
                            'label' => 'Assets'
                        ],
                        [
                            'route' => 'asset-loans.index',
                            'routeCheck' => 'asset-loans.*',
                            'icon' => 'clipboard-list',
                            'label' => 'Asset Loans'
                        ],
                        [
                            'route' => 'asset-logs.index',
                            'routeCheck' => 'asset-logs.*',
                            'icon' => 'file-text',
                            'label' => 'Asset Logs'
                        ]
                    ]
                ]
            ];
            
            $masterDataMenuItems = [
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

        <!-- Main Menu -->
        <div class="mb-6">
            <h3 class="mb-3 text-sm font-semibold tracking-wider uppercase text-base-content/70">Main Menu</h3>
            <ul class="w-full menu rounded-box">
                @foreach($mainMenuItems as $item)
                    @if(isset($item['type']) && $item['type'] === 'dropdown')
                        <li>
                            <span class="menu-dropdown-toggle {{ isActiveRoute($item['routeCheck']) ? 'menu-dropdown-show' : '' }} justify-between">
                                <div class="flex gap-2">
                                    <i data-lucide="{{ $item['icon'] }}" class="w-5 h-5"></i>
                                    {{ $item['label'] }}
                                </div>
                            </span>
                            <ul class="menu-dropdown mt-0 {{ isActiveRoute($item['routeCheck']) ? 'menu-dropdown-show' : '' }}">
                                @foreach($item['children'] as $child)
                                    <li>
                                        <a href="{{ route($child['route']) }}" class="{{ request()->routeIs($child['routeCheck']) ? 'bg-primary text-primary-content' : '' }}">
                                            <i data-lucide="{{ $child['icon'] }}" class="w-5 h-5"></i>
                                            {{ $child['label'] }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </li>
                    @else
                        <li>
                            <a href="{{ route($item['route']) }}" class="{{ request()->routeIs($item['routeCheck']) ? 'bg-primary text-primary-content' : '' }}">
                                <i data-lucide="{{ $item['icon'] }}" class="w-5 h-5"></i>
                                {{ $item['label'] }}
                            </a>
                        </li>
                    @endif
                @endforeach
            </ul>
        </div>
        
        <!-- Master Data Menu -->
        <div class="mb-6">
            <h3 class="mb-3 text-sm font-semibold tracking-wider uppercase text-base-content/70">Master Data</h3>
            <ul class="w-full menu rounded-box">
                @foreach($masterDataMenuItems as $item)
                    <li>
                        <a href="{{ route($item['route']) }}" class="{{ request()->routeIs($item['routeCheck']) ? 'bg-primary text-primary-content' : '' }}">
                            <i data-lucide="{{ $item['icon'] }}" class="w-5 h-5"></i>
                            {{ $item['label'] }}
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
        
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

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Handle dropdown toggle clicks
        const dropdownToggles = document.querySelectorAll('.menu-dropdown-toggle');
        
        dropdownToggles.forEach(toggle => {
            toggle.addEventListener('click', function() {
                const dropdown = this.nextElementSibling;
                
                // Toggle the menu-dropdown-show class
                this.classList.toggle('menu-dropdown-show');
                if (dropdown) {
                    dropdown.classList.toggle('menu-dropdown-show');
                }
            });
        });
    });
</script>