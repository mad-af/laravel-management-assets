@extends('layouts.dashboard')

@section('title', 'Assets Management')

@section('content')
    <livewire:dashboard-content-header title='Assets Management' description='Kelola data aset dalam sistem.'
        buttonText='Tambah Asset' buttonIcon='o-plus' buttonAction='openAssetDrawer' />

    <livewire:assets.table />

    <livewire:assets.drawer />
@endsection