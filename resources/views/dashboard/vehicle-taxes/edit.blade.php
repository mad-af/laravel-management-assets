@extends('layouts.dashboard')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Edit Pajak Kendaraan</h1>
        <p class="text-gray-600">Edit data pajak kendaraan {{ $vehicleTax->asset->name }}</p>
    </div>

    <div class="bg-white rounded-lg shadow-md p-6">
        <form action="{{ route('vehicle-taxes.update', $vehicleTax) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Asset -->
                <div>
                    <label for="asset_id" class="block text-sm font-medium text-gray-700 mb-2">Asset</label>
                    <select name="asset_id" id="asset_id" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                        <option value="">Pilih Asset</option>
                        @foreach($assets as $asset)
                            <option value="{{ $asset->id }}" {{ (old('asset_id', $vehicleTax->asset_id) == $asset->id) ? 'selected' : '' }}>
                                {{ $asset->name }} ({{ $asset->code }})
                            </option>
                        @endforeach
                    </select>
                    @error('asset_id')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Tax Period Start -->
                <div>
                    <label for="tax_period_start" class="block text-sm font-medium text-gray-700 mb-2">Periode Pajak Mulai</label>
                    <input type="date" name="tax_period_start" id="tax_period_start" 
                           value="{{ old('tax_period_start', $vehicleTax->tax_period_start?->format('Y-m-d')) }}" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                    @error('tax_period_start')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Tax Period End -->
                <div>
                    <label for="tax_period_end" class="block text-sm font-medium text-gray-700 mb-2">Periode Pajak Berakhir</label>
                    <input type="date" name="tax_period_end" id="tax_period_end" 
                           value="{{ old('tax_period_end', $vehicleTax->tax_period_end?->format('Y-m-d')) }}" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                    @error('tax_period_end')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Due Date -->
                <div>
                    <label for="due_date" class="block text-sm font-medium text-gray-700 mb-2">Tanggal Jatuh Tempo</label>
                    <input type="date" name="due_date" id="due_date" 
                           value="{{ old('due_date', $vehicleTax->due_date?->format('Y-m-d')) }}" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                    @error('due_date')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Payment Date -->
                <div>
                    <label for="payment_date" class="block text-sm font-medium text-gray-700 mb-2">Tanggal Pembayaran</label>
                    <input type="date" name="payment_date" id="payment_date" 
                           value="{{ old('payment_date', $vehicleTax->payment_date?->format('Y-m-d')) }}" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @error('payment_date')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Amount -->
                <div>
                    <label for="amount" class="block text-sm font-medium text-gray-700 mb-2">Jumlah</label>
                    <input type="number" name="amount" id="amount" step="0.01" min="0" 
                           value="{{ old('amount', $vehicleTax->amount) }}" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                    @error('amount')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Receipt No -->
                <div>
                    <label for="receipt_no" class="block text-sm font-medium text-gray-700 mb-2">Nomor Kwitansi</label>
                    <input type="text" name="receipt_no" id="receipt_no" 
                           value="{{ old('receipt_no', $vehicleTax->receipt_no) }}" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @error('receipt_no')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Notes -->
            <div class="mt-6">
                <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Catatan</label>
                <textarea name="notes" id="notes" rows="3" 
                          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('notes', $vehicleTax->notes) }}</textarea>
                @error('notes')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Actions -->
            <div class="flex justify-end space-x-4 mt-6">
                <a href="{{ route('vehicle-taxes.index') }}" 
                   class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                    Batal
                </a>
                <button type="submit" 
                        class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                    Update
                </button>
            </div>
        </form>
    </div>
</div>
@endsection