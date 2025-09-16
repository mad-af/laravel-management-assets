@extends('layouts.dashboard')

@section('title', 'Location Management')

@section('content')
    <livewire:dashboard-content-header title='Location Management' description='Kelola data lokasi dalam sistem.'
        buttonText='Add Location' buttonIcon='o-plus' buttonAction='openLocationDrawer' />

    <livewire:locations.table />

    <livewire:locations.drawer />

    <livewire:locations.edit-drawer />
@endsection