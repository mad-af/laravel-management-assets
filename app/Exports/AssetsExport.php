<?php

namespace App\Exports;

use App\Models\Asset;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AssetsExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected $branchId;

    public function __construct($branchId)
    {
        $this->branchId = $branchId;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return Asset::with(['category', 'branch', 'vehicleProfile'])
            ->where('branch_id', $this->branchId)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'Kode Asset',
            'Tag Code',
            'Nama Asset',
            'Kategori',
            'Cabang',
            'Brand',
            'Model',
            'Status',
            'Kondisi',
            'Nilai (Rp)',
            'Tanggal Pembelian',
            'Deskripsi',
            'Plat Nomor',
            'VIN',
            'Odometer (KM)',
            'Terakhir Dilihat',
            'Dibuat Pada',
        ];
    }

    /**
     * @param mixed $asset
     * @return array
     */
    public function map($asset): array
    {
        return [
            $asset->code,
            $asset->tag_code,
            $asset->name,
            $asset->category->name ?? '-',
            $asset->branch->name ?? '-',
            $asset->brand,
            $asset->model,
            $asset->status->value ?? '-',
            $asset->condition->value ?? '-',
            number_format($asset->value, 0, ',', '.'),
            $asset->purchase_date ? $asset->purchase_date->format('d/m/Y') : '-',
            $asset->description ?? '-',
            $asset->vehicleProfile->plate_no ?? '-',
            $asset->vehicleProfile->vin ?? '-',
            $asset->vehicleProfile->current_odometer_km ?? '-',
            $asset->last_seen_at ? $asset->last_seen_at->format('d/m/Y H:i') : '-',
            $asset->created_at->format('d/m/Y H:i'),
        ];
    }

    /**
     * @param Worksheet $sheet
     * @return array
     */
    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text.
            1 => ['font' => ['bold' => true]],
        ];
    }
}