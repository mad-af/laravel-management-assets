<!DOCTYPE html>
<html lang="en" data-theme="cupcake">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') - Laravel Dashboard</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <div class="drawer lg:drawer-open">
        <input id="drawer-toggle" type="checkbox" class="drawer-toggle" />
        
        <!-- Page content -->
        <div class="drawer-content flex flex-col">
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