@extends('layouts.dashboard')

@section('title', 'Asset Maintenance')

@section('content')
    <div class="space-y-6">
        <!-- Dashboard Content Header -->
        <x-dashboard-content-header 
            title="Asset Maintenance"
            description="Manage and track asset maintenance activities"
            button-text="Add Maintenance"
            button-icon="o-plus"
            button-action="addMaintenance()"
            :additional-buttons="[
                [
                    'text' => 'Filter',
                    'icon' => 'o-funnel',
                    'class' => 'btn-outline btn-sm'
                ]
            ]"
        />

        <!-- Kanban Board -->
        <div class="h-[calc(100vh-12rem)]">
            <livewire:kanban-board />
        </div>
    </div>

    <!-- Maintenance Drawer -->
    <x-maintenances.drawer />

    <x-maintenances.scripts />
@endsection