@extends('layouts.dashboard')

@section('title', 'Asset Transfers')

@section('content')
    <div class="space-y-6">
        {{-- Header --}}
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Asset Transfers</h1>
                <p class="text-gray-600">Kelola transfer aset antar lokasi</p>
            </div>
            <a href="{{ route('asset-transfers.create') }}" class="btn btn-primary">
                <x-icon name="o-plus" class="w-4 h-4" />
                Create Transfer
            </a>
        </div>

        {{-- Filters --}}
        <div class="shadow-sm card bg-base-100">
            <div class="card-body">
                <form method="GET" class="flex flex-wrap gap-4">
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">Status</span>
                        </label>
                        <select name="status" class="w-full max-w-xs select select-bordered">
                            <option value="">All Status</option>
                            <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                            <option value="submitted" {{ request('status') == 'submitted' ? 'selected' : '' }}>Submitted</option>
                            <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                            <option value="executed" {{ request('status') == 'executed' ? 'selected' : '' }}>Executed</option>
                            <option value="void" {{ request('status') == 'void' ? 'selected' : '' }}>Void</option>
                        </select>
                    </div>
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">Search</span>
                        </label>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Transfer No, Reason..." class="w-full max-w-xs input input-bordered" />
                    </div>
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">&nbsp;</span>
                        </label>
                        <div class="flex gap-2">
                            <button type="submit" class="btn btn-outline">
                                <x-icon name="o-magnifying-glass" class="w-4 h-4" />
                                Filter
                            </button>
                            <a href="{{ route('asset-transfers.index') }}" class="btn btn-ghost">
                                Reset
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- Table --}}
        <div class="shadow-sm card bg-base-100">
            <div class="p-0 card-body">
                <div class="overflow-x-auto">
                    <table class="table table-zebra">
                        <thead>
                            <tr>
                                <th>Transfer No</th>
                                <th>Status</th>
                                <th>Requested By</th>
                                <th>Items Count</th>
                                <th>Scheduled At</th>
                                <th>Created At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($transfers as $transfer)
                                <tr>
                                    <td>
                                        <div class="font-medium">{{ $transfer->transfer_no }}</div>
                                        @if($transfer->reason)
                                            <div class="text-sm text-gray-500">{{ Str::limit($transfer->reason, 50) }}</div>
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            $statusColors = [
                                                'draft' => 'badge-ghost',
                                                'submitted' => 'badge-info',
                                                'approved' => 'badge-success',
                                                'executed' => 'badge-primary',
                                                'void' => 'badge-error'
                                            ];
                                        @endphp
                                        <div class="badge {{ $statusColors[$transfer->status] ?? 'badge-ghost' }}">
                                            {{ ucfirst($transfer->status) }}
                                        </div>
                                    </td>
                                    <td>
                                        <div class="font-medium">{{ $transfer->requestedBy->name }}</div>
                                        <div class="text-sm text-gray-500">{{ $transfer->requestedBy->email }}</div>
                                    </td>
                                    <td>
                                        <div class="badge badge-outline">{{ $transfer->items_count ?? $transfer->items->count() }} items</div>
                                    </td>
                                    <td>
                                        @if($transfer->scheduled_at)
                                            {{ $transfer->scheduled_at->format('d M Y H:i') }}
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </td>
                                    <td>{{ $transfer->created_at->format('d M Y H:i') }}</td>
                                    <td>
                                        <div class="flex gap-2">
                                            <a href="{{ route('asset-transfers.show', $transfer) }}" class="btn btn-ghost btn-sm">
                                                <x-icon name="o-eye" class="w-4 h-4" />
                                            </a>
                                            @if($transfer->status === 'draft')
                                                <a href="{{ route('asset-transfers.edit', $transfer) }}" class="btn btn-ghost btn-sm">
                                                    <x-icon name="o-pencil" class="w-4 h-4" />
                                                </a>
                                                <form action="{{ route('asset-transfers.destroy', $transfer) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-ghost btn-sm text-error">
                                                        <x-icon name="o-trash" class="w-4 h-4" />
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="py-8 text-center">
                                        <div class="text-gray-500">
                                            <x-icon name="o-document-text" class="mx-auto mb-2 w-12 h-12 opacity-50" />
                                            <p>No asset transfers found</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Pagination --}}
        @if($transfers->hasPages())
            <div class="flex justify-center">
                {{ $transfers->links() }}
            </div>
        @endif
    </div>
@endsection