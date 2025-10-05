@extends('layouts.dashboard')

@section('title', 'Asset Maintenance')

@section('content')

    <!-- Dashboard Content Header -->
    <x-dashboard-content-header title="Perawatan Aset" description="Kelola dan pantau aktivitas perawatan aset"
        button-text="Tambah Perawatan" button-icon="o-plus" button-action="addMaintenance()" :additional-buttons="[
            [
                'text' => 'Filter',
                'icon' => 'o-funnel',
                'class' => ' btn-sm',
                'action' => 'openFilterDrawer()'
            ]
        ]" />

    <livewire:maintenances.kanban-board />

    <!-- Maintenance Drawer -->
    <x-maintenances.drawer />

    <!-- Maintenance Scripts -->
    <x-maintenances.scripts />

@endsection