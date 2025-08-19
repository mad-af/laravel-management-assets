@extends('layouts.dashboard')

@section('title', 'Location Management')

@section('content')
    <div class="space-y-6">
        <!-- Page Header -->
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-base-content">Location Management</h1>
                <p class="mt-1 text-base-content/70">Kelola data pengguna sistem.</p>
            </div>
            <div>
                <a href="{{ route('locations.create') }}" class="btn btn-primary btn-sm">
                    <i data-lucide="plus" class="mr-2 w-4 h-4"></i>
                    Tambah Location
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success">
                <i data-lucide="check-circle" class="w-5 h-5"></i>
                {{ session('success') }}
            </div>
        @endif

        <!-- locations Table -->
        <div class="shadow-xl card bg-base-100">
            <div class="card-body">
                <h2 class="mb-4 text-lg font-semibold card-title">Daftar Pengguna</h2>
                <x-locations.table :locations="$locations" />

                @if($locations->hasPages())
                    <div class="mt-6">
                        {{ $locations->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <x-locations.scripts />
@endsection