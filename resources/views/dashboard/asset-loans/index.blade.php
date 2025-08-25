@extends('layouts.dashboard')

@section('title', 'Asset Loan Management')

@section('content')
    <div class="space-y-6">
        <!-- Page Header -->
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-base-content">Asset Loan Management</h1>
                <p class="mt-1 text-base-content/70">Kelola data pinjaman aset sistem.</p>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success">
                <i data-lucide="check-circle" class="w-5 h-5"></i>
                {{ session('success') }}
            </div>
        @endif

        <!-- asset-loans Table -->
        <div class="shadow-xl card bg-base-100">
            <div class="card-body">
                <h2 class="mb-4 text-lg font-semibold card-title">Daftar Pinjaman Aset</h2>
                <x-asset-loans.table :asset-loans="$assetLoans" />

                @if($assetLoans->hasPages())
                    <div class="mt-6">
                        {{ $assetLoans->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection