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

<body class="min-h-screen bg-base-200">
    <div class="container px-4 py-8 mx-auto max-w-4xl">
        <!-- Header -->
        <div class="mb-8 text-center">
            <div class="flex gap-3 justify-center items-center mb-4">
                <div class="flex justify-center items-center w-12 h-12 rounded-full bg-primary">
                    <x-icon name="o-cube" class="w-6 h-6 text-primary-content" />
                </div>
                <h1 class="text-3xl font-bold text-base-content">@yield('header-title', 'Asset Information')</h1>
            </div>
            <p class="text-base-content/70">@yield('header-description', 'Informasi detail asset perusahaan')</p>
        </div>

        <!-- Content -->
        @yield('content')

        <!-- Footer -->
        <div class="mt-8 text-sm text-center text-base-content/50">
            <p>© {{ date('Y') }} Asset Management System</p>
            <p class="mt-1">Informasi ini bersifat rahasia dan hanya untuk keperluan internal perusahaan</p>
        </div>
    </div>

</body>
</html>