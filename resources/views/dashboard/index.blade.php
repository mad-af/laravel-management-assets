@extends('layouts.dashboard')

@section('title', 'Dashboard')

@section('content')
    <livewire:dashboard-content-header title='Dashboard' description='Welcome to the dashboard' />

    <livewire:dashboard.asset-stats />
@endsection