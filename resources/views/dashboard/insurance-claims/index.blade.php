@extends('layouts.dashboard')

@section('title', 'Category Management')

@section('content')
    <livewire:dashboard-content-header title='Klaim Asuransi' description='Kelola data klaim asuransi dalam sistem.'
        buttonText='Tambah Klaim Asuransi' buttonIcon='o-plus' buttonAction='openInsuranceClaimDrawer' />

    <livewire:insurance-claims.table />

    <livewire:insurance-claims.drawer />
@endsection