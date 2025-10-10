<div class="card bg-base-100 shadow-xl">
    <div class="card-body">
        <h2 class="card-title">
            <i data-lucide="bar-chart-3" class="w-5 h-5"></i>
            Asset Statistics
        </h2>
        
        <div class="space-y-4">
            <!-- Asset Age -->
            <div class="stat">
                <div class="stat-figure text-primary">
                    <i data-lucide="calendar" class="w-8 h-8"></i>
                </div>
                <div class="stat-title">Umur Asset</div>
                <div class="stat-value text-primary text-lg">{{ $this->getAssetAge() }}</div>
                @if($asset->purchase_date)
                <div class="stat-desc">Sejak {{ \Carbon\Carbon::parse($asset->purchase_date)->locale('id')->translatedFormat('j F Y') }}</div>
                @endif
            </div>

            <div class="divider my-2"></div>

            <!-- Total Maintenances -->
            <div class="stat">
                <div class="stat-figure text-warning">
                    <i data-lucide="wrench" class="w-8 h-8"></i>
                </div>
                <div class="stat-title">Total Maintenance</div>
                <div class="stat-value text-warning text-lg">{{ $this->getTotalMaintenances() }}</div>
                <div class="stat-desc">{{ $this->getLastMaintenanceDate() }}</div>
            </div>

            <div class="divider my-2"></div>

            <!-- Total Transfers -->
            <div class="stat">
                <div class="stat-figure text-accent">
                    <i data-lucide="arrow-right-left" class="w-8 h-8"></i>
                </div>
                <div class="stat-title">Total Transfer</div>
                <div class="stat-value text-accent text-lg">{{ $this->getTotalTransfers() }}</div>
                <div class="stat-desc">Perpindahan cabang</div>
            </div>

            <div class="divider my-2"></div>

            <!-- Total Activity Logs -->
            <div class="stat">
                <div class="stat-figure text-info">
                    <i data-lucide="activity" class="w-8 h-8"></i>
                </div>
                <div class="stat-title">Total Aktivitas</div>
                <div class="stat-value text-info text-lg">{{ $this->getTotalLogs() }}</div>
                <div class="stat-desc">Log aktivitas</div>
            </div>
        </div>

        <!-- Asset Value Progress -->
        @if($asset->value)
        <div class="mt-4">
            <div class="flex justify-between text-sm">
                <span>Nilai Asset</span>
                <span class="font-semibold">Rp {{ number_format($asset->value, 0, ',', '.') }}</span>
            </div>
            <progress class="progress progress-primary w-full mt-2" value="100" max="100"></progress>
            <div class="text-xs text-base-content/70 mt-1">Asset dalam kondisi {{ $asset->condition->label() }}</div>
        </div>
        @endif
    </div>
</div>