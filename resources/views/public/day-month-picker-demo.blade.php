<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Demo Day-Month Picker</title>
    @vite('resources/css/app.css')
    @livewireStyles
</head>
<body class="p-6">
    <div class="max-w-xl mx-auto">
        <h1 class="text-lg font-semibold mb-4">Demo Day-Month Picker</h1>
        <p class="mb-3 text-sm text-base-content/70">Komponen untuk memilih <strong>tanggal</strong> dan <strong>bulan</strong> saja.</p>

        <livewire:components.day-month-picker label="Tanggal & Bulan" />

        <div class="mt-6 text-xs text-base-content/60">Halaman demo ini publik dan tidak memerlukan login.</div>
    </div>

    @livewireScripts
    @vite('resources/js/app.js')
</body>
</html>