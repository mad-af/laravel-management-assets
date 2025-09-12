@extends('layouts.dashboard')

@section('title', 'Asset Maintenance')

@section('content')
    <div class="space-y-6">
        <!-- Page Header -->
        <x-page-header 
            title="Asset Maintenance"
            description="Manage and track asset maintenance activities"
            button-text="Add Maintenance"
            button-icon="o-plus"
        />

        <!-- Kanban Board -->
        <div class="h-[calc(100vh-12rem)]">
            <livewire:kanban-board />
        </div>
    </div>
@endsection