<x-info-card title="Statistik Aset" icon="o-chart-bar">
    <div class="w-full shadow stats stats-vertical lg:stats-horizontal">
        <div class="w-60 stat">
            <div class="stat-figure text-primary"><x-icon name="o-cube" class="w-6 h-6" /></div>
            <div class="stat-title">Total Aset</div>
            <div class="stat-value text-primary">{{ $totalAssets }}</div>
            <div class="stat-desc">Terdaftar di cabang saat ini</div>
        </div>
        <div class="w-60 stat">
            <div class="stat-figure text-success"><x-icon name="o-check-circle" class="w-6 h-6" /></div>
            <div class="stat-title">Aset Aktif</div>
            <div class="stat-value text-success">{{ $activeAssets }}</div>
            <div class="stat-desc">Siap digunakan</div>
        </div>
        <div class="w-60 stat">
            <div class="stat-figure text-warning"><x-icon name="o-wrench-screwdriver" class="w-6 h-6" /></div>
            <div class="stat-title">Dalam Perawatan</div>
            <div class="stat-value text-warning">{{ $maintenanceAssets }}</div>
            <div class="stat-desc">Proses maintenance berjalan</div>
        </div>
        <div class="w-60 stat">
            <div class="stat-figure text-info"><x-icon name="o-hand-raised" class="w-6 h-6" /></div>
            <div class="stat-title">Sedang Dipinjam</div>
            <div class="stat-value text-info">{{ $onLoanAssets }}</div>
            <div class="stat-desc">Pinjaman aktif</div>
        </div>
        <div class="w-60 stat">
            <div class="stat-figure text-primary"><x-icon name="o-arrows-right-left" class="w-6 h-6" /></div>
            <div class="stat-title">Dalam Transfer</div>
            <div class="stat-value text-primary">{{ $inTransferAssets }}</div>
            <div class="stat-desc">Perpindahan antar cabang</div>
        </div>
        <div class="w-60 stat">
            <div class="stat-figure text-secondary"><x-icon name="o-truck" class="w-6 h-6" /></div>
            <div class="stat-title">Kendaraan</div>
            <div class="stat-value text-secondary">{{ $vehiclesCount }}</div>
            <div class="stat-desc">Profil kendaraan aktif</div>
        </div>
    </div>
</x-info-card>