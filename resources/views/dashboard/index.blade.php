@extends('layouts.dashboard')

@section('title', 'Dashboard')

@section('content')
    <livewire:dashboard-content-header title='Dashboard' description='Welcome to the dashboard' />

    <livewire:dashboard.asset-stats />

    <livewire:dashboard.transfers-need-confirmation />
    <livewire:dashboard.overdue-borrowers />
    <livewire:dashboard.upcoming-vehicle-maintenance />
    <livewire:dashboard.vehicle-taxes-due />
    <livewire:dashboard.vehicle-taxes-invalid />
@endsection