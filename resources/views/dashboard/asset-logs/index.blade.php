@extends('layouts.dashboard')

@section('title', 'Asset Logs')

@section('content')
    <livewire:dashboard-content-header title='Asset Logs' description='Monitor semua aktivitas dan perubahan asset.' />

    <livewire:asset-logs.table />
@endsection