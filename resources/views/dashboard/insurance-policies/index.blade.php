@extends('layouts.dashboard')

@section('title', 'Category Management')

@section('content')
    <livewire:dashboard-content-header title='Polis Asuransi' description='Kelola data polis asuransi dalam sistem.'
        buttonText='Tambah Polis Asuransi' buttonIcon='o-plus' buttonAction='openInsurancePolicyDrawer' />

    <livewire:insurance-policies.table />

    <livewire:insurance-policies.drawer />
@endsection