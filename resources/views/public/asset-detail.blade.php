<!DOCTYPE html>
<html lang="id" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $asset->name }} - Asset Detail</title>
    <link href="https://cdn.jsdelivr.net/npm/daisyui@4.4.19/dist/full.min.css" rel="stylesheet" type="text/css" />
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
    <style>
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }
    </style>
</head>
<body class="bg-base-200 min-h-screen">
    <div class="container mx-auto px-4 py-8 max-w-4xl">
        <!-- Header -->
        <div class="text-center mb-8">
            <div class="flex justify-center items-center gap-3 mb-4">
                <div class="w-12 h-12 bg-primary rounded-full flex items-center justify-center">
                    <i data-lucide="package" class="w-6 h-6 text-primary-content"></i>
                </div>
                <h1 class="text-3xl font-bold text-base-content">Asset Information</h1>
            </div>
            <p class="text-base-content/70">Informasi detail asset perusahaan</p>
        </div>

        <!-- Asset Detail Card -->
        <div class="card bg-base-100 shadow-xl mb-6">
            <div class="card-body">
                <h2 class="card-title text-2xl mb-6 flex items-center gap-2">
                    <i data-lucide="info" class="w-6 h-6"></i>
                    {{ $asset->name }}
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Asset Code -->
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-semibold">Kode Asset</span>
                        </label>
                        <div class="input input-bordered flex items-center gap-2 bg-base-200">
                            <i data-lucide="hash" class="w-4 h-4 opacity-70"></i>
                            <span class="font-mono">{{ $asset->code }}</span>
                        </div>
                    </div>

                    <!-- Tag Code -->
                    @if($asset->tag_code)
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-semibold">Tag Code</span>
                        </label>
                        <div class="input input-bordered flex items-center gap-2 bg-base-200">
                            <i data-lucide="tag" class="w-4 h-4 opacity-70"></i>
                            <span class="font-mono">{{ $asset->tag_code }}</span>
                        </div>
                    </div>
                    @endif

                    <!-- Category -->
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-semibold">Kategori</span>
                        </label>
                        <div class="input input-bordered flex items-center gap-2 bg-base-200">
                            <i data-lucide="folder" class="w-4 h-4 opacity-70"></i>
                            <span>{{ $asset->category->name }}</span>
                        </div>
                    </div>

                    <!-- Branch/Location -->
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-semibold">Lokasi</span>
                        </label>
                        <div class="input input-bordered flex items-center gap-2 bg-base-200">
                            <i data-lucide="map-pin" class="w-4 h-4 opacity-70"></i>
                            <span>{{ $asset->branch->name }}</span>
                        </div>
                    </div>

                    <!-- Company -->
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-semibold">Perusahaan</span>
                        </label>
                        <div class="input input-bordered flex items-center gap-2 bg-base-200">
                            <i data-lucide="building" class="w-4 h-4 opacity-70"></i>
                            <span>{{ $asset->company->name }}</span>
                        </div>
                    </div>

                    <!-- Brand & Model -->
                    @if($asset->brand || $asset->model)
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-semibold">Brand & Model</span>
                        </label>
                        <div class="input input-bordered flex items-center gap-2 bg-base-200">
                            <i data-lucide="cpu" class="w-4 h-4 opacity-70"></i>
                            <span>{{ $asset->brand }} {{ $asset->model }}</span>
                        </div>
                    </div>
                    @endif

                    <!-- Status -->
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-semibold">Status</span>
                        </label>
                        <div class="flex items-center gap-2">
                            <span class="badge {{ $asset->status_badge_color }} badge-lg">
                                {{ ucfirst(str_replace('_', ' ', $asset->status->value)) }}
                            </span>
                        </div>
                    </div>

                    <!-- Condition -->
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-semibold">Kondisi</span>
                        </label>
                        <div class="flex items-center gap-2">
                            <span class="badge {{ $asset->condition_badge_color }} badge-lg">
                                {{ $asset->condition->label() }}
                            </span>
                        </div>
                    </div>

                    <!-- Purchase Date -->
                    @if($asset->purchase_date)
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-semibold">Tanggal Pembelian</span>
                        </label>
                        <div class="input input-bordered flex items-center gap-2 bg-base-200">
                            <i data-lucide="calendar" class="w-4 h-4 opacity-70"></i>
                            <span>{{ $asset->purchase_date->format('d M Y') }}</span>
                        </div>
                    </div>
                    @endif

                    <!-- Value -->
                    @if($asset->value)
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-semibold">Nilai Asset</span>
                        </label>
                        <div class="input input-bordered flex items-center gap-2 bg-base-200">
                            <i data-lucide="dollar-sign" class="w-4 h-4 opacity-70"></i>
                            <span class="font-semibold text-success">{{ $asset->formatted_value }}</span>
                        </div>
                    </div>
                    @endif
                </div>

                <!-- Description -->
                @if($asset->description)
                <div class="form-control mt-6">
                    <label class="label">
                        <span class="label-text font-semibold">Deskripsi</span>
                    </label>
                    <div class="textarea textarea-bordered bg-base-200 min-h-20">
                        {{ $asset->description }}
                    </div>
                </div>
                @endif

                <!-- Current Loan Information -->
                @if($asset->currentLoan->isNotEmpty())
                <div class="alert alert-info mt-6">
                    <i data-lucide="user-check" class="w-5 h-5"></i>
                    <div>
                        <h3 class="font-bold">Asset Sedang Dipinjam</h3>
                        <div class="text-xs">
                            Dipinjam oleh: <strong>{{ $asset->currentLoan->first()->employee->name }}</strong><br>
                            Tanggal Pinjam: {{ $asset->currentLoan->first()->checkout_at->format('d M Y H:i') }}
                        </div>
                    </div>
                </div>
                @endif

                <!-- Vehicle Profile Information -->
                @if($asset->vehicleProfile)
                <div class="card bg-base-200 mt-6">
                    <div class="card-body">
                        <h3 class="card-title text-lg flex items-center gap-2">
                            <i data-lucide="car" class="w-5 h-5"></i>
                            Informasi Kendaraan
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                            @if($asset->vehicleProfile->plate_no)
                            <div>
                                <span class="text-sm font-semibold opacity-70">Nomor Plat:</span>
                                <p class="font-mono">{{ $asset->vehicleProfile->plate_no }}</p>
                            </div>
                            @endif
                            @if($asset->vehicleProfile->year_manufacture)
                            <div>
                                <span class="text-sm font-semibold opacity-70">Tahun Produksi:</span>
                                <p>{{ $asset->vehicleProfile->year_manufacture }}</p>
                            </div>
                            @endif
                            @if($asset->vehicleProfile->current_odometer_km)
                            <div>
                                <span class="text-sm font-semibold opacity-70">Odometer:</span>
                                <p>{{ number_format($asset->vehicleProfile->current_odometer_km) }} km</p>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Footer -->
        <div class="text-center text-base-content/50 text-sm">
            <p>© {{ date('Y') }} Asset Management System</p>
            <p class="mt-1">Informasi ini bersifat rahasia dan hanya untuk keperluan internal perusahaan</p>
        </div>
    </div>

    <script>
        // Initialize Lucide icons
        lucide.createIcons();
    </script>
</body>
</html>