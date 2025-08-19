@extends('layouts.dashboard')

@section('title', 'Tambah Category Baru')

@section('content')
    <div class="space-y-6">
        <!-- Page Header -->
        <div class="flex justify-between items-center">
            <div class="flex gap-4 items-center">
                <a href="{{ route('categories.index') }}" class="btn btn-ghost btn-sm">
                    <i data-lucide="arrow-left" class="mr-2 w-4 h-4"></i>
                </a>
                <div>
                    <h1 class="text-3xl font-bold text-base-content">Tambah Category Baru</h1>
                    <p class="mt-1 text-base-content/70">Buat akun pengguna baru untuk sistem.</p>
                </div>
            </div>
        </div>

        <!-- Create Category Form -->
        <div class="shadow-xl card bg-base-100">
            <div class="card-body">
                <h2 class="mb-4 text-lg font-semibold card-title">Form Tambah Category</h2>

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

                <form action="{{ route('categories.store') }}" method="POST" class="space-y-4">
                    @csrf

                    <!-- Name Field -->
                    <div class="form-control">
                        <label class="block mb-1 label">
                            <span class="label-text">Nama Lengkap</span>
                            <span class="label-text-alt text-error">*</span>
                        </label>
                        <input type="text" name="name" value="{{ old('name') }}" placeholder="John Doe" 
                               class="input input-bordered @error('name') input-error @enderror" required />
                        @error('name')
                            <label class="label">
                                <span class="label-text-alt text-error">{{ $message }}</span>
                            </label>
                        @enderror
                    </div>

                    <!-- Email Field -->
                    <div class="form-control">
                        <label class="block mb-1 label">
                            <span class="label-text">Email</span>
                            <span class="label-text-alt text-error">*</span>
                        </label>
                        <input type="email" name="email" value="{{ old('email') }}" placeholder="john.doe@example.com" 
                               class="input input-bordered @error('email') input-error @enderror" required />
                        @error('email')
                            <label class="label">
                                <span class="label-text-alt text-error">{{ $message }}</span>
                            </label>
                        @enderror
                    </div>

                    <!-- Form Actions -->
                    <div class="flex gap-2 justify-end pt-4">
                        <a href="{{ route('categories.index') }}" class="btn btn-ghost">
                            <i data-lucide="x" class="mr-2 w-4 h-4"></i>
                            Batal
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i data-lucide="plus" class="mr-2 w-4 h-4"></i>
                            Tambah Category
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>
@endsection