<!doctype html>
<html lang="id" data-theme="winter">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>MVP Asset Management — Bento</title>
    <meta name="description"
        content="Landing page MVP untuk sistem manajemen aset. Desain bento, compact, tanpa angka, 1 tombol menuju login." />
    <!-- Tailwind + DaisyUI CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/daisyui@4.12.10/dist/full.min.css" rel="stylesheet" />
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        /* Responsive design untuk mobile */
        html,
        body {
            height: 100%;
            overflow-x: hidden;
        }

        /* Grid bento: tinggi baris responsif berbasis viewport */
        .bento-grid {
            grid-auto-rows: var(--row-h, 280px);
        }

        /* Mobile optimizations */
        @media (max-width: 768px) {
            .bento-grid {
                grid-auto-rows: auto;
                min-height: auto;
            }
            
            html, body {
                overflow: auto;
            }
            
            .mobile-scroll {
                height: auto;
                min-height: 100vh;
            }
        }
    </style>
</head>

<body class="bg-base-200 text-base-content">

    <!-- Main (Navbar melayang di dalam container yang sama dengan bento) -->
    <main class="container relative px-4 pt-4 mx-auto mobile-scroll">
        <!-- Navbar melayang: lebar mengikuti container/bento -->
        <div class="sticky top-3 z-30">
            <nav class="rounded-2xl border shadow-sm backdrop-blur navbar bg-base-100/80 border-base-200">
                <div class="flex gap-2 justify-between items-center py-2 w-full">
                    <div class="flex gap-2 md:gap-3 items-center">
                        <span class="inline-flex justify-center items-center w-8 h-8 md:w-9 md:h-9 rounded-xl bg-primary/10">
                            <i data-lucide="boxes" class="w-4 h-4 md:w-5 md:h-5 text-primary"></i>
                        </span>
                        <div>
                            <h1 class="text-base md:text-lg font-semibold leading-tight">Asset Management</h1>
                            <p class="-mt-0.5 text-xs opacity-40 hidden sm:block">MVP • Desain Bento • Compact</p>
                        </div>
                    </div>
                    <!-- Hapus tombol di navbar: tombol login hanya ada di Kartu Utama -->
                    <div class="text-xs opacity-0 select-none">.</div>
                </div>
            </nav>
        </div>

        <!-- Grid Bento -->
        <!-- Tinggi disetel agar muat 1 layar bersama navbar melayang -->
        <section class="grid grid-cols-1 md:grid-cols-12 gap-4 bento-grid h-auto md:h-[calc(100vh-140px)] mt-3 pb-4 md:pb-0"
            style="--row-h: calc((100vh - 140px - 16px)/2); grid-template-rows: auto;">
            <!-- Kartu Utama (Hero): lebih tinggi dari kartu 1, berada di atas kartu 1 -->
            <x-card class="overflow-hidden col-span-1 md:col-span-12 lg:col-span-7 shadow-sm order-1" shadow>
                <div class="flex relative flex-col h-full p-4 md:p-6">
                    <div class="flex flex-col sm:flex-row gap-3 justify-between items-start">
                        <div class="space-y-2 max-w-xl">
                            <div class="badge badge-primary badge-outline">MVP</div>
                            <h2 class="text-xl md:text-2xl font-semibold leading-snug">Satu Dashboard untuk Semua Aset</h2>
                            <p class="text-sm opacity-80">Pantau, atur, dan audit aset secara terpusat. Mulai dari
                                registrasi hingga penghapusan—semua dalam satu alur yang sederhana dan rapi.</p>
                            <a href="{{ route('login') }}" class="inline-flex gap-2 items-center mt-1 btn btn-primary btn-sm">
                                <i data-lucide="log-in" class="w-4 h-4"></i>
                                <span>Login Demo</span>
                            </a>
                        </div>
                        <span class="inline-flex justify-center items-center w-10 h-10 md:w-12 md:h-12 rounded-2xl bg-primary/10 flex-shrink-0">
                            <i data-lucide="layout-dashboard" class="w-5 h-5 md:w-6 md:h-6 text-primary"></i>
                        </span>
                    </div>
                    <i data-lucide="grid" class="absolute -right-3 -bottom-3 w-16 md:w-24 h-16 md:h-24 opacity-10"></i>
                </div>
            </x-card>

            <!-- Kartu 2: Analitik Mendalam (kanan atas) -->
            <x-card title="Analitik Mendalam" class="overflow-hidden col-span-1 md:col-span-12 lg:col-span-5 shadow-sm order-2" shadow>
                <div class="flex relative flex-col h-full p-4 md:p-6">
                    <div class="space-y-1">
                        <div class="flex gap-2 items-center">
                            <span class="inline-flex justify-center items-center w-8 h-8 rounded-xl bg-primary/10">
                                <i data-lucide="bar-chart-3" class="w-4 h-4 text-primary"></i>
                            </span>
                        </div>
                        <p class="text-xs opacity-80">Dapatkan insight berharga dengan laporan dan analitik yang
                            komprehensif dan actionable.</p>
                    </div>
                    <!-- Placeholder chart (tanpa angka/label) -->
                    <div class="overflow-hidden relative flex-1 mt-4 rounded-xl bg-base-200/60 min-h-24 md:min-h-28">
                        <svg viewBox="0 0 300 100" class="absolute inset-0">
                            <polyline points="0,80 30,74 60,76 90,62 120,66 150,50 180,54 210,38 240,46 270,28 300,34"
                                fill="none" stroke="currentColor" stroke-width="3" class="opacity-40" />
                        </svg>
                    </div>
                    <!-- Watermark ikon besar -->
                    <i data-lucide="bar-chart-3"
                        class="absolute -right-2 -bottom-2 w-16 md:w-20 h-16 md:h-20 opacity-40 text-primary-content"></i>
                </div>
            </x-card>

            <!-- Kartu 1 (fitur) dipindah ke bawah & konten dibiarkan (kecuali chip MVP dihapus) -->
            <x-card title="Manajemen Terpusat" class="overflow-hidden col-span-1 md:col-span-12 lg:col-span-7 shadow-sm order-3" shadow>
                <div class="flex relative flex-col h-full p-4 md:p-6">
                    <!-- Header dengan ikon + judul (tanpa badge MVP) -->
                    <div class="space-y-2 max-w-xl">
                        <div class="flex gap-2 items-center">
                            <span class="inline-flex justify-center items-center w-8 h-8 rounded-xl bg-primary/10">
                                <i data-lucide="layout-dashboard" class="w-4 h-4 text-primary"></i>
                            </span>
                        </div>
                        <p class="text-sm opacity-80">Kelola semua aset perusahaan dalam satu dashboard yang
                            terintegrasi dan mudah diakses. Struktur data, pengguna, dan kategori dapat disusun rapi
                            untuk operasional harian.</p>
                    </div>

                    <!-- Placeholder UI: daftar aset (tanpa angka) -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 mt-4">
                        <x-card class="bg-base-200/60">
                            <div class="p-3">
                                <div class="flex gap-2 items-center">
                                    <i data-lucide="hard-drive" class="w-4 h-4"></i>
                                    <span class="text-xs font-medium">Inventaris</span>
                                </div>
                                <p class="text-xs opacity-70">Unit, kategori, status.</p>
                            </div>
                        </x-card>
                        <x-card class="bg-base-200/60">
                            <div class="p-3">
                                <div class="flex gap-2 items-center">
                                    <i data-lucide="user-cog" class="w-4 h-4"></i>
                                    <span class="text-xs font-medium">Pengguna & Peran</span>
                                </div>
                                <p class="text-xs opacity-70">Hak akses berbasis role.</p>
                            </div>
                        </x-card>
                        <x-card class="bg-base-200/60">
                            <div class="p-3">
                                <div class="flex gap-2 items-center">
                                    <i data-lucide="workflow" class="w-4 h-4"></i>
                                    <span class="text-xs font-medium">Alur & Status</span>
                                </div>
                                <p class="text-xs opacity-70">Lifecycle aset yang jelas.</p>
                            </div>
                        </x-card>
                    </div>

                    <!-- Watermark ikon besar -->
                    <i data-lucide="layout-dashboard"
                        class="absolute -right-2 -bottom-2 w-16 md:w-24 h-16 md:h-24 opacity-40 text-primary-content"></i>
                </div>
            </x-card>

            <!-- Kartu 3: Scan QR/Barcode (kanan bawah) -->
            <x-card title="Scan QR/Barcode" class="overflow-hidden col-span-1 md:col-span-12 lg:col-span-5 shadow-sm order-4" shadow>
                <div class="flex relative flex-col h-full p-4 md:p-6">
                    <div class="space-y-1">
                        <div class="flex gap-2 items-center">
                            <span class="inline-flex justify-center items-center w-8 h-8 rounded-xl bg-primary/10">
                                <i data-lucide="qr-code" class="w-4 h-4 text-primary"></i>
                            </span>
                        </div>
                        <p class="text-xs opacity-80">Dapat melakukan scan QR/Barcode untuk manajemen aset (placeholder
                            UI untuk MVP).</p>
                    </div>
                    <!-- Placeholder area scanner (tanpa aksi & tombol) -->
                    <div class="grid flex-1 place-items-center mt-4 rounded-xl bg-base-200/60 min-h-24 md:min-h-28">
                        <div class="relative w-20 md:w-24 h-20 md:h-24 rounded-lg border-2 border-dashed">
                            <span class="absolute -top-2 -left-2 w-4 h-4 rounded-md border-t-2 border-l-2"></span>
                            <span class="absolute -top-2 -right-2 w-4 h-4 rounded-md border-t-2 border-r-2"></span>
                            <span class="absolute -bottom-2 -left-2 w-4 h-4 rounded-md border-b-2 border-l-2"></span>
                            <span class="absolute -right-2 -bottom-2 w-4 h-4 rounded-md border-r-2 border-b-2"></span>
                            <div class="absolute inset-x-2 top-1/2 h-0.5 animate-pulse -translate-y-1/2 bg-primary/60">
                            </div>
                        </div>
                    </div>
                    <!-- Watermark ikon besar -->
                    <i data-lucide="qr-code"
                        class="absolute -right-2 -bottom-2 w-16 md:w-20 h-16 md:h-20 opacity-40 text-primary-content"></i>
                </div>
            </x-card>
        </section>
    </main>

    <script>
        // Render Lucide Icons
        window.addEventListener('DOMContentLoaded', () => {
            lucide.createIcons();
        });
    </script>
</body>

</html>