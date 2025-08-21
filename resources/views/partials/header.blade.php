<header class="border-b shadow-sm navbar bg-base-100 border-base-300">
    <!-- Mobile menu button -->
    <div class="navbar-start">
        <label for="drawer-toggle" class="btn btn-square btn-ghost lg:hidden">
            <i data-lucide="menu" class="w-6 h-6"></i>
        </label>

        <div class="hidden lg:flex">
            <h1 class="text-xl font-semibold">Dashboard Overview</h1>
        </div>
    </div>

    <!-- Right side actions -->
    <div class="navbar-end">
        <div class="flex gap-2 items-center">

            <!-- Theme Controller -->
            <div class="dropdown dropdown-end">
                <div tabindex="0" role="button" class="btn btn-ghost btn-circle">
                    <i data-lucide="palette" class="w-5 h-5"></i>
                </div>
                <ul tabindex="0"
                    class="dropdown-content menu bg-base-100 rounded-box z-[1] w-48 p-2 shadow-2xl border border-base-300">
                    <li><a class="theme-option" onclick="changeTheme('light')">☀️ Light</a></li>
                    <li><a class="theme-option" onclick="changeTheme('dark')">🌙 Dark</a></li>
                    <li><a class="theme-option" onclick="changeTheme('cupcake')">🧁 Cupcake</a></li>
                    <li><a class="theme-option" onclick="changeTheme('corporate')">🏢 Corporate</a></li>
                    <li><a class="theme-option" onclick="changeTheme('synthwave')">🌆 Synthwave</a></li>
                    <li><a class="theme-option" onclick="changeTheme('dracula')">🧛 Dracula</a></li>
                </ul>
            </div>

            <!-- User menu -->
            <div class="dropdown dropdown-end">
                <div tabindex="0" role="button" class="btn btn-ghost btn-circle">
                    @auth
                        <x-avatar initials="{{ substr(Auth::user()->name, 0, 2) }}" size="sm" placeholder="true" />
                    @else
                        <x-avatar initials="U" size="sm" placeholder="true" />
                    @endauth
                </div>
                <ul tabindex="0"
                    class="dropdown-content z-[1] menu p-0 shadow-lg bg-base-100 rounded-box w-64 border border-base-300">
                    @auth
                        <!-- User Info Header -->
                        {{-- <div class="px-4 py-3 bg-base-200 rounded-t-box">
                            <div class="flex items-center space-x-3">
                                <div class="avatar placeholder">
                                    <div class="w-10 rounded-full bg-primary text-primary-content">
                                        <span class="text-sm font-semibold">{{ substr(Auth::user()->name, 0, 2) }}</span>
                                    </div>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-semibold truncate text-base-content">{{ Auth::user()->name }}</p>
                                    <p class="text-xs truncate text-base-content/70">{{ Auth::user()->email }}</p>
                                    <div class="mt-1 badge badge-sm badge-outline">{{ ucfirst(Auth::user()->role ?? 'User')
                                        }}</div>
                                </div>
                            </div>
                        </div> --}}

                        <!-- Menu Items -->
                        <div class="p-2">

                            <li>
                                <a href="#" class="flex items-center px-3 py-2 text-sm rounded-lg hover:bg-base-200">
                                    <i data-lucide="help-circle" class="mr-3 w-4 h-4"></i>
                                    <span>Help & Support</span>
                                </a>
                            </li>

                            <div class="my-0 divider"></div>

                            <li>
                                <a href="#" onclick="logout()"
                                    class="flex items-center px-3 py-2 text-sm rounded-lg transition-colors text-error hover:bg-error/10">
                                    <i data-lucide="log-out" class="mr-3 w-4 h-4"></i>
                                    <span>Sign Out</span>
                                </a>
                            </li>
                        </div>
                    @else
                        <div class="p-2">
                            <li>
                                <a href="{{ route('login') }}"
                                    class="flex items-center px-3 py-2 text-sm rounded-lg hover:bg-base-200">
                                    <i data-lucide="log-in" class="mr-3 w-4 h-4"></i>
                                    <span>Sign In</span>
                                </a>
                            </li>
                        </div>
                    @endauth
                </ul>
            </div>
        </div>
    </div>
</header>

<!-- Breadcrumbs -->
<div class="px-6 py-2 border-b bg-base-200 border-base-300">
    @include('partials.breadcrumbs')
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