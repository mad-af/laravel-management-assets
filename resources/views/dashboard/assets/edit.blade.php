@extends('layouts.dashboard')

@section('title', 'Edit Asset')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Edit Asset</h1>
            <p class="text-gray-600 mt-1">Update asset information</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('assets.show', $asset) }}" class="btn btn-ghost">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                </svg>
                View Asset
            </a>
            <a href="{{ route('assets.index') }}" class="btn btn-ghost">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back to Assets
            </a>
        </div>
    </div>

    <!-- Form -->
    <div class="card bg-base-100 shadow-xl">
        <div class="card-body">
            <form method="POST" action="{{ route('assets.update', $asset) }}" class="space-y-6">
                @csrf
                @method('PUT')
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Asset Code -->
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-semibold">Asset Code <span class="text-red-500">*</span></span>
                        </label>
                        <input type="text" name="code" value="{{ old('code', $asset->code) }}" 
                               class="input input-bordered @error('code') input-error @enderror" 
                               placeholder="e.g., AST-001" required>
                        @error('code')
                            <label class="label">
                                <span class="label-text-alt text-red-500">{{ $message }}</span>
                            </label>
                        @enderror
                    </div>

                    <!-- Asset Name -->
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-semibold">Asset Name <span class="text-red-500">*</span></span>
                        </label>
                        <input type="text" name="name" value="{{ old('name', $asset->name) }}" 
                               class="input input-bordered @error('name') input-error @enderror" 
                               placeholder="e.g., Dell Laptop XPS 13" required>
                        @error('name')
                            <label class="label">
                                <span class="label-text-alt text-red-500">{{ $message }}</span>
                            </label>
                        @enderror
                    </div>

                    <!-- Category -->
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-semibold">Category <span class="text-red-500">*</span></span>
                        </label>
                        <select name="category_id" class="select select-bordered @error('category_id') select-error @enderror" required>
                            <option value="">Select Category</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" 
                                    {{ old('category_id', $asset->category_id) == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <label class="label">
                                <span class="label-text-alt text-red-500">{{ $message }}</span>
                            </label>
                        @enderror
                    </div>

                    <!-- Location -->
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-semibold">Location <span class="text-red-500">*</span></span>
                        </label>
                        <select name="location_id" class="select select-bordered @error('location_id') select-error @enderror" required>
                            <option value="">Select Location</option>
                            @foreach($locations as $location)
                                <option value="{{ $location->id }}" 
                                    {{ old('location_id', $asset->location_id) == $location->id ? 'selected' : '' }}>
                                    {{ $location->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('location_id')
                            <label class="label">
                                <span class="label-text-alt text-red-500">{{ $message }}</span>
                            </label>
                        @enderror
                    </div>

                    <!-- Status -->
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-semibold">Status <span class="text-red-500">*</span></span>
                        </label>
                        <select name="status" class="select select-bordered @error('status') select-error @enderror" required>
                            <option value="">Select Status</option>
                            <option value="active" {{ old('status', $asset->status) == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ old('status', $asset->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            <option value="maintenance" {{ old('status', $asset->status) == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                            <option value="disposed" {{ old('status', $asset->status) == 'disposed' ? 'selected' : '' }}>Disposed</option>
                        </select>
                        @error('status')
                            <label class="label">
                                <span class="label-text-alt text-red-500">{{ $message }}</span>
                            </label>
                        @enderror
                    </div>

                    <!-- Condition -->
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-semibold">Condition <span class="text-red-500">*</span></span>
                        </label>
                        <select name="condition" class="select select-bordered @error('condition') select-error @enderror" required>
                            <option value="">Select Condition</option>
                            <option value="excellent" {{ old('condition', $asset->condition) == 'excellent' ? 'selected' : '' }}>Excellent</option>
                            <option value="good" {{ old('condition', $asset->condition) == 'good' ? 'selected' : '' }}>Good</option>
                            <option value="fair" {{ old('condition', $asset->condition) == 'fair' ? 'selected' : '' }}>Fair</option>
                            <option value="poor" {{ old('condition', $asset->condition) == 'poor' ? 'selected' : '' }}>Poor</option>
                        </select>
                        @error('condition')
                            <label class="label">
                                <span class="label-text-alt text-red-500">{{ $message }}</span>
                            </label>
                        @enderror
                    </div>

                    <!-- Value -->
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-semibold">Asset Value <span class="text-red-500">*</span></span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" name="value" value="{{ old('value', $asset->value) }}" 
                                   class="input input-bordered flex-1 @error('value') input-error @enderror" 
                                   placeholder="0.00" step="0.01" min="0" required>
                        </div>
                        @error('value')
                            <label class="label">
                                <span class="label-text-alt text-red-500">{{ $message }}</span>
                            </label>
                        @enderror
                    </div>

                    <!-- Purchase Date -->
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-semibold">Purchase Date</span>
                        </label>
                        <input type="date" name="purchase_date" 
                               value="{{ old('purchase_date', $asset->purchase_date ? $asset->purchase_date->format('Y-m-d') : '') }}" 
                               class="input input-bordered @error('purchase_date') input-error @enderror">
                        @error('purchase_date')
                            <label class="label">
                                <span class="label-text-alt text-red-500">{{ $message }}</span>
                            </label>
                        @enderror
                    </div>
                </div>

                <!-- Description -->
                <div class="form-control">
                    <label class="label">
                        <span class="label-text font-semibold">Description</span>
                    </label>
                    <textarea name="description" rows="4" 
                              class="textarea textarea-bordered @error('description') textarea-error @enderror" 
                              placeholder="Additional details about the asset...">{{ old('description', $asset->description) }}</textarea>
                    @error('description')
                        <label class="label">
                            <span class="label-text-alt text-red-500">{{ $message }}</span>
                        </label>
                    @enderror
                </div>

                <!-- Form Actions -->
                <div class="flex justify-end gap-4 pt-6 border-t">
                    <a href="{{ route('assets.show', $asset) }}" class="btn btn-ghost">Cancel</a>
                    <button type="submit" class="btn btn-primary">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Update Asset
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection