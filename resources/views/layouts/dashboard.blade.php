<!DOCTYPE html>
<html lang="en" data-theme="light">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') - Laravel Dashboard</title>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>
    <div class="drawer lg:drawer-open">
        <!-- Theme initialization script -->
        <script>
            // Initialize theme before page renders to prevent flash
            (function() {
                const savedTheme = localStorage.getItem('theme') || 'light';
                document.documentElement.setAttribute('data-theme', savedTheme);
            })();
        </script>
        <input id="drawer-toggle" type="checkbox" class="drawer-toggle" />

        <!-- Page content -->
        <div class="flex flex-col drawer-content">
            <!-- Header -->
            @include('partials.header')

            <!-- Main content -->
            <main class="flex-1 p-6 bg-base-200">
                @yield('content')
            </main>
        </div>

        <!-- Sidebar -->
        <div class="drawer-side">
            <label for="drawer-toggle" class="drawer-overlay"></label>
            @include('partials.sidebar')
        </div>
    </div>
    
    <!-- Lucide Icons CDN - Load after DOM -->
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
    <script>
        // Initialize Lucide icons after DOM is loaded
        document.addEventListener('DOMContentLoaded', function() {
            lucide.createIcons();
        });
    </script>
</body>

</html>