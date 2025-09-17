@extends('layouts.dashboard')

@section('title', 'Asset Transfer Details')

@section('content')

    <livewire:dashboard-content-header 
        title='Transfer Details' 
        description='{{ $assetTransfer->transfer_no }}' 
        showBackButton />

    <div class="space-y-6">
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            {{-- Main Content --}}
            <div class="space-y-6 lg:col-span-2">
                {{-- Basic Information --}}
                <div class="shadow-sm card bg-base-100">
                    <div class="card-body">
                        <h2 class="mb-4 card-title">Basic Information</h2>
                        
                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            <div>
                                <label class="text-sm font-medium text-gray-500">Transfer No</label>
                                <p class="text-lg font-semibold">{{ $assetTransfer->transfer_no }}</p>
                            </div>
                            
                            <div>
                                <label class="text-sm font-medium text-gray-500">Status</label>
                                @php
                                    $statusColors = [
                                        'draft' => 'badge-ghost',
                                        'submitted' => 'badge-info',
                                        'approved' => 'badge-success',
                                        'executed' => 'badge-primary',
                                        'void' => 'badge-error'
                                    ];
                                @endphp
                                <div class="mt-1">
                                    <div class="badge {{ $statusColors[$assetTransfer->status?->value] ?? 'badge-ghost' }} badge-lg">
                                        {{ ucfirst($assetTransfer->status?->value) }}
                                    </div>
                                </div>
                            </div>
                            
                            <div>
                                <label class="text-sm font-medium text-gray-500">Type</label>
                                <p class="capitalize">{{ $assetTransfer->type?->label() ?? '-' }}</p>
                            </div>
                            
                            <div>
                                <label class="text-sm font-medium text-gray-500">Priority</label>
                                @php
                                    $priorityColors = [
                                        'low' => 'badge-success',
                                        'medium' => 'badge-warning',
                                        'high' => 'badge-error',
                                        'urgent' => 'badge-error'
                                    ];
                                @endphp
                                <div class="mt-1">
                                    <div class="badge {{ $priorityColors[$assetTransfer->priority?->value] ?? 'badge-ghost' }}">
                                        {{ ucfirst($assetTransfer->priority?->value ?? 'medium') }}
                                    </div>
                                </div>
                            </div>
                            
                            @if($assetTransfer->fromLocation)
                                <div>
                                    <label class="text-sm font-medium text-gray-500">From Location</label>
                                    <p>{{ $assetTransfer->fromLocation->name }}</p>
                                </div>
                            @endif
                            
                            @if($assetTransfer->toLocation)
                                <div>
                                    <label class="text-sm font-medium text-gray-500">To Location</label>
                                    <p>{{ $assetTransfer->toLocation->name }}</p>
                                </div>
                            @endif
                            
                            <div>
                                <label class="text-sm font-medium text-gray-500">Requested By</label>
                                <p>{{ $assetTransfer->requestedBy->name }}</p>
                                <p class="text-sm text-gray-500">{{ $assetTransfer->requestedBy->email }}</p>
                            </div>
                            
                            @if($assetTransfer->approvedBy)
                                <div>
                                    <label class="text-sm font-medium text-gray-500">Approved By</label>
                                    <p>{{ $assetTransfer->approvedBy->name }}</p>
                                    <p class="text-sm text-gray-500">{{ $assetTransfer->approvedBy->email }}</p>
                                </div>
                            @endif
                            
                            @if($assetTransfer->scheduled_at)
                                <div>
                                    <label class="text-sm font-medium text-gray-500">Scheduled At</label>
                                    <p>{{ $assetTransfer->scheduled_at->format('d M Y H:i') }}</p>
                                </div>
                            @endif
                            
                            @if($assetTransfer->executed_at)
                                <div>
                                    <label class="text-sm font-medium text-gray-500">Executed At</label>
                                    <p>{{ $assetTransfer->executed_at->format('d M Y H:i') }}</p>
                                </div>
                            @endif
                        </div>
                        
                        @if($assetTransfer->reason)
                            <div class="mt-4">
                                <label class="text-sm font-medium text-gray-500">Reason</label>
                                <p class="mt-1">{{ $assetTransfer->reason }}</p>
                            </div>
                        @endif
                        
                        @if($assetTransfer->notes)
                            <div class="mt-4">
                                <label class="text-sm font-medium text-gray-500">Notes</label>
                                <p class="mt-1">{{ $assetTransfer->notes }}</p>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Transfer Items --}}
                <div class="shadow-sm card bg-base-100">
                    <div class="card-body">
                        <h2 class="mb-4 card-title">Transfer Items ({{ $assetTransfer->items->count() }})</h2>
                        
                        <div class="overflow-x-auto">
                            <table class="table table-zebra">
                                <thead>
                                    <tr>
                                        <th>Asset</th>
                                        <th>From Location</th>
                                        <th>To Location</th>
                                        <th>Status</th>
                                        <th>Transferred At</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($assetTransfer->items as $item)
                                        <tr>
                                            <td>
                                                <div class="flex gap-3 items-center">
                                                    @if($item->asset->image)
                                                        <div class="avatar">
                                                            <div class="w-12 h-12 mask mask-squircle">
                                                                <img src="{{ Storage::url($item->asset->image) }}" alt="{{ $item->asset->name }}" />
                                                            </div>
                                                        </div>
                                                    @else
                                                        <div class="avatar placeholder">
                                                            <div class="w-12 rounded-full bg-neutral text-neutral-content">
                                                                <span class="text-xs">{{ substr($item->asset->name, 0, 2) }}</span>
                                                            </div>
                                                        </div>
                                                    @endif
                                                    <div>
                                                        <div class="font-bold">{{ $item->asset->name }}</div>
                                                        <div class="text-sm opacity-50">{{ $item->asset->asset_tag }}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="font-medium">{{ $item->fromLocation->name }}</div>
                                                <div class="text-sm text-gray-500">{{ $item->fromLocation->address ?? 'No address' }}</div>
                                            </td>
                                            <td>
                                                <div class="font-medium">{{ $item->toLocation->name }}</div>
                                                <div class="text-sm text-gray-500">{{ $item->toLocation->address ?? 'No address' }}</div>
                                            </td>
                                            <td>
                                                @php
                                                    $itemStatusColors = [
                                                        'pending' => 'badge-warning',
                                                        'in_transit' => 'badge-info',
                                                        'delivered' => 'badge-success',
                                                        'cancelled' => 'badge-error'
                                                    ];
                                                @endphp
                                                <div class="badge {{ $itemStatusColors[$item->status] ?? 'badge-ghost' }}">
                                                    {{ ucfirst($item->status) }}
                                                </div>
                                            </td>
                                            <td>
                                                @if($item->transferred_at)
                                                    {{ $item->transferred_at->format('d M Y H:i') }}
                                                @else
                                                    <span class="text-gray-400">-</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Sidebar --}}
            <div class="space-y-6">
                {{-- Quick Stats --}}
                <div class="shadow-sm card bg-base-100">
                    <div class="card-body">
                        <h3 class="mb-4 text-lg card-title">Quick Stats</h3>
                        
                        <div class="space-y-3">
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-500">Total Items</span>
                                <span class="font-semibold">{{ $assetTransfer->items->count() }}</span>
                            </div>
                            
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-500">Pending</span>
                                <span class="badge badge-warning badge-sm">{{ $assetTransfer->items->where('status', 'pending')->count() }}</span>
                            </div>
                            
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-500">In Transit</span>
                                <span class="badge badge-info badge-sm">{{ $assetTransfer->items->where('status', 'in_transit')->count() }}</span>
                            </div>
                            
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-500">Delivered</span>
                                <span class="badge badge-success badge-sm">{{ $assetTransfer->items->where('status', 'delivered')->count() }}</span>
                            </div>
                            
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-500">Cancelled</span>
                                <span class="badge badge-error badge-sm">{{ $assetTransfer->items->where('status', 'cancelled')->count() }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Timeline --}}
                <div class="shadow-sm card bg-base-100">
                    <div class="card-body">
                        <h3 class="mb-4 text-lg card-title">Timeline</h3>
                        
                        <div class="space-y-4">
                            <div class="flex gap-3 items-start">
                                <div class="flex-shrink-0 mt-2 w-2 h-2 rounded-full bg-primary"></div>
                                <div>
                                    <p class="font-medium">Transfer Created</p>
                                    <p class="text-sm text-gray-500">{{ $assetTransfer->created_at->format('d M Y H:i') }}</p>
                                    <p class="text-sm text-gray-500">by {{ $assetTransfer->requestedBy->name }}</p>
                                </div>
                            </div>
                            
                            @if($assetTransfer->approved_at)
                                <div class="flex gap-3 items-start">
                                    <div class="flex-shrink-0 mt-2 w-2 h-2 rounded-full bg-success"></div>
                                    <div>
                                        <p class="font-medium">Transfer Approved</p>
                                        <p class="text-sm text-gray-500">{{ $assetTransfer->approved_at->format('d M Y H:i') }}</p>
                                        <p class="text-sm text-gray-500">by {{ $assetTransfer->approvedBy->name }}</p>
                                    </div>
                                </div>
                            @endif
                            
                            @if($assetTransfer->executed_at)
                                <div class="flex gap-3 items-start">
                                    <div class="flex-shrink-0 mt-2 w-2 h-2 rounded-full bg-info"></div>
                                    <div>
                                        <p class="font-medium">Transfer Executed</p>
                                        <p class="text-sm text-gray-500">{{ $assetTransfer->executed_at->format('d M Y H:i') }}</p>
                                    </div>
                                </div>
                            @endif
                            
                            @if($assetTransfer->status === 'draft')
                                <div class="flex gap-3 items-start">
                                    <div class="flex-shrink-0 mt-2 w-2 h-2 bg-gray-300 rounded-full"></div>
                                    <div>
                                        <p class="font-medium text-gray-400">Pending Execution</p>
                                        <p class="text-sm text-gray-400">Waiting for execution</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Actions --}}
                @if($assetTransfer->status === 'draft')
                    <div class="shadow-sm card bg-base-100">
                        <div class="card-body">
                            <h3 class="mb-4 text-lg card-title">Actions</h3>
                            
                            <div class="space-y-2">
                                <a href="{{ route('asset-transfers.edit', $assetTransfer) }}" class="btn btn-outline btn-block">
                                    <x-icon name="o-pencil" class="w-4 h-4" />
                                    Edit Transfer
                                </a>
                                
                                <form action="{{ route('asset-transfers.execute', $assetTransfer) }}" method="POST" onsubmit="return confirm('Are you sure you want to execute this transfer? This action cannot be undone.')">
                                    @csrf
                                    <button type="submit" class="btn btn-success btn-block">
                                        <x-icon name="o-play" class="w-4 h-4" />
                                        Execute Transfer
                                    </button>
                                </form>
                                
                                <form action="{{ route('asset-transfers.destroy', $assetTransfer) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this transfer?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-error btn-outline btn-block">
                                        <x-icon name="o-trash" class="w-4 h-4" />
                                        Delete Transfer
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection