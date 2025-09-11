@extends('layouts.dashboard')

@section('title', 'Asset Maintenance')

@section('content')
    <div class="space-y-6">
        <!-- Page Header -->
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Asset Maintenance</h1>
                <p class="text-gray-600">Manage and track asset maintenance activities</p>
            </div>
            <x-button icon="o-plus" class="btn-primary">
                Add Maintenance
            </x-button>
        </div>

        <!-- Kanban Board -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 h-[calc(100vh-200px)]">
            @php
                use App\Enums\MaintenanceStatus;
                $statusColumns = [
                    MaintenanceStatus::OPEN->value => ['label' => 'Open', 'color' => 'bg-info/10 border-info', 'badge' => 'badge-info'],
                    MaintenanceStatus::SCHEDULED->value => ['label' => 'Scheduled', 'color' => 'bg-warning/10 border-warning', 'badge' => 'badge-warning'],
                    MaintenanceStatus::IN_PROGRESS->value => ['label' => 'In Progress', 'color' => 'bg-primary/10 border-primary', 'badge' => 'badge-primary'],
                    MaintenanceStatus::COMPLETED->value => ['label' => 'Completed', 'color' => 'bg-success/10 border-success', 'badge' => 'badge-success'],
                    MaintenanceStatus::CANCELLED->value => ['label' => 'Cancelled', 'color' => 'bg-error/10 border-error', 'badge' => 'badge-error']
                ];
            @endphp

            @foreach($statusColumns as $status => $config)
                <div class="{{ $config['color'] }} border-2 rounded-lg p-4 flex flex-col">
                    <!-- Column Header -->
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="font-semibold text-base-content">{{ $config['label'] }}</h3>
                        <span class="badge {{ $config['badge'] }} badge-sm">
                            {{ $maintenances->where('status.value', $status)->count() }}
                        </span>
                    </div>

                    <!-- Cards Container -->
                    <div class="overflow-y-auto flex-1 space-y-3">
                        @foreach($maintenances->where('status.value', $status) as $maintenance)
                            <div class="border shadow-sm transition-shadow cursor-pointer card bg-base-100 border-base-300 hover:shadow-md">
                                <div class="p-4 card-body">
                                    <!-- Priority and Type Badges -->
                                    <div class="flex justify-between items-start mb-2">
                                        <span class="badge {{ $maintenance->priority->badgeColor() }} badge-xs">
                                            {{ $maintenance->priority->label() }}
                                        </span>
                                        @php
                                            $typeColor = match($maintenance->type) {
                                                App\Enums\MaintenanceType::PREVENTIVE => 'badge-info',
                                                App\Enums\MaintenanceType::CORRECTIVE => 'badge-warning',
                                            };
                                        @endphp
                                        <span class="badge {{ $typeColor }} badge-xs">
                                            {{ $maintenance->type->label() }}
                                        </span>
                                    </div>

                                    <!-- Title -->
                                    <h4 class="mb-2 text-sm font-medium text-base-content line-clamp-2">
                                        {{ $maintenance->title }}
                                    </h4>

                                    <!-- Asset Info -->
                                    <div class="mb-2 text-xs text-base-content/70">
                                        <div class="flex gap-1 items-center">
                                            <x-icon name="o-cube" class="w-3 h-3" />
                                            <span>{{ $maintenance->asset->name ?? 'N/A' }}</span>
                                        </div>
                                    </div>

                                    <!-- Description -->
                                    @if($maintenance->description)
                                        <p class="mb-3 text-xs text-base-content/60 line-clamp-2">
                                            {{ Str::limit($maintenance->description, 80) }}
                                        </p>
                                    @endif

                                    <!-- Dates and Cost -->
                                    <div class="space-y-1 text-xs text-base-content/60">
                                        @if($maintenance->scheduled_date)
                                            <div class="flex gap-1 items-center">
                                                <x-icon name="o-calendar" class="w-3 h-3" />
                                                <span>{{ $maintenance->scheduled_date->format('M d, Y') }}</span>
                                            </div>
                                        @endif
                                        @if($maintenance->completed_date)
                                            <div class="flex gap-1 items-center">
                                                <x-icon name="o-check-circle" class="w-3 h-3" />
                                                <span>Completed: {{ $maintenance->completed_date->format('M d, Y') }}</span>
                                            </div>
                                        @endif
                                        @if($maintenance->cost)
                                            <div class="flex gap-1 items-center">
                                                <x-icon name="o-currency-dollar" class="w-3 h-3" />
                                                <span>${{ number_format($maintenance->cost, 2) }}</span>
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Assigned User -->
                                    @if($maintenance->assignedUser)
                                        <div class="flex gap-2 items-center pt-2 mt-3 border-t border-base-300">
                                            <div class="avatar placeholder">
                                                <div class="w-6 h-6 rounded-full bg-neutral text-neutral-content">
                                                    <span class="text-xs">{{ substr($maintenance->assignedUser->name, 0, 1) }}</span>
                                                </div>
                                            </div>
                                            <span class="text-xs text-base-content/70">{{ $maintenance->assignedUser->name }}</span>
                                        </div>
                                    @endif

                                    <!-- Notes Preview -->
                                    @if($maintenance->notes)
                                        <div class="pt-2 mt-2 border-t border-base-300">
                                            <p class="text-xs italic text-base-content/60">
                                                {{ Str::limit($maintenance->notes, 60) }}
                                            </p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach

                        <!-- Empty State -->
                        @if($maintenances->where('status.value', $status)->isEmpty())
                            <div class="py-8 text-center text-base-content/40">
                                <x-icon name="o-inbox" class="mx-auto mb-2 w-8 h-8 opacity-50" />
                                <p class="text-sm">No {{ strtolower($config['label']) }} maintenance</p>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection