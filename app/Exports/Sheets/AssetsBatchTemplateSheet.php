<?php

namespace App\Exports\Sheets;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class AssetsBatchTemplateSheet implements FromCollection, WithHeadings, WithStyles, WithTitle, ShouldAutoSize
{
    public function title(): string
    {
        return 'Template';
    }

    public function collection()
    {
        return new Collection();
    }

    public function headings(): array
    {
        // Urutan sesuai permintaan: kategori id, link gambar, nama asset, status, kondisi,
        // nilai asset (rp), brand/merek, model/tipe, tanggal pembelian, deskripsi
        return [
            'category_id',
            'image',
            'name',
            'status',
            'condition',
            'value',
            'brand',
            'model',
            'purchase_date',
            'description',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Bold header
        $sheet->getStyle('A1:J1')->getFont()->setBold(true);

        // Warnai merah untuk kolom yang required berdasarkan form: category_id, name, status, condition, value
        foreach (['A1', 'C1', 'D1', 'E1', 'F1'] as $cell) {
            $sheet->getStyle($cell)->getFill()->setFillType(Fill::FILL_SOLID);
            $sheet->getStyle($cell)->getFill()->getStartColor()->setARGB('FFFFC7CE'); // light red
        }

        // Tambah hint kecil di atas header (opsional) – tidak diminta, jadi di-skip

        return [
            // Return array style tambahan bila perlu
        ];
    }
}