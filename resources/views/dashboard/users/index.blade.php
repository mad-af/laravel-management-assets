@extends('layouts.dashboard')

@section('title', 'User Management')

@section('content')
    <div class="space-y-6">
        <!-- Page Header -->
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-base-content">User Management</h1>
                <p class="mt-1 text-base-content/70">Kelola data pengguna sistem.</p>
            </div>
            <div class="flex gap-2">
                <button class="btn btn-primary btn-sm" onclick="openCreateDrawer()">
                    <i data-lucide="plus" class="mr-2 w-4 h-4"></i>
                    Tambah User
                </button>
                <button class="btn btn-outline btn-sm">
                    <i data-lucide="download" class="mr-2 w-4 h-4"></i>
                    Export
                </button>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success">
                <i data-lucide="check-circle" class="w-5 h-5"></i>
                {{ session('success') }}
            </div>
        @endif

        <!-- Users Table -->
        <div class="shadow-xl card bg-base-100">
            <div class="card-body">
                <h2 class="mb-4 text-lg font-semibold card-title">Daftar Pengguna</h2>
{{-- @php
    dd($users);
@endphp --}}
                <x-user-table :users="$users" />

                @if($users->hasPages())
                    <div class="mt-6">
                        {{ $users->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <x-user-create-drawer />
    <x-user-edit-drawer />
    <x-user-scripts />
@endsection