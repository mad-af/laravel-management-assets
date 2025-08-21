@props(['asset', 'class' => ''])

<div class="shadow-xl card bg-base-100 {{ $class }}">
    <div class="card-body">
        <h3 class="mb-4 text-lg card-title">Asset Statistics</h3>

        <div class="space-y-4">
            <div class="stat">
                <div class="stat-title">Total Logs</div>
                <div class="text-2xl stat-value">{{ $asset->logs->count() }}</div>
                <div class="stat-desc">Activity records</div>
            </div>

            <div class="stat">
                <div class="stat-title">Days Since Purchase</div>
                <div class="text-2xl stat-value">
                    {{ $asset->purchase_date ? $asset->purchase_date->diffInDays(now()) : 'N/A' }}
                </div>
                <div class="stat-desc">{{ $asset->purchase_date ? 'days old' : 'No purchase date' }}</div>
            </div>

            <div class="stat">
                <div class="stat-title">Last Activity</div>
                <div class="text-lg stat-value">
                    {{ $asset->logs->first() ? $asset->logs->first()->created_at->diffForHumans() : 'No activity' }}
                </div>
                <div class="stat-desc">{{ $asset->logs->first() ? $asset->logs->first()->action : '' }}</div>
            </div>
        </div>
    </div>
</div>