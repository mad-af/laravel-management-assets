@extends('layouts.dashboard')

@section('title', 'Employee Management')

@section('content')
    <livewire:dashboard-content-header title='Employee Management' description='Kelola data karyawan dalam sistem.'
        buttonText='Add Employee' buttonIcon='o-plus' buttonAction='openEmployeeDrawer' />

    <livewire:employees.table />

    <livewire:employees.drawer />
@endsection
