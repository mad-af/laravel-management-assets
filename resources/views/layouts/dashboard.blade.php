<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Dashboard') - {{ config('app.name', 'Laravel') }}</title>
    @vite(array_filter([
        'resources/css/app.css',
        'resources/js/app.js',
    ]))
</head>

<body class="min-h-screen font-sans antialiased bg-base-200" data-route="{{ Route::currentRouteName() }}">
    <!-- Theme initialization script -->
    <script>
        // Initialize theme before page renders to prevent flash
        (function () {
            const savedTheme = localStorage.getItem('theme') || 'light';
            document.documentElement.setAttribute('data-theme', savedTheme);
        })();
    </script>

    {{-- MAIN --}}
    <x-main full-width>
        {{-- SIDEBAR --}}
        <x-slot:sidebar drawer="main-drawer" class="bg-base-100 lg:bg-inherit">
            @include('partials.sidebar')
        </x-slot:sidebar>

        {{-- CONTENT --}}
        <x-slot:content class="!p-0">
            @include('partials.header')
            <main class="flex-1 p-6 space-y-4 bg-base-200">
                @yield('content')
            </main>
        </x-slot:content>
    </x-main>

    {{-- Toast --}}
    <x-toast />
    {{-- Global Toast Listener --}}
    <livewire:toast-listener />

    {{-- Vehicle Profile Alert Component --}}
    <livewire:vehicle-profile-alert />

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