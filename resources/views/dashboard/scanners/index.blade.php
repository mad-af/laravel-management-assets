@extends('layouts.dashboard')

@section('title', 'QR/Barcode Scanner')

@section('content')
    <livewire:dashboard-content-header title='QR/Barcode Scanner' description='Scan QR code atau barcode untuk mencari dan mengelola aset.' />

    @if(session('success'))
        <div class="mb-6 alert alert-success">
            <i data-lucide="check-circle" class="w-4 h-4"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="mb-6 alert alert-error">
            <i data-lucide="x-circle" class="w-4 h-4"></i>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <!-- Main content -->
    <div class="space-y-4">
        <!-- Scanner Interface Component -->
        <livewire:scanners.scanner-interface />

        <!-- Scanner History Table Component -->
        <livewire:scanners.table />
    </div>

    <!-- Scanner Drawer Component -->
    <livewire:scanners.drawer />


@endsection