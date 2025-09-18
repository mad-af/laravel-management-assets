@props(['transfer'])

<!-- Card Detail Informasi AssetTransfer -->
<div class="shadow-xl card card-compact bg-base-100">
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
                    <span class="badge {{ $this->getStatusBadgeClass($transferData['status']) }}">{{ ucfirst($transferData['status']->value) }}</span>
                </div>
            </div>
            
            <div>
                <label class="text-sm font-medium text-base-content/70">Priority</label>
                <div>
                    <span class="badge {{ $this->getPriorityBadgeClass($transferData['priority']) }}">{{ ucfirst($transferData['priority']->value) }}</span>
                </div>
            </div>
            
            <div>
                <label class="text-sm font-medium text-base-content/70">Type</label>
                <p >{{ ucfirst($transferData['type']->value) }}</p>
            </div>
            
            <div>
                <label class="text-sm font-medium text-base-content/70">From Location</label>
                <p class="font-semibold">{{ $transferData['from_location'] ?? 'N/A' }}</p>
            </div>
            
            <div>
                <label class="text-sm font-medium text-base-content/70">To Location</label>
                <p class="font-semibold">{{ $transferData['to_location'] ?? 'N/A' }}</p>
            </div>
            
            <div>
                <label class="text-sm font-medium text-base-content/70">Requested By</label>
                <p >{{ $transferData['requested_by'] ?? 'N/A' }}</p>
            </div>
            
            <div>
                <label class="text-sm font-medium text-base-content/70">Company</label>
                <p>{{ $transferData['company'] ?? 'N/A' }}</p>
            </div>
            
            @if($transferData['scheduled_at'])
            <div>
                <label class="text-sm font-medium text-base-content/70">Scheduled Date</label>
                <p class="font-semibold">{{ $transferData['scheduled_at']->format('M d, Y H:i') }}</p>
            </div>
            @endif
            
            @if($transferData['requested_at'])
            <div>
                <label class="text-sm font-medium text-base-content/70">Requested Date</label>
                <p class="font-semibold">{{ $transferData['requested_at']->format('M d, Y H:i') }}</p>
            </div>
            @endif
            
            @if($transferData['description'])
            <div class="md:col-span-2">
                <label class="text-sm font-medium text-base-content/70">Description</label>
                <p class="font-semibold">{{ $transferData['description'] }}</p>
            </div>
            @endif
        </div>
        
        @if($transferData['reason'])
        <div class="mt-4">
            <label class="text-sm font-medium text-base-content/70">Reason</label>
            <p class="p-3 mt-1 text-sm rounded-lg bg-base-200 text-base-content">{{ $transferData['reason'] }}</p>
        </div>
        @endif
        
        @if($transferData['notes'])
        <div class="mt-4">
            <label class="text-sm font-medium text-base-content/70">Notes</label>
            <p class="p-3 mt-1 text-sm whitespace-pre-line rounded-lg bg-base-200 text-base-content">{{ $transferData['notes'] }}</p>
        </div>
        @endif
    </div>
</div>