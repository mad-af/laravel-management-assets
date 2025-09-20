@extends('layouts.dashboard')

@section('title', 'Vehicle Management')

@section('content')
    <livewire:dashboard-content-header title='Vehicle Management'
        description='Manage vehicle profiles and information in the system.' buttonText='Add Odometer' buttonIcon='o-calculator'
        buttonAction='openVehicleOdometerDrawer' :additional-buttons="[
            [
                'text' => 'Save Vehicle Profile',
                'icon' => 'o-truck',
                'class' => 'btn-outline btn-sm',
                'action' => 'openVehicleProfileDrawer'
            ]
        ]" />

    <livewire:vehicles.table />

    <livewire:vehicles.drawer />
@endsection