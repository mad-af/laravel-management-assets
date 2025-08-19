@extends('layouts.dashboard')

@section('title', 'Tambah User Baru')

@section('content')
    <div class="space-y-6">
        <!-- Page Header -->
        <div class="flex justify-between items-center">
            <div class="flex gap-4 items-center">
                <a href="{{ route('users.index') }}" class="btn btn-ghost btn-sm">
                    <i data-lucide="arrow-left" class="mr-2 w-4 h-4"></i>
                </a>
                <div>
                    <h1 class="text-3xl font-bold text-base-content">Tambah User Baru</h1>
                    <p class="mt-1 text-base-content/70">Buat akun pengguna baru untuk sistem.</p>
                </div>
            </div>
        </div>

        <!-- Create User Form -->
        <div class="shadow-xl card bg-base-100">
            <div class="card-body">
                <h2 class="mb-4 text-lg font-semibold card-title">Form Tambah User</h2>

                @if ($errors->any())
                    <div class="mb-4 alert alert-error">
                        <i data-lucide="alert-circle" class="w-5 h-5"></i>
                        <div>
                            <h3 class="font-bold">Terjadi kesalahan!</h3>
                            <ul class="mt-2 list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif

                <form action="{{ route('users.store') }}" method="POST" class="space-y-4">
                    @csrf

                </form>
            </div>
        </div>
    </div>
@endsection

