@extends('layouts.dashboard')

@section('title', 'Vehicle Management')

@section('content')
    <livewire:dashboard-content-header title='Vehicle Management'
        description='Manage vehicle profiles and information in the system.' buttonText='Save Profile' buttonIcon='o-plus'
        buttonAction='openVehicleProfileDrawer' :additional-buttons="[
            [
                'text' => 'Save Odometer',
                'icon' => 'o-funnel',
                'class' => 'btn-primary btn-sm',
                'action' => 'openVehicleOdometerDrawer'
            ]
        ]" />

    <livewire:vehicles.table />

    <livewire:vehicles.drawer />
@endsection