<div>
    <!-- Drawer -->
    <x-drawer wire:model="isOpen" class="w-11/12 lg:w-1/3" right>
        @if($mode === 'checkout')
            <!-- Checkout Form -->
            <x-slot:title>
                <div class="flex items-center gap-2">
                    <x-icon name="o-arrow-up-tray" class="w-5 h-5" />
                    Check Out Asset
                </div>
            </x-slot:title>

            @if($asset)
                <!-- Asset Info -->
                <div class="p-4 mb-6 rounded-lg bg-base-200">
                    <h4 class="mb-2 font-semibold">Asset Information</h4>
                    <div class="space-y-1 text-sm">
                        <div><strong>Name:</strong> {{ $asset->name }}</div>
                        <div><strong>Code:</strong> {{ $asset->code }}</div>
                        <div><strong>Category:</strong> {{ $asset->category->name ?? '-' }}</div>
                        <div><strong>Location:</strong> {{ $asset->location->name ?? '-' }}</div>
                    </div>
                </div>

                <form wire:submit="checkout" class="space-y-4">
                    <!-- Borrower Name -->
                    <x-input 
                        wire:model="borrowerName" 
                        label="Borrower Name" 
                        placeholder="Enter borrower name" 
                        required 
                        error-field="borrowerName"
                    />

                    <!-- Checkout Date -->
                    <x-datetime 
                        wire:model="checkoutDate" 
                        label="Checkout Date" 
                        required 
                        error-field="checkoutDate"
                    />

                    <!-- Due Date -->
                    <x-datetime 
                        wire:model="dueDate" 
                        label="Due Date" 
                        required 
                        error-field="dueDate"
                    />

                    <!-- Condition Out -->
                    <div class="form-control">
                        <label class="mb-2 label">
                            <span class="label-text">Condition Out <span class="text-error">*</span></span>
                        </label>
                        <div class="space-y-2">
                            @foreach($loanConditions as $condition)
                                <label class="gap-3 justify-start cursor-pointer label">
                                    <input 
                                        type="radio" 
                                        wire:model="conditionOut" 
                                        value="{{ $condition->value }}" 
                                        class="radio radio-sm" 
                                        required 
                                    />
                                    <span class="label-text">{{ $condition->label() }}</span>
                                </label>
                            @endforeach
                        </div>
                        @error('conditionOut')
                            <div class="mt-1 text-sm text-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Notes -->
                    <x-textarea 
                        wire:model="checkoutNotes" 
                        label="Notes" 
                        placeholder="Additional notes..." 
                        rows="3"
                        error-field="checkoutNotes"
                    />

                    <!-- Actions -->
                    <div class="flex gap-2 pt-4">
                        <x-button type="submit" class="flex-1 btn-primary">
                            <x-icon name="o-arrow-up-tray" class="mr-2 w-4 h-4" />
                            Check Out
                        </x-button>
                        <x-button wire:click="close" class="flex-1" outline>
                            Cancel
                        </x-button>
                    </div>
                </form>
            @endif

        @elseif($mode === 'checkin')
            <!-- Checkin Form -->
            <x-slot:title>
                <div class="flex items-center gap-2">
                    <x-icon name="o-arrow-down-tray" class="w-5 h-5" />
                    Check In Asset
                </div>
            </x-slot:title>

            @if($asset && $assetLoan)
                <!-- Asset & Loan Info -->
                <div class="p-4 mb-6 rounded-lg bg-base-200">
                    <h4 class="mb-2 font-semibold">Asset Information</h4>
                    <div class="space-y-1 text-sm">
                        <div><strong>Name:</strong> {{ $asset->name }}</div>
                        <div><strong>Code:</strong> {{ $asset->code }}</div>
                        <div><strong>Borrower:</strong> {{ $assetLoan->borrower_name }}</div>
                        <div><strong>Borrowed:</strong> {{ $assetLoan->borrowed_at->format('d/m/Y H:i') }}</div>
                        <div><strong>Due:</strong> {{ $assetLoan->due_at->format('d/m/Y H:i') }}</div>
                        @if($assetLoan->due_at->isPast())
                            <div class="text-error"><strong>Status:</strong> Overdue</div>
                        @endif
                    </div>
                </div>

                <form wire:submit="checkin" class="space-y-4">
                    <!-- Checkin Date -->
                    <x-datetime 
                        wire:model="checkinDate" 
                        label="Check In Date" 
                        required 
                        error-field="checkinDate"
                    />

                    <!-- Condition In -->
                    <div class="form-control">
                        <label class="mb-2 label">
                            <span class="label-text">Condition In <span class="text-error">*</span></span>
                        </label>
                        <div class="space-y-2">
                            @foreach($loanConditions as $condition)
                                <label class="gap-3 justify-start cursor-pointer label">
                                    <input 
                                        type="radio" 
                                        wire:model="conditionIn" 
                                        value="{{ $condition->value }}" 
                                        class="radio radio-sm" 
                                        required 
                                    />
                                    <span class="label-text">{{ $condition->label() }}</span>
                                </label>
                            @endforeach
                        </div>
                        @error('conditionIn')
                            <div class="mt-1 text-sm text-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Return Notes -->
                    <x-textarea 
                        wire:model="checkinNotes" 
                        label="Return Notes" 
                        placeholder="Additional notes about return condition..." 
                        rows="3"
                        error-field="checkinNotes"
                    />

                    <!-- Actions -->
                    <div class="flex gap-2 pt-4">
                        <x-button type="submit" class="flex-1 btn-success">
                            <x-icon name="o-arrow-down-tray" class="mr-2 w-4 h-4" />
                            Check In
                        </x-button>
                        <x-button wire:click="close" class="flex-1" outline>
                            Cancel
                        </x-button>
                    </div>
                </form>
            @endif
        @endif
    </x-drawer>
</div>