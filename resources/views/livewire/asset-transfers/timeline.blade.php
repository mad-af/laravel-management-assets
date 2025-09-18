@props(['transfer'])

<div class="shadow-xl card card-compact bg-base-100">
    <div class="card-body">
        <h2 class="text-lg card-title">
            <x-icon name="o-clock" class="w-5 h-5" />
            Transfer Timeline
        </h2>
        
        <div class="pl-3.5 mt-4">
            
            <x-timeline-item 
                title="Transfer Created" 
                subtitle="{{ $timelineData['created_at']->format('M d, Y H:i') }}" 
                description="by {{ $timelineData['requested_by'] ?? 'System' }}" 
                icon="o-plus-circle" 
                first 
            />
            
            @if($timelineData['scheduled_at'])
            <x-timeline-item 
                title="Transfer Scheduled" 
                subtitle="{{ $timelineData['scheduled_at']->format('M d, Y H:i') }}" 
                icon="o-calendar" 
            />
            @endif
            
            @if($timelineData['approved_at'])
            <x-timeline-item 
                title="Transfer Approved" 
                subtitle="{{ $timelineData['approved_at']->format('M d, Y H:i') }}" 
                description="by {{ $timelineData['approved_by'] ?? 'System' }}" 
                icon="o-check-circle" 
            />
            @endif
            
            @if($timelineData['executed_at'])
            <x-timeline-item 
                title="Transfer Executed" 
                subtitle="{{ $timelineData['executed_at']->format('M d, Y H:i') }}" 
                icon="o-truck" 
                last 
            />
            @else
            <x-timeline-item 
                title="Transfer Executed" 
                subtitle="Pending execution" 
                icon="o-truck" 
                pending 
                last 
            />
            @endif
        </div>
    </div>
</div>