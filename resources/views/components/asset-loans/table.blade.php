@props(['asset-loans', 'class' => ''])

<div class="overflow-x-auto {{ $class }}">
    <table class="table table-striped table-hover">
        <thead class="table-dark">
            <tr>
                <th>Asset</th>
                <th>Borrower</th>
                <th>Checkout Date</th>
                <th>Due Date</th>
                <th>Status</th>
                <th>Condition</th>
            </tr>
        </thead>
        <tbody>
            @forelse($assetLoans as $assetLoan)
                <tr data-asset-loan-id="{{ $assetLoan->id }}" data-asset-loan-borrower="{{ $assetLoan->borrower_name }}">
                    <td>
                        <div class="flex flex-col">
                            <span class="font-medium">{{ $assetLoan->asset->name }}</span>
                            <span class="text-xs text-base-content/70">{{ $assetLoan->asset->code }}</span>
                        </div>
                    </td>
                    <td>{{ $assetLoan->borrower_name }}</td>
                    <td>{{ $assetLoan->checkout_at->format('d M Y') }}</td>
                    <td>
                        <div class="flex flex-col">
                            <span>{{ $assetLoan->due_at->format('d M Y') }}</span>
                            @if($assetLoan->isOverdue())
                                <span class="text-xs text-error">Overdue</span>
                            @endif
                        </div>
                    </td>
                    <td>
                        @if($assetLoan->isActive())
                            @if($assetLoan->isOverdue())
                                <span class="text-xs whitespace-nowrap badge badge-error">Overdue</span>
                            @else
                                <span class="text-xs whitespace-nowrap badge badge-warning">Active</span>
                            @endif
                        @else
                            <span class="text-xs whitespace-nowrap badge badge-success">Returned</span>
                        @endif
                    </td>
                    <td>
                        <div class="flex flex-col gap-1">
                            <span class="text-xs">Out:
                                @if($assetLoan->condition_out === 'excellent')
                                    <span class="badge badge-success badge-xs">Excellent</span>
                                @elseif($assetLoan->condition_out === 'good')
                                    <span class="badge badge-info badge-xs">Good</span>
                                @elseif($assetLoan->condition_out === 'fair')
                                    <span class="badge badge-warning badge-xs">Fair</span>
                                @else
                                    <span class="badge badge-error badge-xs">Poor</span>
                                @endif
                            </span>
                            @if($assetLoan->condition_in)
                                <span class="text-xs">In:
                                    @if($assetLoan->condition_in === 'excellent')
                                        <span class="badge badge-success badge-xs">Excellent</span>
                                    @elseif($assetLoan->condition_in === 'good')
                                        <span class="badge badge-info badge-xs">Good</span>
                                    @elseif($assetLoan->condition_in === 'fair')
                                        <span class="badge badge-warning badge-xs">Fair</span>
                                    @else
                                        <span class="badge badge-error badge-xs">Poor</span>
                                    @endif
                                </span>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="py-4 text-center text-muted">
                        <i data-lucide="file-text" class="block mx-auto mb-3 w-12 h-12"></i>
                        No asset loans found
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>