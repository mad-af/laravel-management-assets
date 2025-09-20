@extends('layouts.dashboard')

@section('title', 'Vehicle Management')

@section('content')
    <livewire:dashboard-content-header title='Vehicle Management'
        description='Manage vehicle profiles and information in the system.' buttonText='Save Profile' buttonIcon='o-truck'
        buttonAction='openVehicleProfileDrawer' :additional-buttons="[
            [
                'text' => 'Add Odometer',
                'icon' => 'o-calculator',
                'class' => 'btn-primary btn-sm',
                'action' => 'openVehicleOdometerDrawer'
            ]
        ]" />

    <livewire:vehicles.table />

    <livewire:vehicles.drawer />
@endsection