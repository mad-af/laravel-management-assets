<div class="card bg-base-100 shadow-xl">
    <div class="card-body">
        <div class="flex justify-between items-center">
            <h2 class="card-title">
                <i data-lucide="activity" class="w-5 h-5"></i>
                Activity Log
            </h2>
            
            @if($logs->count() > 0)
            <button wire:click="toggleShowAll" class="btn btn-ghost btn-sm">
                @if($showAll)
                    <i data-lucide="eye-off" class="w-4 h-4"></i>
                    Show Less
                @else
                    <i data-lucide="eye" class="w-4 h-4"></i>
                    Show All
                @endif
            </button>
            @endif
        </div>
        
        @if($logs->count() > 0)
            <div class="space-y-4 mt-4">
                @foreach($logs as $log)
                <div class="flex items-start space-x-3">
                    <!-- Timeline dot -->
                    <div class="flex-shrink-0 w-3 h-3 rounded-full bg-primary mt-2"></div>
                    
                    <!-- Content -->
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-2">
                                <span class="badge {{ $this->getActionBadgeClass($log->action->value) }} badge-sm">
                                    {{ $this->getActionLabel($log->action->value) }}
                                </span>
                                @if($log->user)
                                <span class="text-sm text-base-content/70">
                                    oleh {{ $log->user->name }}
                                </span>
                                @endif
                            </div>
                            <time class="text-xs text-base-content/50">
                                {{ \Carbon\Carbon::parse($log->created_at)->locale('id')->diffForHumans() }}
                            </time>
                        </div>
                        
                        @if($log->description)
                        <p class="text-sm text-base-content/80 mt-1">{{ $log->description }}</p>
                        @endif
                        
                        @if($log->old_values || $log->new_values)
                        <div class="mt-2 text-xs">
                            @if($log->old_values)
                            <div class="text-error">
                                <strong>Sebelum:</strong> {{ json_encode($log->old_values) }}
                            </div>
                            @endif
                            @if($log->new_values)
                            <div class="text-success">
                                <strong>Sesudah:</strong> {{ json_encode($log->new_values) }}
                            </div>
                            @endif
                        </div>
                        @endif
                    </div>
                </div>
                
                @if(!$loop->last)
                <div class="ml-1.5 w-px h-4 bg-base-300"></div>
                @endif
                @endforeach
            </div>
            
            @if($showAll && method_exists($logs, 'links'))
            <div class="mt-4">
                {{ $logs->links() }}
            </div>
            @endif
        @else
            <div class="text-center py-8">
                <i data-lucide="file-x" class="w-12 h-12 mx-auto text-base-content/30"></i>
                <p class="text-base-content/70 mt-2">Belum ada aktivitas untuk asset ini</p>
            </div>
        @endif
    </div>
</div>