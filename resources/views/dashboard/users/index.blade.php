@extends('layouts.dashboard')

@section('title', 'User Management')

@section('content')
    <livewire:dashboard-content-header title='User Management' description='Kelola data pengguna dalam sistem.'
        buttonText='Add User' buttonIcon='o-plus' buttonAction='openUserDrawer' />

    <livewire:users.table />

    <livewire:users.drawer />
@endsection