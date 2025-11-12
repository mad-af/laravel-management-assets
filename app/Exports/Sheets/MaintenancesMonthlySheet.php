<?php

namespace App\Exports\Sheets;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class MaintenancesMonthlySheet implements FromCollection, ShouldAutoSize, WithHeadings, WithMapping, WithStyles, WithTitle
{
    protected string $title;
    protected Collection $items;

    public function __construct(string $title, Collection $items)
    {
        $this->title = $title;
        $this->items = $items;
    }

    public function title(): string
    {
        return $this->title;
    }

    public function collection()
    {
        return $this->items;
    }

    public function headings(): array
    {
        return [
            'Kode Perawatan',
            'Kode Asset',
            'Nama Asset',
            'Judul',
            'Tipe',
            'Status',
            'Prioritas',
            'Mulai',
            'Estimasi Selesai',
            'Selesai',
            'Biaya (Rp)',
            'Teknisi',
            'Vendor',
            'Odometer (KM)',
            'Catatan',
            'Service Tasks',
            'Service Details',
        ];
    }

    public function map($m): array
    {
        // Normalize service tasks to a readable string
        $tasks = '-';
        if (is_array($m->service_tasks) && !empty($m->service_tasks)) {
            $tasks = implode('; ', array_map(function ($t) {
                $text = is_array($t) ? ($t['task'] ?? '') : (string) $t;
                $text = trim((string) $text);
                if ($text === '') {
                    return null;
                }
                $prefix = (is_array($t) && !empty($t['completed'])) ? '✓ ' : '';
                return $prefix . $text;
            }, array_filter($m->service_tasks, function ($t) {
                // Keep only items that have some text value
                if (is_array($t)) {
                    return isset($t['task']) && trim((string) $t['task']) !== '';
                }
                return is_string($t) && trim($t) !== '';
            })));
            if ($tasks === '') {
                $tasks = '-';
            }
        }

        // Normalize service details to a readable string
        $details = '-';
        if (is_array($m->service_details) && !empty($m->service_details)) {
            $details = implode('; ', array_map(function ($d) {
                $name = '';
                $qty = 0;
                if (is_array($d)) {
                    $name = trim((string) ($d['name'] ?? ''));
                    $qty = (int) ($d['qty'] ?? 0);
                } elseif (is_string($d)) {
                    $name = trim($d);
                    $qty = 1;
                }
                if ($name === '') {
                    return null;
                }
                return $qty > 0 ? "$name ($qty)" : $name;
            }, array_filter($m->service_details, function ($d) {
                if (is_array($d)) {
                    return isset($d['name']) && trim((string) $d['name']) !== '';
                }
                return is_string($d) && trim($d) !== '';
            })));
            if ($details === '') {
                $details = '-';
            }
        }

        return [
            $m->code ?? '-',
            $m->asset->code ?? '-',
            $m->asset->name ?? '-',
            $m->title ?? '-',
            $m->type?->label() ?? (string)($m->type ?? '-'),
            $m->status?->label() ?? (string)($m->status ?? '-'),
            $m->priority?->label() ?? (string)($m->priority ?? '-'),
            $m->started_at ? $m->started_at->format('d/m/Y') : '-',
            $m->estimated_completed_at ? $m->estimated_completed_at->format('d/m/Y') : '-',
            $m->completed_at ? $m->completed_at->format('d/m/Y') : '-',
            $m->cost ? number_format($m->cost, 0, ',', '.') : '-',
            $m->technician_name ?? '-',
            $m->vendor_name ?? '-',
            $m->odometer_km_at_service ?? '-',
            $m->notes ?? '-',
            $tasks,
            $details,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Header style (row 1)
        $sheet->getStyle('A1:Q1')->applyFromArray([
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

        // Wrap text for all cells
        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();
        $sheet->getStyle("A1:{$highestColumn}{$highestRow}")->getAlignment()->setWrapText(true);

        return [];
    }
}