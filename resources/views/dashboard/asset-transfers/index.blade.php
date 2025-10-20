@extends('layouts.dashboard')

@section('title', 'Asset Transfer Management')

@section('content')
    <livewire:dashboard-content-header title='Asset Transfer Management'
        description='Kelola transfer aset antar cabang dalam sistem.' buttonText='Create Transfer' buttonIcon='o-plus'
        buttonAction='openAssetTransferDrawer' />

    <livewire:asset-transfers.table />

    <livewire:asset-transfers.drawer />
@endsection