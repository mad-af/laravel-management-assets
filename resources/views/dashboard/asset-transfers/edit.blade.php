@extends('layouts.dashboard')

@section('title', 'Edit Asset Transfer')

@section('content')
    <div class="space-y-6">
        {{-- Header --}}
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Edit Asset Transfer</h1>
                <p class="text-gray-600">{{ $assetTransfer->transfer_no }}</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('asset-transfers.show', $assetTransfer) }}" class="btn btn-outline">
                    <x-icon name="o-eye" class="w-4 h-4" />
                    View Details
                </a>
                <a href="{{ route('asset-transfers.index') }}" class="btn btn-ghost">
                    <x-icon name="o-arrow-left" class="w-4 h-4" />
                    Back to List
                </a>
            </div>
        </div>

        @if($assetTransfer->status !== 'draft')
            <div class="alert alert-warning">
                <x-icon name="o-exclamation-triangle" class="w-5 h-5" />
                <span>This transfer cannot be edited because its status is "{{ ucfirst($assetTransfer->status) }}". Only draft transfers can be edited.</span>
            </div>
        @else
            <form action="{{ route('asset-transfers.update', $assetTransfer) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')
                
                {{-- Basic Information --}}
                <div class="card bg-base-100 shadow-sm">
                    <div class="card-body">
                        <h2 class="card-title mb-4">Basic Information</h2>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text">Transfer No <span class="text-error">*</span></span>
                                </label>
                                <input type="text" name="transfer_no" value="{{ old('transfer_no', $assetTransfer->transfer_no) }}" class="input input-bordered @error('transfer_no') input-error @enderror" required />
                                @error('transfer_no')
                                    <label class="label">
                                        <span class="label-text-alt text-error">{{ $message }}</span>
                                    </label>
                                @enderror
                            </div>

                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text">Type <span class="text-error">*</span></span>
                                </label>
                                <select name="type" class="select select-bordered @error('type') select-error @enderror" required>
                                    <option value="">Select Type</option>
                                    <option value="relocation" {{ old('type', $assetTransfer->type) == 'relocation' ? 'selected' : '' }}>Relocation</option>
                                    <option value="maintenance" {{ old('type', $assetTransfer->type) == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                                    <option value="disposal" {{ old('type', $assetTransfer->type) == 'disposal' ? 'selected' : '' }}>Disposal</option>
                                    <option value="return" {{ old('type', $assetTransfer->type) == 'return' ? 'selected' : '' }}>Return</option>
                                </select>
                                @error('type')
                                    <label class="label">
                                        <span class="label-text-alt text-error">{{ $message }}</span>
                                    </label>
                                @enderror
                            </div>

                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text">Priority <span class="text-error">*</span></span>
                                </label>
                                <select name="priority" class="select select-bordered @error('priority') select-error @enderror" required>
                                    <option value="">Select Priority</option>
                                    <option value="low" {{ old('priority', $assetTransfer->priority) == 'low' ? 'selected' : '' }}>Low</option>
                                    <option value="medium" {{ old('priority', $assetTransfer->priority) == 'medium' ? 'selected' : '' }}>Medium</option>
                                    <option value="high" {{ old('priority', $assetTransfer->priority) == 'high' ? 'selected' : '' }}>High</option>
                                    <option value="urgent" {{ old('priority', $assetTransfer->priority) == 'urgent' ? 'selected' : '' }}>Urgent</option>
                                </select>
                                @error('priority')
                                    <label class="label">
                                        <span class="label-text-alt text-error">{{ $message }}</span>
                                    </label>
                                @enderror
                            </div>

                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text">Scheduled At</span>
                                </label>
                                <input type="datetime-local" name="scheduled_at" value="{{ old('scheduled_at', $assetTransfer->scheduled_at ? $assetTransfer->scheduled_at->format('Y-m-d\TH:i') : '') }}" class="input input-bordered @error('scheduled_at') input-error @enderror" />
                                @error('scheduled_at')
                                    <label class="label">
                                        <span class="label-text-alt text-error">{{ $message }}</span>
                                    </label>
                                @enderror
                            </div>
                        </div>

                        <div class="form-control">
                            <label class="label">
                                <span class="label-text">Reason</span>
                            </label>
                            <textarea name="reason" class="textarea textarea-bordered @error('reason') textarea-error @enderror" rows="3" placeholder="Enter reason for transfer...">{{ old('reason', $assetTransfer->reason) }}</textarea>
                            @error('reason')
                                <label class="label">
                                    <span class="label-text-alt text-error">{{ $message }}</span>
                                </label>
                            @enderror
                        </div>

                        <div class="form-control">
                            <label class="label">
                                <span class="label-text">Notes</span>
                            </label>
                            <textarea name="notes" class="textarea textarea-bordered @error('notes') textarea-error @enderror" rows="3" placeholder="Additional notes...">{{ old('notes', $assetTransfer->notes) }}</textarea>
                            @error('notes')
                                <label class="label">
                                    <span class="label-text-alt text-error">{{ $message }}</span>
                                </label>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Transfer Items --}}
                <div class="card bg-base-100 shadow-sm">
                    <div class="card-body">
                        <div class="flex items-center justify-between mb-4">
                            <h2 class="card-title">Transfer Items</h2>
                            <button type="button" id="addItem" class="btn btn-outline btn-sm">
                                <x-icon name="o-plus" class="w-4 h-4" />
                                Add Item
                            </button>
                        </div>

                        <div id="transferItems" class="space-y-4">
                            @php
                                $items = old('items', $assetTransfer->items->map(function($item) {
                                    return [
                                        'asset_id' => $item->asset_id,
                                        'to_location_id' => $item->to_location_id
                                    ];
                                })->toArray());
                            @endphp
                            
                            @foreach($items as $index => $item)
                                <div class="transfer-item border border-base-300 rounded-lg p-4">
                                    <div class="flex items-center justify-between mb-4">
                                        <h3 class="font-medium">Item {{ $index + 1 }}</h3>
                                        <button type="button" class="btn btn-ghost btn-sm text-error remove-item">
                                            <x-icon name="o-trash" class="w-4 h-4" />
                                        </button>
                                    </div>
                                    
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div class="form-control">
                                            <label class="label">
                                                <span class="label-text">Asset <span class="text-error">*</span></span>
                                            </label>
                                            <select name="items[{{ $index }}][asset_id]" class="select select-bordered asset-select" required>
                                                <option value="">Select Asset</option>
                                                @foreach($assets as $asset)
                                                    <option value="{{ $asset->id }}" data-location="{{ $asset->location->name ?? 'No Location' }}" {{ $item['asset_id'] == $asset->id ? 'selected' : '' }}>
                                                        {{ $asset->name }} ({{ $asset->asset_tag }})
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        
                                        <div class="form-control">
                                            <label class="label">
                                                <span class="label-text">To Location <span class="text-error">*</span></span>
                                            </label>
                                            <select name="items[{{ $index }}][to_location_id]" class="select select-bordered" required>
                                                <option value="">Select Location</option>
                                                @foreach($locations as $location)
                                                    <option value="{{ $location->id }}" {{ $item['to_location_id'] == $location->id ? 'selected' : '' }}>
                                                        {{ $location->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    
                                    <div class="mt-2">
                                        <small class="text-gray-500">Current Location: <span class="current-location">-</span></small>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        
                        @error('items')
                            <div class="text-error text-sm mt-2">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                {{-- Actions --}}
                <div class="flex justify-end gap-4">
                    <a href="{{ route('asset-transfers.show', $assetTransfer) }}" class="btn btn-ghost">Cancel</a>
                    <button type="submit" class="btn btn-primary">
                        <x-icon name="o-check" class="w-4 h-4" />
                        Update Transfer
                    </button>
                </div>
            </form>
        @endif
    </div>

    <script>
        let itemIndex = {{ count($items) }};
        
        document.getElementById('addItem')?.addEventListener('click', function() {
            const container = document.getElementById('transferItems');
            const newItem = createTransferItem(itemIndex);
            container.appendChild(newItem);
            itemIndex++;
        });
        
        function createTransferItem(index) {
            const div = document.createElement('div');
            div.className = 'transfer-item border border-base-300 rounded-lg p-4';
            div.innerHTML = `
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-medium">Item ${index + 1}</h3>
                    <button type="button" class="btn btn-ghost btn-sm text-error remove-item">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                    </button>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">Asset <span class="text-error">*</span></span>
                        </label>
                        <select name="items[${index}][asset_id]" class="select select-bordered asset-select" required>
                            <option value="">Select Asset</option>
                            @foreach($assets as $asset)
                                <option value="{{ $asset->id }}" data-location="{{ $asset->location->name ?? 'No Location' }}">
                                    {{ $asset->name }} ({{ $asset->asset_tag }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">To Location <span class="text-error">*</span></span>
                        </label>
                        <select name="items[${index}][to_location_id]" class="select select-bordered" required>
                            <option value="">Select Location</option>
                            @foreach($locations as $location)
                                <option value="{{ $location->id }}">{{ $location->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                
                <div class="mt-2">
                    <small class="text-gray-500">Current Location: <span class="current-location">-</span></small>
                </div>
            `;
            
            // Add event listeners
            div.querySelector('.remove-item').addEventListener('click', function() {
                if (document.querySelectorAll('.transfer-item').length > 1) {
                    div.remove();
                    updateItemNumbers();
                }
            });
            
            div.querySelector('.asset-select').addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                const currentLocationSpan = div.querySelector('.current-location');
                currentLocationSpan.textContent = selectedOption.dataset.location || '-';
            });
            
            return div;
        }
        
        // Add event listeners to existing items
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.remove-item').forEach(button => {
                button.addEventListener('click', function() {
                    if (document.querySelectorAll('.transfer-item').length > 1) {
                        button.closest('.transfer-item').remove();
                        updateItemNumbers();
                    }
                });
            });
            
            document.querySelectorAll('.asset-select').forEach(select => {
                select.addEventListener('change', function() {
                    const selectedOption = this.options[this.selectedIndex];
                    const currentLocationSpan = this.closest('.transfer-item').querySelector('.current-location');
                    currentLocationSpan.textContent = selectedOption.dataset.location || '-';
                });
                
                // Trigger change event for pre-selected values
                if (select.value) {
                    select.dispatchEvent(new Event('change'));
                }
            });
        });
        
        function updateItemNumbers() {
            document.querySelectorAll('.transfer-item').forEach((item, index) => {
                item.querySelector('h3').textContent = `Item ${index + 1}`;
            });
        }
    </script>
@endsection