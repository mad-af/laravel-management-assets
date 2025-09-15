@extends('layouts.dashboard')

@section('title', 'Tambah Location Baru')

@section('content')
    <div class="space-y-6">
        <!-- Page Header -->
        <div class="flex justify-between items-center">
            <div class="flex gap-4 items-center">
                <a href="{{ route('locations.index') }}" class="btn btn-ghost btn-sm">
                    <i data-lucide="arrow-left" class="mr-2 w-4 h-4"></i>
                </a>
                <div>
                    <h1 class="text-3xl font-bold text-base-content">Tambah Location Baru</h1>
                    <p class="mt-1 text-base-content/70">Buat lokasi baru untuk sistem manajemen aset.</p>
                </div>
            </div>
        </div>

        <!-- Create Location Form -->
        <div class="shadow-xl card bg-base-100">
            <div class="card-body">
                <h2 class="mb-4 text-lg font-semibold card-title">Form Tambah Location</h2>

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

                <form action="{{ route('locations.store') }}" method="POST" class="space-y-4">
                    @csrf



                    <!-- Name Field -->
                    <div class="form-control">
                        <label class="block mb-1 label">
                            <span class="label-text">Nama Lokasi</span>
                            <span class="label-text-alt text-error">*</span>
                        </label>
                        <input type="text" name="name" value="{{ old('name') }}" placeholder="Gedung A - Lantai 1" 
                               class="input input-bordered @error('name') input-error @enderror" required />
                        @error('name')
                            <label class="label">
                                <span class="label-text-alt text-error">{{ $message }}</span>
                            </label>
                        @enderror
                    </div>

                    <!-- Status Field -->
                    <div class="form-control">
                        <label class="flex gap-2 items-center cursor-pointer label">
                            <span class="label-text">Status Aktif</span>
                            <input type="checkbox" name="is_active" value="1" {{ old('is_active', '1') ? 'checked' : '' }} class="toggle toggle-primary" />
                        </label>
                    </div>

                    <!-- Form Actions -->
                    <div class="flex gap-2 justify-end pt-4">
                        <a href="{{ route('locations.index') }}" class="btn btn-ghost">
                            <i data-lucide="x" class="mr-2 w-4 h-4"></i>
                            Batal
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i data-lucide="plus" class="mr-2 w-4 h-4"></i>
                            Tambah Location
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>
@endsection