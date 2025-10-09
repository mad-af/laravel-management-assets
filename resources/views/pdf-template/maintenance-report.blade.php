<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <title>Work Order</title>
    <!-- Tailwind + DaisyUI CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                /* Replace default scale: minimum 10pt */

                extend: {
                    fontFamily: { tnr: ['"Times New Roman"', 'Times', 'serif'] },
                },
            },
            daisyui: { themes: ["light"] },
        }
    </script>
    <link href="https://cdn.jsdelivr.net/npm/daisyui@4.12.10/dist/full.min.css" rel="stylesheet" />
</head>

<body class="bg-base-200 print:bg-white">
    <!-- A5 portrait canvas -->
    <main
        class="relative mx-auto print:my-0 bg-white shadow-xl print:shadow-none w-[148mm] h-[210mm] p-[10mm] font-['Times_New_Roman']">

        <div class="space-y-3">
            <!-- Header -->
            @if (false)
                <!-- SECTION 1: Header (Company name + WORK ORDER in the same row/section) -->
                <section class="grid grid-cols-12 gap-6 items-start">
                    <!-- Company name (left) -->
                    <div class="col-span-8">
                        <h1 class="text-xl font-bold leading-tight uppercase">PT. GAYA SUKSES MANDIRI KASEINDO</h1>
                        {{-- <p class="-mt-1 italic">[Your Company Slogan]</p> --}}
                    </div>

                    <!-- WORK ORDER (right) -->
                    <div class="col-span-4 leading-none text-right">
                        <div class="text-3xl font-extrabold tracking-wider">WORK</div>
                        <div class="text-3xl font-extrabold tracking-wider">ORDER</div>
                    </div>
                </section>

                <!-- SECTION 2: Addresses (own section) -->
                <section class="grid grid-cols-12 gap-6 mt-3">
                    <!-- LEFT column: Surabaya + Jakarta stacked -->
                    <div class="col-span-4 text-xs leading-tight">
                        <p class="font-semibold">Surabaya Office | Factory</p>
                        <p>Jl. Raya Margomulyo 63A, Surabaya</p>
                        <p>Tel: +62 31 7499 050 <br /> surabaya@safeway.co.id</p>
                    </div>

                    <div class="col-span-4 text-xs leading-tight">
                        <p class="font-semibold">Jakarta Office</p>
                        <p>Kompleks Artha Gading Blok F No. 20, Jakarta</p>
                        <p>Tel: +62 21 4585 0806 <br /> jakarta@safeway.co.id</p>
                    </div>

                    <!-- RIGHT column: Semarang (sits to the right of Jakarta; whole addresses are a separate section) -->
                    <div class="col-span-4 text-xs leading-tight">
                        <p class="font-semibold">Semarang Office | Factory</p>
                        <p>Kawasan Industri Candi, Blok XIC No. 2C, Semarang</p>
                        <p>Tel: +62 24 7627 600 <br /> semarang@safeway.co.id</p>
                    </div>
                </section>
            @else
                <section class="flex justify-between items-start">
                    <div class="space-y-0.5">
                        <div class="mb-2">
                            <h1 class="text-xl font-bold leading-tight uppercase">PT. GAYA SUKSES MANDIRI KASEINDO</h1>
                            {{-- <p class="-mt-1 italic">[Your Company Slogan]</p> --}}
                        </div>

                        <div class="text-xs leading-tight">
                            <p class="font-semibold">Surabaya Office | Factory</p>
                            <p>Jl. Raya Margomulyo 63A, Surabaya</p>
                            <p>Tel: +62 31 7499 050 <span class="mx">·</span> surabaya@safeway.co.id</p>
                        </div>

                        <div class="text-xs leading-tight">
                            <p class="font-semibold">Jakarta Office</p>
                            <p>Kompleks Artha Gading Blok F No. 20, Jakarta</p>
                            <p>Tel: +62 21 4585 0806 <span class="mx">·</span> jakarta@safeway.co.id</p>
                        </div>

                        <div class="text-xs leading-tight">
                            <p class="font-semibold">Semarang Office | Factory</p>
                            <p>Kawasan Industri Candi, Blok XIC No. 2C, Semarang</p>
                            <p>Tel: +62 24 7627 600 <span class="mx">·</span> semarang@safeway.co.id</p>
                        </div>
                    </div>

                    <div class="text-right">
                        <div class="text-3xl font-extrabold tracking-wider leading-none">WORK</div>
                        <div class="text-3xl font-extrabold tracking-wider leading-none">ORDER</div>
                    </div>
                </section>
            @endif

            <!-- WO note & number -->
            <section class="space-y-1 text-xs">
                <p>Nomor berikut harus dicantumkan pada semua korespondensi, dokumen pengiriman, dan faktur yang
                    terkait:</p>
                <div class="flex gap-4 leading-snug">
                    <p class="flex-1">
                        <span class="font-bold align-middle">Nomor W.O.:</span>
                        <span class="uppercase align-middle">{{ $data->work_order_no ?? 'WO-000000' }}</span>
                    </p>
                    <div class="flex-1 leading-snug">
                        <p>
                            <span class="font-semibold">Tanggal W.O.:</span>
                            <span class="">{{ $data->start_date ? $data->start_date->format('d F Y') : 'Belum ditentukan' }}</span>
                        </p>
                        <p>
                            <span class="font-semibold">Estimasi Tanggal Selesai:</span>
                            <span class="">{{ $data->estimation_end_date ? $data->estimation_end_date->format('d F Y') : 'Belum ditentukan' }}</span>
                        </p>
                    </div>
                </div>
            </section>

            <!-- To / Ship To -->
            <section class="grid grid-cols-2 gap-4 text-xs">
                <div>
                    <h3 class="font-semibold">Kepada:</h3>
                    <div class="leading-snug">
                        <p>{{ $data->workshop->name ?? 'Workshop Maintenance' }}</p>
                        <p>{{ $data->workshop->address ?? 'Alamat workshop tidak tersedia' }}</p>
                        <p>{{ $data->vehicle->vehicle_no ?? 'N/A' }}</p>
                        <p>{{ $data->vehicle->brand->name ?? 'Unknown' }} - {{ $data->vehicle->type ?? 'Unknown' }}</p>
                    </div>
                </div>

                <div>
                    <h3 class="font-semibold">Kontak PIC Perusahaan:</h3>
                    <div class="leading-snug">
                        <p>{{ $data->employee->name ?? 'N/A' }}</p>
                        <p>{{ $data->employee->phone ?? 'N/A' }}</p>
                    </div>
                </div>
            </section>

            <!-- Service Instructions Table -->
            <section>
                <table class="w-full text-xs border border-black border-collapse">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="px-1 w-12 font-semibold text-center border border-black">No.</th>
                            <th class="px-1 font-semibold text-center border border-black">Instruksi Pekerjaan
                            </th>
                            <th class="px-1 w-24 font-semibold text-center border border-black">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if($data->maintenance && $data->maintenance->service_tasks && count($data->maintenance->service_tasks) > 0)
                            @foreach($data->maintenance->service_tasks as $index => $task)
                                <tr>
                                    <td class="px-1 text-center border border-black">{{ $index + 1 }}</td>
                                    <td class="px-1 border border-black">{{ $task['task'] ?? $task }}</td>
                                    <td class="px-1 border border-black">{{ $task['notes'] ?? '' }}</td>
                                </tr>
                            @endforeach
                        @else
                            <!-- Fallback to title and description if no service_tasks -->
                            @if($data->maintenance && $data->maintenance->title)
                                <tr>
                                    <td class="px-1 text-center border border-black">1</td>
                                    <td class="px-1 border border-black">{{ $data->maintenance->title }}</td>
                                    <td class="px-1 border border-black"></td>
                                </tr>
                            @endif
                            @if($data->maintenance && $data->maintenance->description)
                                <tr>
                                    <td class="px-1 text-center border border-black">2</td>
                                    <td class="px-1 border border-black">{{ $data->maintenance->description }}</td>
                                    <td class="px-1 border border-black"></td>
                                </tr>
                            @endif
                            @if(!$data->maintenance || (!$data->maintenance->title && !$data->maintenance->description))
                                <!-- Default empty rows if no data -->
                                <tr>
                                    <td class="px-1 text-center border border-black">1</td>
                                    <td class="px-1 border border-black">-</td>
                                    <td class="px-1 border border-black"></td>
                                </tr>
                                <tr>
                                    <td class="px-1 text-center border border-black">2</td>
                                    <td class="px-1 border border-black">-</td>
                                    <td class="px-1 border border-black"></td>
                                </tr>
                                <tr>
                                    <td class="px-1 text-center border border-black">3</td>
                                    <td class="px-1 border border-black">-</td>
                                    <td class="px-1 border border-black"></td>
                                </tr>
                            @endif
                        @endif
                    </tbody>
                </table>
            </section>

            <!-- Notes Section -->
            <section>
                <h3 class="text-xs font-semibold">Catatan:</h3>
                <div class="border border-black min-h-[60px] p-2 text-xs">
                    @if($data->note && $data->note !== 'No notes available')
                        <p>{{ $data->note }}</p>
                    @else
                        <p class="italic text-gray-600">Tuliskan catatan tambahan atau instruksi khusus di sini...</p>
                    @endif
                </div>
            </section>

            <!-- Signature Section -->
            <section class="grid grid-cols-3 gap-4 !mt-6 text-xs">
                <div class="space-y-2 text-center">
                    <h4 class="font-semibold">Dibuat Oleh:</h4>
                    <div class="flex flex-col justify-end min-h-12">
                        <p class="text-center">( Staff GA )</p>
                    </div>
                </div>

                <div class="space-y-2 text-center">
                    <h4 class="font-semibold">Disetujui Oleh:</h4>
                    <div class="flex flex-col justify-end min-h-12">
                        <p class="text-center">( Kepala HR &amp; GA )</p>
                    </div>
                </div>

                <div class="space-y-2 text-center">
                    <h4 class="font-semibold">Diterima Oleh:</h4>
                    <div class="flex flex-col justify-end min-h-12">
                        <p class="text-center">( _________________ )</p>
                    </div>
                </div>
            </section>


        </div>

        <!-- Footer note (optional) -->
        <section class="absolute bottom-4 text-[8px] text-base-content/70">
            <p>Diterbitkan oleh <span class="font-bold">PT. Gaya Sukses Mandiri Kaseindo</span> · Dokumen Rahasia – Untuk penggunaan internal dan klien.</p>
        </section>
    </main>
</body>

</html>