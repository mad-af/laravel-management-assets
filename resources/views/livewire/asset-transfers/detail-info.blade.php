<!-- Card Detail Informasi AssetTransfer -->
<div class="shadow-sm card card-compact bg-base-100">
    <div class="card-body">
        <h2 class="text-lg card-title">
            <x-icon name="o-information-circle" class="w-5 h-5" />
            Transfer Information
        </h2>
        
        <div class="grid grid-cols-1 gap-4 mt-4 md:grid-cols-2">
            <div>
                <label class="text-sm font-medium text-base-content/70">Transfer Number</label>
                <p class="font-semibold">{{ $transferData['transfer_no'] }}</p>
            </div>
            
            <div>
                <label class="text-sm font-medium text-base-content/70">Status</label>
                <div>
                    <span class="badge {{ $this->getStatusBadgeClass($transferData['status']) }}">{{ is_object($transferData['status']) ? ucfirst($transferData['status']->value) : ucfirst($transferData['status']) }}</span>
                </div>
            </div>
            
            <div>
                <label class="text-sm font-medium text-base-content/70">Type</label>
                <p>{{ is_object($transferData['type']) ? ucfirst($transferData['type']->value) : ucfirst($transferData['type'] ?? '-') }}</p>
            </div>
            
            <div>
                <label class="text-sm font-medium text-base-content/70">From Branch</label>
                <p class="font-semibold">{{ $transferData['from_branch'] ?? 'N/A' }}</p>
            </div>
            
            <div>
                <label class="text-sm font-medium text-base-content/70">To Branch</label>
                <p class="font-semibold">{{ $transferData['to_branch'] ?? 'N/A' }}</p>
            </div>
            
            <div>
                <label class="text-sm font-medium text-base-content/70">Requested By</label>
                <p>{{ $transferData['requested_by'] ?? 'N/A' }}</p>
            </div>
    
            @if(!empty($transferData['requested_at']))
            <div>
                <label class="text-sm font-medium text-base-content/70">Requested Date</label>
                <p class="font-semibold">{{ is_object($transferData['requested_at']) ? $transferData['requested_at']->format('M d, Y H:i') : $transferData['requested_at'] }}</p>
            </div>
            @endif
            
            @if(!empty($transferData['description']))
            <div class="md:col-span-2">
                <label class="text-sm font-medium text-base-content/70">Description</label>
                <p class="font-semibold">{{ $transferData['description'] }}</p>
            </div>
            @endif
        </div>
        
        @if(!empty($transferData['reason']))
        <div class="mt-4">
            <label class="text-sm font-medium text-base-content/70">Reason</label>
            <p class="p-3 mt-1 text-sm rounded-lg bg-base-200 text-base-content">{{ $transferData['reason'] }}</p>
        </div>
        @endif
        
        @if(!empty($transferData['notes']))
        <div class="mt-4">
            <label class="text-sm font-medium text-base-content/70">Notes</label>
            <p class="p-3 mt-1 text-sm whitespace-pre-line rounded-lg bg-base-200 text-base-content">{{ $transferData['notes'] }}</p>
        </div>
        @endif
    </div>
</div>