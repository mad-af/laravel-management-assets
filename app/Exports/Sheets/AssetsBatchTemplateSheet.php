<?php

namespace App\Exports\Sheets;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AssetsBatchTemplateSheet implements FromCollection, ShouldAutoSize, WithColumnWidths, WithCustomStartCell, WithEvents, WithHeadings, WithStyles, WithTitle
{
    public function title(): string
    {
        return 'Template';
    }

    public function startCell(): string
    {
        // Headings dimulai di baris 4; baris 1-3 dipakai untuk panduan
        return 'A4';
    }

    public function collection()
    {
        return new Collection;
    }

    public function headings(): array
    {
        // Urutan: kategori id, link gambar, nama asset, status, kondisi,
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
        // Bold pada baris header (baris 4)
        $sheet->getStyle('A4:J4')->getFont()->setBold(true);

        // Warna merah untuk kolom wajib: category_id, name, status, condition, value
        foreach (['A4', 'C4', 'D4', 'E4', 'F4'] as $cell) {
            $sheet->getStyle($cell)->getFill()->setFillType(Fill::FILL_SOLID);
            $sheet->getStyle($cell)->getFill()->getStartColor()->setARGB('FFFFC7CE'); // light red
        }

        // Gaya untuk baris 3 (hint per kolom)
        $sheet->getStyle('A3:J3')->getFont()->setItalic(true);
        $sheet->getStyle('A3:J3')->getFont()->setSize(10);
        $sheet->getStyle('A3:J3')->getAlignment()->setWrapText(true);

        return [];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 36, // category_id
            'B' => 36, // image
            'C' => 24, // name
            'D' => 18, // status
            'E' => 18, // condition
            'F' => 18, // value
            'G' => 20, // brand
            'H' => 20, // model
            'I' => 16, // purchase_date
            'J' => 40, // description
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Judul panduan (baris 1) – merge dan wrap
                $sheet->mergeCells('A1:J1');
                $sheet->setCellValue('A1', 'Panduan Pengisian Template Batch Asset');
                $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(12);
                $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

                // Aturan umum (baris 2) – merge dan wrap
                $sheet->mergeCells('A2:J2');
                $sheet->setCellValue('A2',
                    "• Kolom header berwarna merah wajib diisi.\n".
                    "• Format tanggal: YYYY-MM-DD (contoh: 2023-01-15).\n".
                    "• Status & Kondisi: gunakan nilai persis seperti di sheet 'Referensi'.\n".
                    "• Nilai asset: angka tanpa pemisah ribuan, gunakan dua desimal (contoh: 15000000.00).\n".
                    "• Link gambar: isi dengan URL gambar yang dapat diakses publik (opsional).\n".
                    "• Jangan hapus atau ubah sheet 'Referensi'."
                );
                $sheet->getStyle('A2')->getAlignment()->setWrapText(true);
                $sheet->getRowDimension(2)->setRowHeight(90);

                // Hint per kolom (baris 3)
                $sheet->setCellValue('A3', 'Kategori ID (wajib; lihat sheet Referensi)');
                $sheet->setCellValue('B3', 'Link Gambar (opsional; URL)');
                $sheet->setCellValue('C3', 'Nama Asset (wajib)');
                $sheet->setCellValue('D3', 'Status (wajib; gunakan `value` di Referensi)');
                $sheet->setCellValue('E3', 'Kondisi (wajib; gunakan `value` di Referensi)');
                $sheet->setCellValue('F3', 'Nilai Asset Rp (wajib; angka, contoh 15000000.00)');
                $sheet->setCellValue('G3', 'Brand/Merek (opsional)');
                $sheet->setCellValue('H3', 'Model/Tipe (opsional)');
                $sheet->setCellValue('I3', 'Tanggal Pembelian (YYYY-MM-DD)');
                $sheet->setCellValue('J3', 'Deskripsi (opsional)');
            },
        ];
    }
}
