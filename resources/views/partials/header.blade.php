<x-nav sticky full-width>
    <x-slot:brand>
        <!-- Mobile menu button -->
        <label for="drawer-toggle" class="btn btn-square btn-ghost lg:hidden">
            <x-icon name="o-bars-3" class="w-6 h-6" />
        </label>

        <div class="hidden flex-1 lg:flex">
            <livewire:breadcrumb-component :pageTitle="$pageTitle ?? null" :pageDescription="$pageDescription ?? null"
                :backRoute="$backRoute ?? null" :showBreadcrumbs="true" />
        </div>
    </x-slot:brand>

    <x-slot:actions>
        <!-- Theme Controller -->
        <x-dropdown>
            <x-slot:trigger>
                <x-button icon="o-swatch" class="btn-ghost btn-circle" />
            </x-slot:trigger>

            <x-menu-item title="☀️ Light" onclick="changeTheme('light')" />
            <x-menu-item title="🌙 Dark" onclick="changeTheme('dark')" />
            <x-menu-item title="🧁 Cupcake" onclick="changeTheme('cupcake')" />
            <x-menu-item title="🏢 Corporate" onclick="changeTheme('corporate')" />
            <x-menu-item title="🌆 Synthwave" onclick="changeTheme('synthwave')" />
            <x-menu-item title="🧛 Dracula" onclick="changeTheme('dracula')" />
        </x-dropdown>

        <!-- User menu -->
        <x-dropdown>
            <x-slot:trigger>
                <x-avatar placeholder="{{ substr(Auth::user()->name, 0, 2) }}" class="!w-8 !h-8 cursor-pointer" />
            </x-slot:trigger>
                <x-menu-item title="Edit Password" icon="o-key" link="/profile/password" />
        </x-dropdown>
    </x-slot:actions>
</x-nav>

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