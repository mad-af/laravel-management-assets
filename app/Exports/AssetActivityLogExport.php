<?php

namespace App\Exports;

use App\Models\Asset;
use App\Models\AssetLog;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AssetActivityLogExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle
{
    protected $asset;
    protected $logs;

    public function __construct(Asset $asset)
    {
        $this->asset = $asset->load(['category', 'branch', 'company']);
        $this->logs = AssetLog::where('asset_id', $asset->id)
            ->with(['user'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function collection()
    {
        $data = collect();

        // === Asset details block ===
        $details = [
            'Kode Asset' => $this->asset->code ?? '-',
            'Tag Code' => $this->asset->tag_code ?? '-',
            'Nama Asset' => $this->asset->name ?? '-',
            'Kategori' => $this->asset->category->name ?? '-',
            'Brand' => $this->asset->brand ?? '-',
            'Model' => $this->asset->model ?? '-',
            'Status' => $this->asset->status?->label() ?? '-',
            'Kondisi' => $this->asset->condition?->label() ?? '-',
            'Nilai' => $this->asset->value ? 'Rp '.number_format($this->asset->value, 0, ',', '.') : '-',
            'Tanggal Pembelian' => $this->asset->purchase_date ? $this->asset->purchase_date->format('d/m/Y') : '-',
            'Lokasi' => $this->asset->branch->name ?? '-',
            'Perusahaan' => $this->asset->company->name ?? '-',
            'Deskripsi' => $this->asset->description ?? '-',
        ];

        foreach ($details as $label => $value) {
            $data->push((object) [
                'type'  => 'asset_detail',
                'label' => $label,
                'value' => $value,
            ]);
        }

        // Separator
        $data->push((object) ['type' => 'separator']);

        // Activity Log title row
        $data->push((object) ['type' => 'log_header', 'label' => 'ACTIVITY LOG']);

        // Column headers for the log table (explicit row)
        $data->push((object) ['type' => 'log_columns']);

        // Activity log rows
        foreach ($this->logs as $log) {
            $data->push($log);
        }

        return $data;
    }

    public function headings(): array
    {
        // Single top heading row: put asset name in A1 only; others blank
        return [
            strtoupper($this->asset->name ?? 'ASSET'),
            '', '', '', ''
        ];
    }

    public function map($row): array
    {
        if (isset($row->type)) {
            if ($row->type === 'asset_detail') {
                return [$row->label, $row->value, '', '', ''];
            }
            if ($row->type === 'separator') {
                return ['', '', '', '', ''];
            }
            if ($row->type === 'log_header') {
                return ['ACTIVITY LOG', '', '', '', ''];
            }
            if ($row->type === 'log_columns') {
                return ['Tanggal/Waktu', 'Aksi', 'User', 'Catatan', 'Perubahan Data'];
            }
        }

        // Map real log row
        $changedFields = '';
        if ($row->changed_fields && is_array($row->changed_fields)) {
            $pairs = [];
            foreach ($row->changed_fields as $field => $change) {
                if (is_array($change) && isset($change['old'], $change['new'])) {
                    $pairs[] = "{$field}: {$change['old']} → {$change['new']}";
                } else {
                    $pairs[] = "{$field}: ".(is_string($change) ? $change : json_encode($change));
                }
            }
            $changedFields = implode('; ', $pairs);
        }

        return [
            $row->created_at ? $row->created_at->format('d/m/Y H:i:s') : '-',
            $row->action?->label() ?? $row->action ?? '-',
            $row->user->name ?? '-',
            $row->notes ?? '-',
            $changedFields,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $highestRow = $sheet->getHighestRow();

        // Title row A1:E1 (asset name)
        $sheet->mergeCells('A1:E1');
        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 16],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER,
            ],
        ]);

        // Find key rows dynamically
        $logHeaderRow = null; // row containing "ACTIVITY LOG"
        for ($r = 1; $r <= $highestRow; $r++) {
            if ($sheet->getCell("A{$r}")->getValue() === 'ACTIVITY LOG') {
                $logHeaderRow = $r;
                break;
            }
        }

        // Detail block (A2:B15) shaded — includes Deskripsi row
        $sheet->getStyle('A2:B14')->applyFromArray([
            'font' => ['bold' => true],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'E3F2FD'],
            ],
        ]);

        if ($logHeaderRow) {
            // Style ACTIVITY LOG title (merged A..E)
            $sheet->mergeCells("A{$logHeaderRow}:E{$logHeaderRow}");
            $sheet->getStyle("A{$logHeaderRow}:E{$logHeaderRow}")->applyFromArray([
                'font' => ['bold' => true, 'size' => 13, 'color' => ['rgb' => '0D47A1']],
                'fill' => [
                    'fillType'   => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'BBDEFB'],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical'   => Alignment::VERTICAL_CENTER,
                ],
            ]);

            // Actual table header row (next row)
            $headerRow = $logHeaderRow + 1; // this is A18:E18 in your screenshot
            $sheet->getStyle("A{$headerRow}:E{$headerRow}")->applyFromArray([
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType'   => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '1976D2'],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical'   => Alignment::VERTICAL_CENTER,
                ],
            ]);

            // Borders for the log table (from header row downwards)
            $sheet->getStyle("A{$headerRow}:E{$highestRow}")
                  ->getBorders()->getAllBorders()
                  ->setBorderStyle(Border::BORDER_THIN);
        }

        // Column widths
        foreach (range('A', 'E') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        $sheet->getColumnDimension('D')->setWidth(35);
        $sheet->getColumnDimension('E')->setWidth(45);

        // Row height & wrap
        $sheet->getDefaultRowDimension()->setRowHeight(20);
        $sheet->getStyle("A1:E{$highestRow}")->getAlignment()->setWrapText(true);

        return [];
    }

    public function title(): string
    {
        return 'Activity Log - ' . ($this->asset->name ?? 'Asset');
    }
}
