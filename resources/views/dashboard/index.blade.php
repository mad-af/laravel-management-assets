@extends('layouts.dashboard')

@section('title', 'Dashboard')

@section('content')
    <livewire:dashboard-content-header title='Dashboard' description='Welcome to the dashboard' />

    <livewire:dashboard.asset-stats />

    <div class="grid grid-cols-1 gap-4 lg:grid-cols-6">
        <div class="lg:col-span-3">
            <livewire:dashboard.vehicle-taxes-due />
        </div>
        <div class="lg:col-span-3">
            <livewire:dashboard.upcoming-vehicle-maintenance />
        </div>
    </div>

    <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
        <div>
            <livewire:dashboard.transfers-need-confirmation />
        </div>
        <div>
            <livewire:dashboard.overdue-borrowers />
        </div>
    </div>

    
    
    <livewire:dashboard.vehicle-taxes-invalid />
@endsection