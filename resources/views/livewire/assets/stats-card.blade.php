<x-info-card title="Informasi Statistik" icon="o-presentation-chart-bar">
    <div class="w-full shadow stats stats-vertical">
        <!-- Asset Age -->
        <x-stat-card 
            title="Umur Asset"
            value="{{ $this->getAssetAge() }}"
            description="Sejak tanggal pembelian"
            icon="o-calendar-days"
            iconColor="text-primary"
            valueColor="text-primary"
        />
        
        <!-- Total Maintenances -->
        <x-stat-card 
            title="Total Perawatan"
            value="{{ $this->getTotalMaintenances() }}"
            description="Perawatan yang dilakukan"
            icon="o-wrench-screwdriver"
            iconColor="text-secondary"
            valueColor="text-secondary"
        />
        
        <!-- Total Transfers -->
        <x-stat-card 
            title="Total Transfer"
            value="{{ $this->getTotalTransfers() }}"
            description="Perpindahan lokasi"
            icon="o-arrows-right-left"
            iconColor="text-accent"
            valueColor="text-accent"
            iconPosition="right"
        />
        
        <!-- Total Activity Logs -->
        <x-stat-card 
            title="Log Aktivitas"
            value="{{ $this->getTotalLogs() }}"
            description="Total aktivitas tercatat"
            icon="o-document-text"
            iconColor="text-info"
            valueColor="text-info"
            iconPosition="right"
        />
        
        <!-- Asset Value Progress -->
        {{-- <x-stat-card 
            title="Nilai Asset"
            value="Rp {{ number_format($asset->value ?? 0, 0, ',', '.') }}"
            description="Nilai pembelian"
            icon="o-banknotes"
            iconColor="text-success"
            valueColor="text-success"
        /> --}}
    </div>
</x-info-card>