@extends('layouts.dashboard')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="mb-6">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Detail Pajak Kendaraan</h1>
                <p class="text-gray-600">{{ $vehicleTax->asset->name }} ({{ $vehicleTax->asset->code }})</p>
            </div>
            <div class="flex space-x-2">
                <a href="{{ route('vehicle-taxes.edit', $vehicleTax) }}" 
                   class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                    Edit
                </a>
                <a href="{{ route('vehicle-taxes.index') }}" 
                   class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                    Kembali
                </a>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Asset Information -->
            <div class="bg-gray-50 p-4 rounded-lg">
                <h3 class="text-lg font-semibold text-gray-900 mb-3">Informasi Asset</h3>
                <div class="space-y-2">
                    <div>
                        <span class="text-sm font-medium text-gray-600">Nama Asset:</span>
                        <p class="text-gray-900">{{ $vehicleTax->asset->name }}</p>
                    </div>
                    <div>
                        <span class="text-sm font-medium text-gray-600">Kode Asset:</span>
                        <p class="text-gray-900">{{ $vehicleTax->asset->code }}</p>
                    </div>
                </div>
            </div>

            <!-- Tax Period -->
            <div class="bg-gray-50 p-4 rounded-lg">
                <h3 class="text-lg font-semibold text-gray-900 mb-3">Periode Pajak</h3>
                <div class="space-y-2">
                    <div>
                        <span class="text-sm font-medium text-gray-600">Mulai:</span>
                        <p class="text-gray-900">{{ $vehicleTax->tax_period_start?->format('d M Y') ?? '-' }}</p>
                    </div>
                    <div>
                        <span class="text-sm font-medium text-gray-600">Berakhir:</span>
                        <p class="text-gray-900">{{ $vehicleTax->tax_period_end?->format('d M Y') ?? '-' }}</p>
                    </div>
                </div>
            </div>

            <!-- Payment Information -->
            <div class="bg-gray-50 p-4 rounded-lg">
                <h3 class="text-lg font-semibold text-gray-900 mb-3">Informasi Pembayaran</h3>
                <div class="space-y-2">
                    <div>
                        <span class="text-sm font-medium text-gray-600">Jatuh Tempo:</span>
                        <p class="text-gray-900">{{ $vehicleTax->due_date?->format('d M Y') ?? '-' }}</p>
                        @if($vehicleTax->due_date && $vehicleTax->due_date->isPast() && !$vehicleTax->payment_date)
                            <span class="inline-block px-2 py-1 text-xs bg-red-100 text-red-800 rounded-full">Terlambat</span>
                        @endif
                    </div>
                    <div>
                        <span class="text-sm font-medium text-gray-600">Tanggal Bayar:</span>
                        <p class="text-gray-900">
                            {{ $vehicleTax->payment_date?->format('d M Y') ?? '-' }}
                            @if($vehicleTax->payment_date)
                                <span class="inline-block px-2 py-1 text-xs bg-green-100 text-green-800 rounded-full ml-2">Sudah Bayar</span>
                            @else
                                <span class="inline-block px-2 py-1 text-xs bg-yellow-100 text-yellow-800 rounded-full ml-2">Belum Bayar</span>
                            @endif
                        </p>
                    </div>
                    <div>
                        <span class="text-sm font-medium text-gray-600">Jumlah:</span>
                        <p class="text-gray-900 text-lg font-semibold">Rp {{ number_format($vehicleTax->amount, 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>

            <!-- Receipt Information -->
            <div class="bg-gray-50 p-4 rounded-lg">
                <h3 class="text-lg font-semibold text-gray-900 mb-3">Informasi Kwitansi</h3>
                <div class="space-y-2">
                    <div>
                        <span class="text-sm font-medium text-gray-600">Nomor Kwitansi:</span>
                        <p class="text-gray-900">{{ $vehicleTax->receipt_no ?? '-' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Notes -->
        @if($vehicleTax->notes)
        <div class="mt-6 bg-gray-50 p-4 rounded-lg">
            <h3 class="text-lg font-semibold text-gray-900 mb-3">Catatan</h3>
            <p class="text-gray-700">{{ $vehicleTax->notes }}</p>
        </div>
        @endif

        <!-- Timestamps -->
        <div class="mt-6 pt-6 border-t border-gray-200">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-600">
                <div>
                    <span class="font-medium">Dibuat:</span>
                    {{ $vehicleTax->created_at?->format('d M Y H:i') ?? '-' }}
                </div>
                <div>
                    <span class="font-medium">Diperbarui:</span>
                    {{ $vehicleTax->updated_at?->format('d M Y H:i') ?? '-' }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection