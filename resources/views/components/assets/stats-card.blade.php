@props(['asset', 'class' => ''])

<div class="shadow-xl card bg-base-100 {{ $class }}">
    <div class="card-body">
        <h3 class="mb-6 card-title text-base-content">
            <i data-lucide="bar-chart-3" class="w-5 h-5 mr"></i>
            Asset Statistics
        </h3>

        <div class="shadow-sm stats stats-vertical">
            <div class="place-items-start stat">
                <div class="stat-figure text-primary">
                    <i data-lucide="activity" class="w-8 h-8"></i>
                </div>
                <div class="stat-title text-base-content/70">Total Logs</div>
                <div class="text-3xl font-bold stat-value text-primary">{{ $asset->logs->count() }}</div>
                <div class="stat-desc text-base-content/60">Activity records</div>
            </div>

            <div class="place-items-start stat">
                <div class="stat-figure text-secondary">
                    <i data-lucide="calendar-days" class="w-8 h-8"></i>
                </div>
                <div class="stat-title text-base-content/70">Days Since Purchase</div>
                <div class="text-3xl font-bold stat-value text-secondary">
                    @if($asset->purchase_date)
                        @php
                            $now = now();
                            $purchaseDate = $asset->purchase_date;

                            $totalHours = $purchaseDate->diffInHours($now);
                            $totalDays = $purchaseDate->diffInDays($now);
                            $totalMonths = $purchaseDate->diffInMonths($now);
                            $totalYears = $purchaseDate->diffInYears($now);

                            if ($totalYears >= 1) {
                                $display = number_format(round($totalDays / 365.25, 1), 1) . ' years';
                            } elseif ($totalMonths >= 1) {
                                $display = number_format(round($totalDays / 30.44, 1), 1) . ' months';
                            } else {
                                $display = number_format($totalDays, 1) . ' days';
                            }
                        @endphp
                        {{ $display }}
                    @else
                        N/A
                    @endif
                </div>
                <div class="stat-desc text-base-content/60">
                    {{ $asset->purchase_date ? 'Purchased on ' . $asset->purchase_date->format('M d, Y') : 'No purchase date' }}
                </div>
            </div>

            <div class="place-items-start stat">
                <div class="stat-figure text-accent">
                    <i data-lucide="clock" class="w-8 h-8"></i>
                </div>
                <div class="stat-title text-base-content/70">Last Activity</div>
                <div class="text-xl font-semibold stat-value text-accent">
                    {{ $asset->logs->first() ? $asset->logs->first()->created_at->diffForHumans() : 'No activity' }}
                </div>
                <div class="stat-desc text-base-content/60">
                    {{ $asset->logs->first() ? ucfirst($asset->logs->first()->action->label()) : 'No recent activity' }}
                </div>
            </div>
        </div>
    </div>
</div>