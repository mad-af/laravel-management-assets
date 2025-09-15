@extends('layouts.dashboard')

@section('title', 'Edit Company')

@section('content')
    <div class="space-y-6">
        <!-- Page Header -->
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-base-content">Edit Company</h1>
                <p class="mt-1 text-base-content/70">Edit data perusahaan {{ $company->name }}.</p>
            </div>
            <div>
                <a href="{{ route('companies.index') }}" class="btn btn-ghost btn-sm">
                    <i data-lucide="arrow-left" class="mr-2 w-4 h-4"></i>
                    Kembali
                </a>
            </div>
        </div>

        <!-- Form -->
        <div class="shadow-xl card bg-base-100">
            <div class="card-body">
                <form action="{{ route('companies.update', $company) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    @method('PUT')
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Company Name -->
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text font-semibold">Nama Perusahaan <span class="text-error">*</span></span>
                            </label>
                            <input type="text" name="name" value="{{ old('name', $company->name) }}" 
                                   class="input input-bordered @error('name') input-error @enderror" 
                                   placeholder="Masukkan nama perusahaan" required>
                            @error('name')
                                <label class="label">
                                    <span class="label-text-alt text-error">{{ $message }}</span>
                                </label>
                            @enderror
                        </div>

                        <!-- Company Code -->
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text font-semibold">Kode Perusahaan <span class="text-error">*</span></span>
                            </label>
                            <input type="text" name="code" value="{{ old('code', $company->code) }}" 
                                   class="input input-bordered @error('code') input-error @enderror" 
                                   placeholder="Masukkan kode perusahaan" required>
                            @error('code')
                                <label class="label">
                                    <span class="label-text-alt text-error">{{ $message }}</span>
                                </label>
                            @enderror
                        </div>

                        <!-- Tax ID -->
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text font-semibold">Tax ID / NPWP</span>
                            </label>
                            <input type="text" name="tax_id" value="{{ old('tax_id', $company->tax_id) }}" 
                                   class="input input-bordered @error('tax_id') input-error @enderror" 
                                   placeholder="Masukkan Tax ID atau NPWP">
                            @error('tax_id')
                                <label class="label">
                                    <span class="label-text-alt text-error">{{ $message }}</span>
                                </label>
                            @enderror
                        </div>

                        <!-- Phone -->
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text font-semibold">Telepon</span>
                            </label>
                            <input type="text" name="phone" value="{{ old('phone', $company->phone) }}" 
                                   class="input input-bordered @error('phone') input-error @enderror" 
                                   placeholder="Masukkan nomor telepon">
                            @error('phone')
                                <label class="label">
                                    <span class="label-text-alt text-error">{{ $message }}</span>
                                </label>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text font-semibold">Email</span>
                            </label>
                            <input type="email" name="email" value="{{ old('email', $company->email) }}" 
                                   class="input input-bordered @error('email') input-error @enderror" 
                                   placeholder="Masukkan email perusahaan">
                            @error('email')
                                <label class="label">
                                    <span class="label-text-alt text-error">{{ $message }}</span>
                                </label>
                            @enderror
                        </div>

                        <!-- Website -->
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text font-semibold">Website</span>
                            </label>
                            <input type="url" name="website" value="{{ old('website', $company->website) }}" 
                                   class="input input-bordered @error('website') input-error @enderror" 
                                   placeholder="https://example.com">
                            @error('website')
                                <label class="label">
                                    <span class="label-text-alt text-error">{{ $message }}</span>
                                </label>
                            @enderror
                        </div>
                    </div>

                    <!-- Address -->
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-semibold">Alamat</span>
                        </label>
                        <textarea name="address" rows="3" 
                                  class="textarea textarea-bordered @error('address') textarea-error @enderror" 
                                  placeholder="Masukkan alamat lengkap perusahaan">{{ old('address', $company->address) }}</textarea>
                        @error('address')
                            <label class="label">
                                <span class="label-text-alt text-error">{{ $message }}</span>
                            </label>
                        @enderror
                    </div>

                    <!-- Current Logo -->
                    @if($company->image)
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text font-semibold">Logo Saat Ini</span>
                            </label>
                            <div class="avatar">
                                <div class="w-24 h-24 rounded">
                                    <img src="{{ asset('storage/' . $company->image) }}" alt="{{ $company->name }}" />
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Company Logo -->
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-semibold">{{ $company->image ? 'Ganti Logo Perusahaan' : 'Logo Perusahaan' }}</span>
                        </label>
                        <input type="file" name="image" accept="image/*" 
                               class="file-input file-input-bordered @error('image') file-input-error @enderror">
                        <label class="label">
                            <span class="label-text-alt">Format: JPEG, PNG, JPG, GIF. Maksimal 2MB. {{ $company->image ? 'Kosongkan jika tidak ingin mengganti.' : '' }}</span>
                        </label>
                        @error('image')
                            <label class="label">
                                <span class="label-text-alt text-error">{{ $message }}</span>
                            </label>
                        @enderror
                    </div>

                    <!-- Submit Buttons -->
                    <div class="flex justify-end space-x-4 pt-6">
                        <a href="{{ route('companies.index') }}" class="btn btn-ghost">
                            Batal
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i data-lucide="save" class="mr-2 w-4 h-4"></i>
                            Update
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection