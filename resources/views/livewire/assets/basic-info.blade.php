@php
    $imageItems = [
        ['label' => 'Gambar Asset', 'path' => $asset->image],
    ];

    $items = [
        ['label' => 'Kode Asset', 'value' => $asset->code],
        ['label' => 'Nama Asset', 'value' => $asset->name],
        ['label' => 'Kategori', 'value' => $asset->category->name ?? 'N/A'],
        ['label' => 'Cabang', 'value' => $asset->branch->name ?? 'N/A'],
        ['label' => 'Serial Number', 'value' => $asset->serial_number ?? 'N/A'],
    ];

    // Add tag code if exists
    if ($asset->tag_code) {
        $items[] = ['label' => 'Tag Code', 'value' => $asset->tag_code];
    }

    // Add brand if exists
    if ($asset->brand) {
        $items[] = ['label' => 'Brand', 'value' => $asset->brand];
    }

    // Add model if exists
    if ($asset->model) {
        $items[] = ['label' => 'Model', 'value' => $asset->model];
    }

    // Add status with badge
    $items[] = [
        'label' => 'Status',
        'value' => $asset->status->label(),
        'badge' => true,
        'badge_class' => 'badge-'.$asset->status->color()
    ];

    // Add condition with badge
    $items[] = [
        'label' => 'Kondisi',
        'value' => $asset->condition->label(),
        'badge' => true,
        'badge_class' => 'badge-outline badge-'.$asset->condition->color()
    ];

    // Add borrower info if asset is currently on loan
    if ($asset->currentLoan) {
        $borrowerName = $asset->currentLoan->employee?->full_name
            ?? $asset->currentLoan->borrower_name
            ?? null;

        if ($borrowerName) {
            $borrowerValue = $borrowerName;
            if ($asset->currentLoan->due_at) {
                $dueDate = \Carbon\Carbon::parse($asset->currentLoan->due_at)->locale('id')->translatedFormat('j F Y');
                $isOverdue = $asset->currentLoan->isOverdue();
                $overdueClass = $isOverdue ? ' text-error font-semibold' : '';
                $overdueBadge = $isOverdue ? ' <span class="badge badge-error badge-sm">Terlambat</span>' : '';
                $borrowerValue .= ' (Tempo: ' . $dueDate . ')' . $overdueBadge;
            }
            $items[] = [
                'label' => 'Peminjam',
                'value' => $borrowerValue,
            ];
        }
    }

    // Add maintenance overdue info (only for vehicles)
    if ($asset->vehicleProfile) {
        $overdueInfo = $asset->getMaintenanceOverdueInfo();
        $nextServiceDate = $asset->vehicleProfile->next_service_date;
        $nextServiceKm = $asset->vehicleProfile->service_target_odometer_km;

        if ($overdueInfo) {
            $items[] = [
                'label' => 'Status Perawatan',
                'value' => $overdueInfo['message'] . ' <span class="badge badge-error badge-sm">Terlambat</span>',
            ];
        } elseif ($nextServiceDate) {
            $now = \Carbon\Carbon::now();
            $days = (int) $nextServiceDate->startOfDay()->diffInDays($now->startOfDay(), false);
            $badge = '';
            if ($days <= 7 && $days > 0) {
                $badge = ' <span class="badge badge-warning badge-sm">Segera (' . $days . ' hari lagi)</span>';
            } elseif ($days > 0) {
                $badge = ' <span class="badge badge-info badge-sm">' . $days . ' hari lagi</span>';
            }
            $items[] = [
                'label' => 'Perawatan Berikutnya',
                'value' => $nextServiceDate->locale('id')->translatedFormat('j F Y') . $badge,
            ];
        }
    }

    // Add value if exists
    if ($asset->value) {
        $items[] = ['label' => 'Nilai Asset', 'value' => 'Rp ' . number_format($asset->value, 0, ',', '.')];
    }

    // Add purchase date if exists
    if ($asset->purchase_date) {
        $items[] = ['label' => 'Tanggal Pembelian', 'value' => \Carbon\Carbon::parse($asset->purchase_date)->locale('id')->translatedFormat('j F Y')];
    }

    $longTextItems = [];
    
    // Add description if exists
    if ($asset->description) {
        $longTextItems[] = ['label' => 'Deskripsi', 'value' => $asset->description];
    }
@endphp

<x-info-grid 
    title="Informasi Dasar" 
    icon="o-information-circle"
    :items="$items"
    :longTextItems="$longTextItems"
    :imageItems="$imageItems"
>
    <!-- Asset Image -->
    @if($asset->image)
    <div class="mt-4">
        <label class="text-sm font-medium text-base-content/70">Gambar Asset</label>
        <div class="mt-2">
            <img src="{{ asset('storage/' . $asset->image) }}" 
                 alt="{{ $asset->name }}" 
                 class="object-cover w-full max-w-md h-48 rounded-lg border">
        </div>
    </div>
    @endif
</x-info-grid>