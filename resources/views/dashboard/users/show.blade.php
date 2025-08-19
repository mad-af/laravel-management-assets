@extends('layouts.dashboard')

@section('title', 'Detail User')

@section('content')
    <div class="space-y-6">
        <!-- Page Header -->
        <div class="flex justify-between items-center">
            <div class="flex gap-4 items-center">
                <a href="{{ route('users.index') }}" class="btn btn-ghost btn-sm">
                    <i data-lucide="arrow-left" class="mr-2 w-4 h-4"></i>
                </a>
                <div>
                    <h1 class="text-3xl font-bold text-base-content">Detail User</h1>
                    <p class="mt-1 text-base-content/70">Informasi lengkap pengguna sistem.</p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            <!-- User Info Card -->
            <div class="lg:col-span-2">
                <x-users.detail-card :user="$user" />
            </div>

            <!-- Quick Actions Card -->
            <div class="lg:col-span-1">
                <x-users.quick-actions :user="$user" />
            </div>
        </div>
    </div>
@endsection