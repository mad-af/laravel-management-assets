<header class="navbar bg-base-100 shadow-sm border-b border-base-300">
    <!-- Mobile menu button -->
    <div class="navbar-start">
        <label for="drawer-toggle" class="btn btn-square btn-ghost lg:hidden">
            <i data-lucide="menu" class="w-6 h-6"></i>
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
                    <i data-lucide="search" class="w-5 h-5"></i>
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
                        <i data-lucide="bell" class="w-5 h-5"></i>
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
                    <i data-lucide="palette" class="w-5 h-5"></i>
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