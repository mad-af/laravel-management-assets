@extends('layouts.dashboard')

@section('title', 'Edit Asset')

@section('content')
    <div class="space-y-6">
        <!-- Page Header -->
        <div class="flex justify-between items-center">
            <div class="flex gap-4 items-center">
                <a href="{{ route('assets.index') }}" class="btn btn-ghost btn-sm">
                    <i data-lucide="arrow-left" class="mr-2 w-4 h-4"></i>
                </a>
                <div>
                    <h1 class="text-3xl font-bold text-base-content">Edit Asset</h1>
                    <p class="mt-1 text-base-content/70">Perbarui informasi pengguna {{ $asset->name }}.</p>
                </div>
            </div>
        </div>

        <!-- Edit Asset Form -->
        <div class="shadow-xl card bg-base-100">
            <div class="card-body">
                <h2 class="mb-4 text-lg font-semibold card-title">Form Edit Asset</h2>

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

                <form action="{{ route('assets.update', $asset) }}" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <!-- Name Field -->
                    <div class="form-control">
                        <label class="block mb-1 label">
                            <span class="label-text">Nama Lengkap</span>
                            <span class="label-text-alt text-error">*</span>
                        </label>
                        <input type="text" name="name" value="{{ old('name', $asset->name) }}" placeholder="John Doe" 
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
                        <input type="email" name="email" value="{{ old('email', $asset->email) }}" placeholder="john.doe@example.com" 
                               class="input input-bordered @error('email') input-error @enderror" required />
                        @error('email')
                            <label class="label">
                                <span class="label-text-alt text-error">{{ $message }}</span>
                            </label>
                        @enderror
                    </div>

                    <!-- Form Actions -->
                    <div class="flex gap-2 justify-end pt-4">
                        <a href="{{ route('assets.index') }}" class="btn btn-ghost">
                            <i data-lucide="x" class="mr-2 w-4 h-4"></i>
                            Batal
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i data-lucide="save" class="mr-2 w-4 h-4"></i>
                            Update Asset
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>
@endsection