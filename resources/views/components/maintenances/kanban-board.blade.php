@props(['statusColumns'])

<div class="h-full">
    <!-- Kanban Board Container -->
    <div class="flex overflow-x-auto gap-3 h-full">
        @foreach($statusColumns as $column)
            <div class="flex flex-col flex-shrink-0 w-64 min-h-0 rounded-lg border min-w-64 border-base-300">
                <x-maintenances.kanban-column 
                    :status="$column['status']" 
                    :title="$column['status']->label()" 
                    :maintenances="collect($column['maintenances'])" 
                    :badge-color-class="$column['status']->badgeColor()" 
                />
            </div>
        @endforeach
    </div>
</div>