<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Dashboard') - {{ config('app.name', 'Laravel') }}</title>
    @vite(array_filter([
        'resources/css/app.css',
        'resources/js/app.js',
        request()->routeIs('scanners.index') ? 'resources/js/scanner.js' : null,
    ]))
</head>

<body class="min-h-screen font-sans antialiased bg-base-200">
    <!-- Theme initialization script -->
    <script>
        // Initialize theme before page renders to prevent flash
        (function () {
            const savedTheme = localStorage.getItem('theme') || 'light';
            document.documentElement.setAttribute('data-theme', savedTheme);
        })();
    </script>
    {{-- NAVBAR mobile only --}}
    <x-nav sticky class="lg:hidden">
        <x-slot:brand>
            <div class="pt-5 ml-5">Asset Management</div>
        </x-slot:brand>
        <x-slot:actions>
            <label for="main-drawer" class="mr-3 lg:hidden">
                <x-icon name="o-bars-3" class="cursor-pointer" />
            </label>
        </x-slot:actions>
    </x-nav>

    {{-- MAIN --}}
    <x-main full-width>
        {{-- SIDEBAR --}}
        <x-slot:sidebar drawer="main-drawer" class="bg-base-100 lg:bg-inherit">
            @include('partials.sidebar')
        </x-slot:sidebar>

        {{-- CONTENT --}}
        <x-slot:content class="!p-0">
            @include('partials.header')
            <main class="flex-1 p-6 bg-base-200">
                @yield('content')
            </main>
        </x-slot:content>
    </x-main>

    {{-- Toast --}}
    <x-toast />

    {{-- Alert Component --}}
    <livewire:alert />

    {{-- Session Flash Messages Handler --}}
    @if(session('success') || session('error'))
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                @if(session('success'))
                    // Dispatch success alert
                    window.dispatchEvent(new CustomEvent('alert', {
                        detail: {
                            type: 'success',
                            message: '{{ session('success') }}'
                        }
                    }));

                    // Close drawer if success
                    window.dispatchEvent(new CustomEvent('closeDrawer'));
                @endif

                @if(session('error'))
                    // Dispatch error alert
                    window.dispatchEvent(new CustomEvent('alert', {
                        detail: {
                            type: 'error',
                            message: '{{ session('error') }}'
                        }
                    }));
                @endif
                    });
        </script>
    @endif
</body>

</html>