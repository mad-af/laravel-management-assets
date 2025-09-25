@extends('layouts.dashboard')

@section('title', 'Asset Maintenance')

@section('content')

    <!-- Dashboard Content Header -->
    <x-dashboard-content-header title="Asset Maintenance" description="Manage and track asset maintenance activities"
        button-text="Add Maintenance" button-icon="o-plus" button-action="addMaintenance()" :additional-buttons="[
                                [
                                    'text' => 'Filter',
                                    'icon' => 'o-funnel',
                                    'class' => ' btn-sm',
                                    'action' => 'openFilterDrawer()'
                                ]
                            ]" />

    <!-- Kanban Board -->
    <div class="h-[calc(100vh-12rem)]">
        @php
            $maintenances = \App\Models\AssetMaintenance::with(['asset', 'assignedUser'])
                ->orderBy('created_at', 'desc')
                ->get();

            $statusColumns = collect(\App\Enums\MaintenanceStatus::cases())->map(function ($status) use ($maintenances) {
                return [
                    'status' => $status,
                    'maintenances' => $maintenances->where('status', $status)
                ];
            })->toArray();
        @endphp

        <x-maintenances.kanban-board :status-columns="$statusColumns" />
    </div>

    <!-- Maintenance Drawer -->
    <x-maintenances.drawer />

    <!-- Filter Drawer -->
    <x-maintenances.filter-drawer />

    <x-maintenances.scripts />
@endsection