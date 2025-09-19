@extends('layouts.dashboard')

@section('title', 'Vehicle Management')

@section('content')
    <livewire:dashboard-content-header title='Vehicle Management'
        description='Manage vehicle profiles and information in the system.' buttonText='Add Vehicle' buttonIcon='o-plus'
        buttonAction='openVehicleDrawer' />

    <livewire:vehicles.table />

    <livewire:vehicles.drawer />
@endsection