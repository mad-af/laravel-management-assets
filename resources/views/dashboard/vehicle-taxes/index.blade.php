@extends('layouts.dashboard')

@section('title', 'Vehicle Tax Management')

@section('content')
    <livewire:dashboard-content-header title='Vehicle Tax Management' description='Kelola data pajak kendaraan dalam sistem.'
        buttonText='Add Vehicle Tax' buttonIcon='o-plus' buttonAction='openVehicleTaxDrawer' />

    <livewire:vehicle-taxes.table />

    <livewire:vehicle-taxes.drawer />
@endsection