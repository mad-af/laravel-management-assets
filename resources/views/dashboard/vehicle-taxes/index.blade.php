@extends('layouts.dashboard')

@section('title', 'Vehicle Tax Management')

@section('content')
    <livewire:dashboard-content-header title='Vehicle Tax Management' description='Kelola data pajak kendaraan dalam sistem.'
        buttonText='Bayar Pajak Kendaraan' buttonIcon='o-calculator' buttonAction='openVehicleTaxDrawer' :additional-buttons="[
                [
                    'text' => 'Konfigurasi Pajak Kendaraan',
                    'icon' => 'o-document',
                    'class' => ' btn-sm',
                    'action' => 'openVehicleTaxTypeDrawer'
                ]
            ]"/>

    <livewire:vehicle-taxes.table />

    <livewire:vehicle-taxes.drawer />
@endsection