@extends('layouts.dashboard')

@section('title', 'Asset Loan Management')

@section('content')
    <!-- Page Header -->
    <livewire:dashboard-content-header title="Asset Loan Management" description="Kelola data pinjaman aset sistem."
        buttonText="Tambah Pinjaman" buttonAction="openAssetLoanDrawer" />

    <!-- Asset Loans Table -->
    <livewire:asset-loans.table />

    <!-- Asset Loans Drawer -->
    <livewire:asset-loans.drawer />
@endsection