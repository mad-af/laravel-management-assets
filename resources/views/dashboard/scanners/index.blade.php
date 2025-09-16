@extends('layouts.dashboard')

@section('title', 'QR/Barcode Scanner')

@section('content')
    <livewire:dashboard-content-header title='QR/Barcode Scanner' description='Scan QR code atau barcode untuk mencari dan mengelola aset.' />



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