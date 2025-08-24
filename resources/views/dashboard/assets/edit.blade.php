@extends('layouts.dashboard')

@section('title', 'Edit Asset')

@section('content')
    <div class="space-y-6">
        <!-- Page Header -->
        <div class="flex justify-between items-center">
            <div class="flex gap-4 items-center">
                <a href="{{ route('assets.show', $asset) }}" class="btn btn-ghost btn-sm">
                    <i data-lucide="arrow-left" class="mr-2 w-4 h-4"></i>
                </a>
                <div>
                    <h1 class="text-3xl font-bold text-base-content">Edit Asset</h1>
                    <p class="mt-1 text-base-content/70">Update informasi asset dalam sistem manajemen.</p>
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

                <form method="POST" action="{{ route('assets.update', $asset) }}" class="space-y-4">
                    @csrf
                    @method('PUT')
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <!-- Code Field -->
                        <div class="form-control">
                            <label class="block mb-1 label">
                                <span class="label-text">Kode Asset</span>
                                <span class="label-text-alt text-error">*</span>
                            </label>
                            <input type="text" name="code" value="{{ old('code', $asset->code) }}" placeholder="AST-001"
                                class="input input-bordered @error('code') input-error @enderror" required />
                            @error('code')
                                <label class="label">
                                    <span class="label-text-alt text-error">{{ $message }}</span>
                                </label>
                            @enderror
                        </div>

                        <!-- Name Field -->
                        <div class="form-control">
                            <label class="block mb-1 label">
                                <span class="label-text">Nama Asset</span>
                                <span class="label-text-alt text-error">*</span>
                            </label>
                            <input type="text" name="name" value="{{ old('name', $asset->name) }}" placeholder="Laptop Dell"
                                class="input input-bordered @error('name') input-error @enderror" required />
                            @error('name')
                                <label class="label">
                                    <span class="label-text-alt text-error">{{ $message }}</span>
                                </label>
                            @enderror
                        </div>

                        <!-- Category Field -->
                        <div class="form-control">
                            <label class="block mb-1 label">
                                <span class="label-text">Kategori</span>
                                <span class="label-text-alt text-error">*</span>
                            </label>
                            <select name="category_id" class="select select-bordered @error('category_id') select-error @enderror" required>
                                <option value="">Pilih Kategori</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id', $asset->category_id) == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <label class="label">
                                    <span class="label-text-alt text-error">{{ $message }}</span>
                                </label>
                            @enderror
                        </div>

                        <!-- Location Field -->
                        <div class="form-control">
                            <label class="block mb-1 label">
                                <span class="label-text">Lokasi</span>
                                <span class="label-text-alt text-error">*</span>
                            </label>
                            <select name="location_id" class="select select-bordered @error('location_id') select-error @enderror" required>
                                <option value="">Pilih Lokasi</option>
                                @foreach($locations as $location)
                                    <option value="{{ $location->id }}" {{ old('location_id', $asset->location_id) == $location->id ? 'selected' : '' }}>
                                        {{ $location->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('location_id')
                                <label class="label">
                                    <span class="label-text-alt text-error">{{ $message }}</span>
                                </label>
                            @enderror
                        </div>

                        <!-- Status Field -->
                        <div class="form-control">
                            <label class="block mb-1 label">
                                <span class="label-text">Status</span>
                                <span class="label-text-alt text-error">*</span>
                            </label>
                            <select name="status" class="select select-bordered @error('status') select-error @enderror" required>
                                <option value="">Pilih Status</option>
                                <option value="active" {{ old('status', $asset->status) == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="damaged" {{ old('status', $asset->status) == 'damaged' ? 'selected' : '' }}>Damaged</option>
                                <option value="lost" {{ old('status', $asset->status) == 'lost' ? 'selected' : '' }}>Lost</option>
                                <option value="maintenance" {{ old('status', $asset->status) == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                                <option value="checked_out" {{ old('status', $asset->status) == 'checked_out' ? 'selected' : '' }}>Checked Out</option>
                            </select>
                            @error('status')
                                <label class="label">
                                    <span class="label-text-alt text-error">{{ $message }}</span>
                                </label>
                            @enderror
                        </div>

                        <!-- Condition Field -->
                        <div class="form-control">
                            <label class="block mb-1 label">
                                <span class="label-text">Kondisi</span>
                                <span class="label-text-alt text-error">*</span>
                            </label>
                            <select name="condition" class="select select-bordered @error('condition') select-error @enderror" required>
                                <option value="">Pilih Kondisi</option>
                                <option value="excellent" {{ old('condition', $asset->condition) == 'excellent' ? 'selected' : '' }}>Excellent</option>
                                <option value="good" {{ old('condition', $asset->condition) == 'good' ? 'selected' : '' }}>Good</option>
                                <option value="fair" {{ old('condition', $asset->condition) == 'fair' ? 'selected' : '' }}>Fair</option>
                                <option value="poor" {{ old('condition', $asset->condition) == 'poor' ? 'selected' : '' }}>Poor</option>
                            </select>
                            @error('condition')
                                <label class="label">
                                    <span class="label-text-alt text-error">{{ $message }}</span>
                                </label>
                            @enderror
                        </div>

                        <!-- Value Field -->
                        <div class="form-control">
                            <label class="block mb-1 label">
                                <span class="label-text">Nilai Asset</span>
                                <span class="label-text-alt text-error">*</span>
                            </label>
                            <input type="number" name="value" value="{{ old('value', $asset->value) }}" placeholder="1000000" step="0.01"
                                class="input input-bordered @error('value') input-error @enderror" required />
                            @error('value')
                                <label class="label">
                                    <span class="label-text-alt text-error">{{ $message }}</span>
                                </label>
                            @enderror
                        </div>

                        <!-- Purchase Date Field -->
                        <div class="form-control">
                            <label class="block mb-1 label">
                                <span class="label-text">Tanggal Pembelian</span>
                            </label>
                            <input type="date" name="purchase_date" value="{{ old('purchase_date', $asset->purchase_date ? $asset->purchase_date->format('Y-m-d') : '') }}"
                                class="input input-bordered @error('purchase_date') input-error @enderror" />
                            @error('purchase_date')
                                <label class="label">
                                    <span class="label-text-alt text-error">{{ $message }}</span>
                                </label>
                            @enderror
                        </div>
                    </div>

                    <!-- Description Field -->
                    <div class="form-control">
                        <label class="block mb-1 label">
                            <span class="label-text">Deskripsi</span>
                        </label>
                        <textarea name="description" placeholder="Deskripsi asset..." rows="3"
                            class="textarea textarea-bordered @error('description') textarea-error @enderror">{{ old('description', $asset->description) }}</textarea>
                        @error('description')
                            <label class="label">
                                <span class="label-text-alt text-error">{{ $message }}</span>
                            </label>
                        @enderror
                    </div>

                    <!-- Form Actions -->
                    <div class="flex gap-2 justify-end pt-4">
                        <a href="{{ route('assets.show', $asset) }}" class="btn btn-ghost">
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