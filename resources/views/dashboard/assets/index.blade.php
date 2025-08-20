@extends('layouts.dashboard')

@section('title', 'Assets Management')

@section('content')
    <div class="space-y-6">
        <!-- Page Header -->
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-base-content">Assets Management</h1>
                <p class="mt-1 text-base-content/70">Manage your organization's assets.</p>
            </div>
            <div>
                <a href="{{ route('assets.create') }}" class="btn btn-primary btn-sm">
                    <i data-lucide="plus" class="mr-2 w-4 h-4"></i>
                    Add New Asset
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="mb-6 alert alert-success">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 alert alert-error">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        <!-- Filters -->
        <x-assets.filter-form :categories="$categories" :locations="$locations" />

        <!-- Assets Table -->
        <div class="shadow-xl card bg-base-100">
            <div class="card-body">
                <h2 class="mb-4 text-lg font-semibold card-title">Daftar Pengguna</h2>
                <x-assets.table :assets="$assets" />

                @if($assets->hasPages())
                    <div class="mt-6">
                        {{ $assets->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection