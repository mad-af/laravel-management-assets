@extends('layouts.dashboard')

@section('title', 'Asset Maintenance')

@section('content')

    <!-- Dashboard Content Header -->
    <livewire:dashboard-content-header title="Perawatan Aset" description="Kelola dan pantau aktivitas perawatan aset"
        button-text="Tambah Perawatan" button-icon="o-plus" button-action="openMaintenanceDrawer" :additional-buttons="[
                    // [
                    //     'text' => 'Tukar Tampilan',
                    //     'icon' => 'o-view-columns',
                    //     'class' => 'btn-sm',
                    //     'action' => 'toggleMaintenanceView()'
                    // ],
                    [
                        'text' => 'Unduh Data Maintenace',
                        'icon' => 'o-document-arrow-down',
                        'class' => ' btn-sm',
                        'action' => 'downloadAssetMaintenance'
                    ]
                ]" />

    {{-- Toggle tampilan: Tabel vs Kanban --}}
    {{-- <div x-data="{ maintenanceView: 'table' }"
        x-on:toggleMaintenanceView.window="maintenanceView = maintenanceView === 'table' ? 'kanban' : 'table'"> --}}
        {{-- Tabel Maintenances --}}
        {{-- <div x-show="maintenanceView === 'table'" x-cloak>
            <livewire:maintenances.table />
        </div> --}}

        {{-- Tampilan Kanban --}}
        {{-- <div x-show="maintenanceView === 'kanban'" x-cloak> --}}
            <livewire:maintenances.kanban-board />
            {{--
        </div> --}}
        {{-- </div> --}}



    <livewire:maintenances.drawer />

@endsection