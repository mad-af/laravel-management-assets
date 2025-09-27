@extends('layouts.dashboard')

@section('title', 'Branch Management')

@section('content')
    <livewire:dashboard-content-header title='Branch Management' description='Kelola data cabang dalam sistem.'
        buttonText='Add Branch' buttonIcon='o-plus' buttonAction='openBranchDrawer' />

    {{-- <livewire:branches.tab /> --}}

    <livewire:branches.table />

    <livewire:branches.drawer />
@endsection