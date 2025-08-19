<header class="navbar bg-base-100 shadow-sm border-b border-base-300">
    <!-- Mobile menu button -->
    <div class="navbar-start">
        <label for="drawer-toggle" class="btn btn-square btn-ghost lg:hidden">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
            </svg>
        </label>
        
        <div class="hidden lg:flex">
            <h1 class="text-xl font-semibold">Dashboard Overview</h1>
        </div>
    </div>
    
    <!-- Search bar -->
    <div class="navbar-center hidden lg:flex">
        <div class="form-control">
            <div class="input-group">
                <input type="text" placeholder="Search..." class="input input-bordered w-64" />
                <button class="btn btn-square" aria-label="Search">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </button>
            </div>
        </div>
    </div>
    
    <!-- Right side actions -->
    <div class="navbar-end">
        <div class="flex items-center gap-2">
            <!-- Notifications -->
            <div class="dropdown dropdown-end">
                <div tabindex="0" role="button" class="btn btn-ghost btn-circle">
                    <div class="indicator">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM10.07 2.82l3.12 3.12M7.05 5.84l3.12 3.12M4.03 8.86l3.12 3.12M1.01 11.88l3.12 3.12"></path>
                        </svg>
                        <span class="badge badge-xs badge-primary indicator-item">3</span>
                    </div>
                </div>
                <ul tabindex="0" class="dropdown-content z-[1] menu p-2 shadow bg-base-100 rounded-box w-80">
                    <li><a>New user registered</a></li>
                    <li><a>System update available</a></li>
                    <li><a>Backup completed</a></li>
                </ul>
            </div>
            
            <!-- Theme Controller -->
            <div class="dropdown dropdown-end">
                <div tabindex="0" role="button" class="btn btn-ghost btn-circle">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zM21 5H9a2 2 0 00-2 2v12a4 4 0 004 4h10a2 2 0 002-2V7a2 2 0 00-2-2z"></path>
                    </svg>
                </div>
                <ul tabindex="0" class="dropdown-content z-[1] menu p-2 shadow bg-base-100 rounded-box w-52">
                    <li><a onclick="document.documentElement.setAttribute('data-theme', 'light')">Light</a></li>
                    <li><a onclick="document.documentElement.setAttribute('data-theme', 'dark')">Dark</a></li>
                    <li><a onclick="document.documentElement.setAttribute('data-theme', 'cupcake')">Cupcake</a></li>
                    <li><a onclick="document.documentElement.setAttribute('data-theme', 'corporate')">Corporate</a></li>
                </ul>
            </div>
            
            <!-- User menu -->
            <div class="dropdown dropdown-end">
                <div tabindex="0" role="button" class="btn btn-ghost btn-circle">
                    <div class="avatar placeholder">
                        <div class="bg-neutral text-neutral-content rounded-full w-8">
                            <span class="text-xs">U</span>
                        </div>
                    </div>
                </div>
                <ul tabindex="0" class="dropdown-content z-[1] menu p-2 shadow bg-base-100 rounded-box w-52">
                    <li><a>Profile</a></li>
                    <li><a>Settings</a></li>
                    <li><a>Logout</a></li>
                </ul>
            </div>
        </div>
    </div>
</header>

<!-- Breadcrumbs -->
<div class="bg-base-200 border-b border-base-300 px-6 py-2">
    @include('partials.breadcrumbs')
</div>