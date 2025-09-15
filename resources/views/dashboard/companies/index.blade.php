@extends('layouts.dashboard')

@section('title', 'Company Management')

@section('content')

    <livewire:dashboard-content-header title='Company Management' description='Kelola data perusahaan dalam sistem.'
        buttonText='Add Company' buttonIcon='o-plus' buttonAction='openCompanyDrawer' />

    <livewire:companies.table />

    <livewire:companies.drawer />

    <x-companies.scripts />
@endsection