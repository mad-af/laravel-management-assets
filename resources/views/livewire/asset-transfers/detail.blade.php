<div class="space-y-6">
    <!-- Detail Information Component -->
    @include('livewire.asset-transfers.components.detail-info', ['transfer' => $transfer])

    <!-- Items Table Component -->
    @include('livewire.asset-transfers.components.items-table', ['transfer' => $transfer])

    <!-- Quick Actions Component -->
    @include('livewire.asset-transfers.components.quick-actions', ['transfer' => $transfer])

    <!-- Timeline Component -->
    @include('livewire.asset-transfers.components.timeline', ['transfer' => $transfer])

    <!-- Modal Change Status -->
    <x-modal wire:model="showStatusModal" title="Change Transfer Status">
        <div class="space-y-4">
            <x-select 
                label="New Status" 
                wire:model="newStatus" 
                :options="$this->getStatusOptions()" 
                option-value="value" 
                option-label="label" 
                placeholder="Select status" />
            
            <x-textarea 
                label="Reason (Optional)" 
                wire:model="statusReason" 
                placeholder="Enter reason for status change..." 
                rows="3" />
        </div>
        
        <x-slot:actions>
            <x-button label="Cancel" wire:click="$set('showStatusModal', false)" />
            <x-button label="Update Status" class="btn-primary" wire:click="updateStatus" />
        </x-slot:actions>
    </x-modal>
</div>