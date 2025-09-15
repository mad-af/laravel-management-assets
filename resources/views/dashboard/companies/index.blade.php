@extends('layouts.dashboard')

@section('title', 'Company Management')

@section('content')

    <x-dashboard-content-header title="Company Management" description="Kelola data perusahaan dalam sistem."
        button-text="Add Company" button-icon="o-plus" button-action="addMaintenance()" />


    <livewire:companies.table />

    <x-companies.scripts />
@endsection