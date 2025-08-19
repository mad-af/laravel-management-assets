<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') - Laravel Dashboard</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <!-- Lucide Icons CDN -->
    @if(config('app.debug'))
        <!-- Development version (unminified) -->
        <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
    @else
        <!-- Production version (minified) -->
        <script src="https://unpkg.com/lucide@latest"></script>
    @endif
</head>

<body>
    <div class="drawer lg:drawer-open">
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
</body>

</html>