@extends('layouts.dashboard')

@section('title', 'Dashboard')

@section('content')
    <livewire:dashboard-content-header title='Dashboard' description='Welcome to the dashboard' />

    <livewire:dashboard.asset-stats />

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <div>
            <livewire:dashboard.transfers-need-confirmation />
        </div>
        <div>
            <livewire:dashboard.overdue-borrowers />
        </div>
    </div>

    <livewire:dashboard.upcoming-vehicle-maintenance />
    <livewire:dashboard.vehicle-taxes-due />
    <livewire:dashboard.vehicle-taxes-invalid />
@endsection