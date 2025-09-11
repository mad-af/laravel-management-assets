@props(['asset-loan', 'class' => ''])

<div class="shadow-xl card bg-base-100 {{ $class }}">
    <div class="card-body">
        <h3 class="flex gap-2 items-center mb-6 card-title text-base-content">
            <i data-lucide="file-text" class="w-5 h-5"></i>
            Asset Loan Information
        </h3>

        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
            <div>
                <label class="text-sm font-semibold text-base-content/70">Asset</label>
                <div class="flex flex-col mt-1">
                    <p class="font-medium text-base-content">{{ $assetLoan->asset->name }}</p>
                    <p class="text-sm text-base-content/70">{{ $assetLoan->asset->code }}</p>
                </div>
            </div>

            <div>
                <label class="text-sm font-semibold text-base-content/70">Borrower Name</label>
                <p class="mt-1 text-base-content">{{ $assetLoan->borrower_name }}</p>
            </div>

            <div>
                <label class="text-sm font-semibold text-base-content/70">Checkout Date</label>
                <p class="mt-1 text-base-content">{{ $assetLoan->checkout_at->format('d M Y, H:i') }}</p>
            </div>

            <div>
                <label class="text-sm font-semibold text-base-content/70">Due Date</label>
                <div class="mt-1">
                    <p class="text-base-content">{{ $assetLoan->due_at->format('d M Y, H:i') }}</p>
                    @if($assetLoan->isOverdue())
                        <span class="badge badge-error badge-sm mt-1">Overdue</span>
                    @endif
                </div>
            </div>

            @if($assetLoan->checkin_at)
                <div>
                    <label class="text-sm font-semibold text-base-content/70">Checkin Date</label>
                    <p class="mt-1 text-base-content">{{ $assetLoan->checkin_at->format('d M Y, H:i') }}</p>
                </div>
            @endif

            <div>
                <label class="text-sm font-semibold text-base-content/70">Status</label>
                <p class="mt-1">
                    @if($assetLoan->isActive())
                        @if($assetLoan->isOverdue())
                            <span class="badge badge-error badge-sm">Overdue</span>
                        @else
                            <span class="badge badge-warning badge-sm">Active</span>
                        @endif
                    @else
                        <span class="badge badge-success badge-sm">Returned</span>
                    @endif
                </p>
            </div>

            <div>
                <label class="text-sm font-semibold text-base-content/70">Condition Out</label>
                <p class="mt-1">
                    @if($assetLoan->condition_out === \App\Enums\LoanCondition::EXCELLENT)
                    <span class="badge badge-success badge-sm">{{ \App\Enums\LoanCondition::EXCELLENT->label() }}</span>
                @elseif($assetLoan->condition_out === \App\Enums\LoanCondition::GOOD)
                    <span class="badge badge-info badge-sm">{{ \App\Enums\LoanCondition::GOOD->label() }}</span>
                @elseif($assetLoan->condition_out === \App\Enums\LoanCondition::FAIR)
                    <span class="badge badge-warning badge-sm">{{ \App\Enums\LoanCondition::FAIR->label() }}</span>
                @else
                    <span class="badge badge-error badge-sm">{{ \App\Enums\LoanCondition::POOR->label() }}</span>
                @endif
                </p>
            </div>

            @if($assetLoan->condition_in)
                <div>
                    <label class="text-sm font-semibold text-base-content/70">Condition In</label>
                    <p class="mt-1">
                        @if($assetLoan->condition_in === \App\Enums\LoanCondition::EXCELLENT)
                        <span class="badge badge-success badge-sm">{{ \App\Enums\LoanCondition::EXCELLENT->label() }}</span>
                    @elseif($assetLoan->condition_in === \App\Enums\LoanCondition::GOOD)
                        <span class="badge badge-info badge-sm">{{ \App\Enums\LoanCondition::GOOD->label() }}</span>
                    @elseif($assetLoan->condition_in === \App\Enums\LoanCondition::FAIR)
                        <span class="badge badge-warning badge-sm">{{ \App\Enums\LoanCondition::FAIR->label() }}</span>
                    @else
                        <span class="badge badge-error badge-sm">{{ \App\Enums\LoanCondition::POOR->label() }}</span>
                    @endif
                    </p>
                </div>
            @endif

            <div>
                <label class="text-sm font-semibold text-base-content/70">Created At</label>
                <p class="mt-1 text-base-content">{{ $assetLoan->created_at->format('d M Y, H:i') }}</p>
            </div>

            <div>
                <label class="text-sm font-semibold text-base-content/70">Last Updated</label>
                <p class="mt-1 text-base-content">{{ $assetLoan->updated_at->format('d M Y, H:i') }}</p>
            </div>
        </div>

        @if($assetLoan->notes)
            <div class="mt-6">
                <label class="text-sm font-semibold text-base-content/70">Notes</label>
                <div class="p-4 mt-2 rounded-lg bg-base-200">
                    <p class="text-sm text-base-content whitespace-pre-wrap">{{ $assetLoan->notes }}</p>
                </div>
            </div>
        @endif
    </div>
</div>
    </div>
</div>