<?php

namespace App\Exports\Sheets;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class MaintenancesMonthlySheet implements FromCollection, ShouldAutoSize, WithEvents, WithHeadings, WithMapping, WithStyles, WithTitle
{
    protected string $title;

    protected Collection $items;

    /**
     * Service schedule check per maintenance id (see MaintenancesMonthlyExport::buildServiceChecks).
     *
     * @var array<string, array>
     */
    protected array $serviceChecks;

    // Column letters (keep in sync with headings())
    private const COL_TARGET_DATE = 'H';

    private const COL_ACTUAL_DATE = 'I';

    private const COL_TARGET_KM = 'J';

    private const COL_ACTUAL_KM = 'K';

    private const COL_SERVICE_STATUS = 'L';

    private const COL_COST = 'P';

    private const COL_LAST = 'U';

    public function __construct(string $title, Collection $items, array $serviceChecks = [])
    {
        $this->title = $title;
        $this->items = $items->values();
        $this->serviceChecks = $serviceChecks;
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
            'Tanggal Service Seharusnya',
            'Tanggal Service Aktual',
            'Target KM Service',
            'KM Aktual Saat Service',
            'Status Service',
            'Mulai',
            'Estimasi Selesai',
            'Selesai',
            'Biaya (Rp)',
            'Teknisi',
            'Vendor',
            'Catatan',
            'Service Tasks',
            'Service Details',
        ];
    }

    public function map($m): array
    {
        $check = $this->serviceChecks[$m->id] ?? [
            'target_date' => null,
            'actual_date' => null,
            'target_km' => null,
            'actual_km' => null,
            'late_days' => null,
            'over_km' => null,
            'evaluated' => false,
        ];

        // Normalize service tasks to a readable string
        $tasks = '-';
        if (is_array($m->service_tasks) && ! empty($m->service_tasks)) {
            $tasks = implode('; ', array_map(function ($t) {
                $text = is_array($t) ? ($t['task'] ?? '') : (string) $t;
                $text = trim((string) $text);
                if ($text === '') {
                    return null;
                }
                $prefix = (is_array($t) && ! empty($t['completed'])) ? '✓ ' : '';

                return $prefix.$text;
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
        if (is_array($m->service_details) && ! empty($m->service_details)) {
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
            $m->type?->label() ?? (string) ($m->type ?? '-'),
            $m->status?->label() ?? (string) ($m->status ?? '-'),
            $m->priority?->label() ?? (string) ($m->priority ?? '-'),
            $check['target_date'] ? $check['target_date']->format('d/m/Y') : '-',
            $check['actual_date'] ? $check['actual_date']->format('d/m/Y') : '-',
            $check['target_km'] ?: '-',
            $check['actual_km'] ?: '-',
            $this->serviceStatusText($check),
            $m->started_at ? $m->started_at->format('d/m/Y') : '-',
            $m->estimated_completed_at ? $m->estimated_completed_at->format('d/m/Y') : '-',
            $m->completed_at ? $m->completed_at->format('d/m/Y') : '-',
            $m->cost ?? '-',
            $m->technician_name ?? '-',
            $m->vendor_name ?? '-',
            $m->notes ?? '-',
            $tasks,
            $details,
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $highestRow = $sheet->getHighestRow();

                $late = [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'FFC7CE'],
                ];
                $onTime = [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'C6EFCE'],
                ];

                // Highlight service schedule: red when target date/KM was passed
                foreach ($this->items as $index => $m) {
                    $row = $index + 2;
                    $check = $this->serviceChecks[$m->id] ?? null;
                    if (! $check || ! $check['evaluated']) {
                        continue;
                    }

                    if ($check['late_days']) {
                        $sheet->getStyle(self::COL_ACTUAL_DATE.$row)->applyFromArray([
                            'fill' => $late,
                            'font' => ['bold' => true, 'color' => ['rgb' => '9C0006']],
                        ]);
                    }

                    if ($check['over_km']) {
                        $sheet->getStyle(self::COL_ACTUAL_KM.$row)->applyFromArray([
                            'fill' => $late,
                            'font' => ['bold' => true, 'color' => ['rgb' => '9C0006']],
                        ]);
                    }

                    $isLate = $check['late_days'] || $check['over_km'];
                    $sheet->getStyle(self::COL_SERVICE_STATUS.$row)->applyFromArray([
                        'fill' => $isLate ? $late : $onTime,
                        'font' => ['bold' => true, 'color' => ['rgb' => $isLate ? '9C0006' : '006100']],
                    ]);
                }

                for ($row = 2; $row <= $highestRow; $row++) {
                    $cell = $sheet->getCell(self::COL_COST.$row);
                    $value = $cell->getValue();

                    if (is_numeric($value) && $value > 0) {
                        $formatted = number_format((float) $value, 0, ',', '.');
                    } elseif ($value === null || $value === '' || $value == 0) {
                        $formatted = '-';
                    } else {
                        $formatted = (string) $value;
                    }

                    $cell->setValueExplicit($formatted, DataType::TYPE_STRING);
                }
            },
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Header style (row 1)
        $sheet->getStyle('A1:'.self::COL_LAST.'1')->applyFromArray([
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

        // Wrap text for all cells
        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();
        $sheet->getStyle("A1:{$highestColumn}{$highestRow}")->getAlignment()->setWrapText(true);

        // Thousand separator for KM columns (kept numeric so Excel can sort/filter)
        $sheet->getStyle(self::COL_TARGET_KM.'2:'.self::COL_ACTUAL_KM.$highestRow)
            ->getNumberFormat()->setFormatCode('#,##0');

        // Service schedule headers stand out from the rest
        $sheet->getStyle(self::COL_TARGET_DATE.'1:'.self::COL_SERVICE_STATUS.'1')
            ->getFill()->getStartColor()->setRGB('E65100');

        return [];
    }

    private function serviceStatusText(array $check): string
    {
        if (! $check['evaluated']) {
            return '-';
        }

        $issues = [];
        if ($check['late_days']) {
            $issues[] = 'Terlambat '.$check['late_days'].' hari';
        }
        if ($check['over_km']) {
            $issues[] = 'Lewat '.number_format($check['over_km'], 0, ',', '.').' KM';
        }

        return $issues ? implode(', ', $issues) : 'Tepat Waktu';
    }
}
