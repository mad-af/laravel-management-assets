@extends('layouts.dashboard')

@section('title', 'Category Management')

@section('content')
    <livewire:dashboard-content-header title='Category Management' description='Kelola data kategori dalam sistem.'
        buttonText='Add Category' buttonIcon='o-plus' buttonAction='openCategoryDrawer' />

    <livewire:insurances.table />

    <livewire:insurances.drawer />
@endsection