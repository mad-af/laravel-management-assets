<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Auth') - {{ config('app.name', 'Laravel') }}</title>
    @vite(array_filter([
        'resources/css/app.css',
        'resources/js/app.js',
    ]))
</head>
<body class="font-sans antialiased bg-base-200">
    <!-- Theme initialization script -->
    <script>
        // Initialize theme before page renders to prevent flash
        (function() {
            const savedTheme = localStorage.getItem('theme') || 'light';
            document.documentElement.setAttribute('data-theme', savedTheme);
        })();
    </script>
    
    <div class="flex justify-center items-center p-4 min-h-screen">
        <div class="w-full max-w-md">
            @yield('content')
        </div>
    </div>

    <x-toast />
    <livewire:toast-listener />
</body>
</html>