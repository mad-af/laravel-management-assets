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

        // Header title
        $data->push((object)[
            'type' => 'title',
            'label' => strtoupper($this->asset->name ?? 'Asset'),
            'value' => '',
        ]);

        // Detail section
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
            $data->push((object)[
                'type' => 'asset_detail',
                'label' => $label,
                'value' => $value,
            ]);
        }

        // Separator
        $data->push((object)['type' => 'separator']);

        // Activity log header
        $data->push((object)['type' => 'log_header', 'label' => 'ACTIVITY LOG']);

        // Logs
        foreach ($this->logs as $log) {
            $data->push($log);
        }

        return $data;
    }

    public function headings(): array
    {
        return ['Tanggal/Waktu', 'Aksi', 'User', 'Catatan', 'Perubahan Data'];
    }

    public function map($row): array
    {
        if (isset($row->type)) {
            if ($row->type === 'title') {
                return [$row->label, '', '', '', ''];
            }

            if ($row->type === 'asset_detail') {
                return [$row->label, $row->value, '', '', ''];
            }

            if ($row->type === 'separator') {
                return ['', '', '', '', ''];
            }

            if ($row->type === 'log_header') {
                return ['ACTIVITY LOG', '', '', '', ''];
            }
        }

        // Log rows
        $changed = '';
        if (is_array($row->changed_fields)) {
            $pairs = [];
            foreach ($row->changed_fields as $f => $c) {
                if (isset($c['old'], $c['new'])) {
                    $pairs[] = "{$f}: {$c['old']} → {$c['new']}";
                }
            }
            $changed = implode('; ', $pairs);
        }

        return [
            $row->created_at ? $row->created_at->format('d/m/Y H:i:s') : '-',
            $row->action?->label() ?? $row->action ?? '-',
            $row->user->name ?? '-',
            $row->notes ?? '-',
            $changed,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $highestRow = $sheet->getHighestRow();

        // General style
        $sheet->getDefaultRowDimension()->setRowHeight(20);
        $sheet->getStyle("A1:E{$highestRow}")
            ->getAlignment()
            ->setVertical(Alignment::VERTICAL_CENTER)
            ->setWrapText(true);

        // Title row (row 1)
        $sheet->mergeCells('A1:E1');
        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 16],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        // Asset detail block (2–14)
        $sheet->getStyle('A2:B14')->applyFromArray([
            'font' => ['bold' => true],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'E3F2FD'],
            ],
        ]);

        // ACTIVITY LOG header (find dynamically)
        $logHeaderRow = null;
        for ($r = 1; $r <= $highestRow; $r++) {
            if ($sheet->getCell("A{$r}")->getValue() === 'ACTIVITY LOG') {
                $logHeaderRow = $r;
                break;
            }
        }

        if ($logHeaderRow) {
            $sheet->mergeCells("A{$logHeaderRow}:E{$logHeaderRow}");
            $sheet->getStyle("A{$logHeaderRow}:E{$logHeaderRow}")->applyFromArray([
                'font' => ['bold' => true, 'size' => 13, 'color' => ['rgb' => '0D47A1']],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'BBDEFB'],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ]);
        }

        // Header columns (log table)
        $headerRow = $logHeaderRow + 1;
        $sheet->getStyle("A{$headerRow}:E{$headerRow}")->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '1976D2'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        // Borders for the entire log table
        $sheet->getStyle("A{$headerRow}:E{$highestRow}")
            ->getBorders()
            ->getAllBorders()
            ->setBorderStyle(Border::BORDER_THIN);
            // ->setColor(['rgb' => 'B0BEC5']);

        // Column sizing
        foreach (range('A', 'E') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $sheet->getColumnDimension('D')->setWidth(35);
        $sheet->getColumnDimension('E')->setWidth(45);

        return [];
    }

    public function title(): string
    {
        return 'Activity Log - ' . ($this->asset->name ?? 'Asset');
    }
}
