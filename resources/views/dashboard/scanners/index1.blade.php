@extends('layouts.dashboard')

@section('title', 'QR/Barcode Scanner')

@section('content')
    <!-- Dashboard Content Header -->
    <x-dashboard-content-header title="QR/Barcode Scanner"
        description="Scan QR code atau barcode untuk mencari dan mengelola aset" />
    <div class="space-y-3">
        <div class="flex flex-col gap-3 lg:flex-row">
            <livewire:scanners.scan-camera />
            <livewire:scanners.scan-result />
        </div>
        <livewire:scanners.scan-history />
    </div>

    <livewire:scanners.drawer />
@endsection