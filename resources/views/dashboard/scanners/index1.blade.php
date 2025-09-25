@extends('layouts.dashboard')

@section('title', 'QR/Barcode Scanner')

@section('content')
    <!-- Dashboard Content Header -->
    <x-dashboard-content-header title="QR/Barcode Scanner"
        description="Scan QR code atau barcode untuk mencari dan mengelola aset" />

    <div class="space-y-4">
        <div class="flex gap-4">
            <livewire:scanners.scan-camera />
            <livewire:scanners.scan-result />
        </div>
        <livewire:scanners.scan-history />
    </div>

    <livewire:scanners.drawer />
@endsection