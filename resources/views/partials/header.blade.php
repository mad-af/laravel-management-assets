<x-nav sticky full-width>
    <x-slot:brand>
        <!-- Mobile menu button -->
        <label for="main-drawer" class="btn btn-square btn-ghost lg:hidden">
            <x-icon name="o-bars-3" class="w-6 h-6" />
        </label>

        <div class="hidden flex-1 lg:flex">
            <livewire:breadcrumb-component :pageTitle="$pageTitle ?? null" :pageDescription="$pageDescription ?? null"
                :backRoute="$backRoute ?? null" :showBreadcrumbs="true" />
        </div>
    </x-slot:brand>

    <x-slot:actions>

        <!-- Branch Controller -->
        <livewire:branch-switcher />

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
                <x-avatar placeholder="{{ substr(Auth::user()->name, 0, 2) }}"
                    class="!w-8 !h-8 cursor-pointer bg-primary" />
            </x-slot:trigger>
            <x-menu-item title="Edit Password" icon="o-key" @click.stop="$dispatch('open-password-modal')" />
        </x-dropdown>
        
        <!-- Password Modal Component -->
        <livewire:profile.password-modal />
    </x-slot:actions>


</x-nav>