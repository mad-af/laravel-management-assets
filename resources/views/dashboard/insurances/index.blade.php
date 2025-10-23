@extends('layouts.dashboard')

@section('title', 'Category Management')

@section('content')
    <livewire:dashboard-content-header title='Asuransi' description='Kelola data asuransi dalam sistem.'
        buttonText='Tambah Asuransi' buttonIcon='o-plus' buttonAction='openInsuranceDrawer' />

    <livewire:insurances.table />

    <livewire:insurances.drawer />
@endsection