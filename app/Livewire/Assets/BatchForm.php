<?php

namespace App\Livewire\Assets;

use App\Enums\AssetCondition;
use App\Enums\AssetStatus;
use App\Exports\AssetsBatchTemplateExport;
use App\Imports\AssetsBatchImport;
use App\Models\Category;
use App\Services\FileUploadService;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;
use Mary\Traits\Toast;

class BatchForm extends Component
{
    use Toast, WithFileUploads;

    public $file;

    public int $summaryTotal = 0;

    public int $summaryValid = 0;

    public int $summaryInvalid = 0;

    public array $rowErrors = [];

    protected $rules = [
        'file' => 'required|file|mimes:xlsx,xls,csv',
    ];

    public function downloadTemplate()
    {
        $filename = 'Assets_Batch_Template_'.now()->format('Y-m-d_H-i-s').'.xlsx';

        try {
            return Excel::download(new AssetsBatchTemplateExport, $filename);
        } catch (\Exception $e) {
            $this->error('Gagal mengunduh template: '.$e->getMessage());

            return null;
        }
    }

    public function updatedFile($value = null)
    {
        // Jalankan otomatis saat file dipilih (wire:model.live)
        if (! $this->file) {
            $this->summaryTotal = 0;
            $this->summaryValid = 0;
            $this->summaryInvalid = 0;
            $this->rowErrors = [];

            return;
        }

        try {
            // Gunakan service upload temporary untuk file umum
            $uploader = new FileUploadService;
            $upload = $uploader->uploadTemporary($this->file, 'imports/assets');
            $storedPath = $upload['path'];
            $originalName = $upload['original_name'];
            $fullPath = $upload['full_path'];

            // Baca sheet pertama dengan header di baris 4 (sesuai template)
            $sheets = Excel::toCollection(new AssetsBatchImport, $fullPath);
            $rows = $sheets->first() ?? collect();

            // Muat referensi
            $allowedCategoryIds = Category::query()->pluck('id')->all();
            $allowedStatuses = AssetStatus::values();
            $allowedConditions = AssetCondition::values();

            // Reset ringkasan
            $this->summaryTotal = $rows->count();
            $this->summaryValid = 0;
            $this->summaryInvalid = 0;
            $this->rowErrors = [];

            // Validasi per-baris
            foreach ($rows as $index => $row) {
                // Excel row number: header di baris 4, data mulai 5
                $excelRow = ($index + 5);
                $errors = [];

                $categoryId = trim((string) ($row['category_id'] ?? ''));
                $image = trim((string) ($row['image'] ?? ''));
                $name = trim((string) ($row['name'] ?? ''));
                $status = trim((string) ($row['status'] ?? ''));
                $condition = trim((string) ($row['condition'] ?? ''));
                $value = trim((string) ($row['value'] ?? ''));
                $brand = trim((string) ($row['brand'] ?? ''));
                $model = trim((string) ($row['model'] ?? ''));
                $purchaseDate = trim((string) ($row['purchase_date'] ?? ''));
                $description = trim((string) ($row['description'] ?? ''));

                // Wajib isi
                if ($categoryId === '') {
                    $errors[] = 'Kategori ID wajib diisi.';
                }
                if ($name === '') {
                    $errors[] = 'Nama asset wajib diisi.';
                }
                if ($status === '') {
                    $errors[] = 'Status asset wajib diisi.';
                }
                if ($condition === '') {
                    $errors[] = 'Kondisi asset wajib diisi.';
                }
                if ($value === '') {
                    $errors[] = 'Nilai asset wajib diisi.';
                }

                // Kategori ID valid dan eksis
                if ($categoryId !== '') {
                    if (! ctype_digit($categoryId)) {
                        $errors[] = 'Kategori ID harus berupa angka (integer).';
                    } elseif (! in_array((int) $categoryId, $allowedCategoryIds, true)) {
                        $errors[] = 'Kategori ID tidak ditemukan di referensi.';
                    }
                }

                // Status dan kondisi sesuai referensi (gunakan value persis)
                if ($status !== '' && ! in_array(strtolower($status), $allowedStatuses, true)) {
                    $errors[] = 'Status tidak valid. Gunakan nilai: '.implode(', ', $allowedStatuses).'.';
                }
                if ($condition !== '' && ! in_array(strtolower($condition), $allowedConditions, true)) {
                    $errors[] = 'Kondisi tidak valid. Gunakan nilai: '.implode(', ', $allowedConditions).'.';
                }

                // Nilai asset numerik (boleh desimal)
                if ($value !== '' && ! preg_match('/^\d+(\.\d{1,2})?$/', $value)) {
                    $errors[] = 'Nilai asset harus angka, maksimal dua desimal (contoh: 15000000.00).';
                }

                // Tanggal pembelian format YYYY-MM-DD
                if ($purchaseDate !== '') {
                    if (! preg_match('/^\d{4}-\d{2}-\d{2}$/', $purchaseDate)) {
                        $errors[] = 'Tanggal pembelian harus format YYYY-MM-DD.';
                    } else {
                        try {
                            Carbon::createFromFormat('Y-m-d', $purchaseDate);
                        } catch (\Exception $e) {
                            $errors[] = 'Tanggal pembelian tidak valid.';
                        }
                    }
                }

                // URL gambar bila diisi
                if ($image !== '' && ! filter_var($image, FILTER_VALIDATE_URL)) {
                    $errors[] = 'Link gambar harus berupa URL yang valid.';
                }

                if (count($errors) === 0) {
                    $this->summaryValid++;
                } else {
                    $this->summaryInvalid++;
                    $this->rowErrors[] = [
                        'row' => $excelRow,
                        'errors' => $errors,
                    ];
                }
            }
        } catch (\Exception $e) {
            $this->error('Gagal memproses file: '.$e->getMessage());
        }
    }

    public function uploadBatch()
    {
        $this->validate();

        try {
            $storedPath = $this->file->store('imports/assets');
            $originalName = $this->file->getClientOriginalName();
            $fullPath = storage_path('app/'.$storedPath);

            // Baca sheet pertama dengan header di baris 4 (sesuai template)
            $sheets = Excel::toCollection(new AssetsBatchImport, $fullPath);
            $rows = $sheets->first() ?? collect();

            // Muat referensi
            $allowedCategoryIds = Category::query()->pluck('id')->all();
            $allowedStatuses = AssetStatus::values();
            $allowedConditions = AssetCondition::values();

            // Reset ringkasan
            $this->summaryTotal = $rows->count();
            $this->summaryValid = 0;
            $this->summaryInvalid = 0;
            $this->rowErrors = [];

            // Validasi per-baris
            foreach ($rows as $index => $row) {
                // Excel row number: header di baris 4, data mulai 5
                $excelRow = ($index + 5);
                $errors = [];

                $categoryId = trim((string) ($row['category_id'] ?? ''));
                $image = trim((string) ($row['image'] ?? ''));
                $name = trim((string) ($row['name'] ?? ''));
                $status = trim((string) ($row['status'] ?? ''));
                $condition = trim((string) ($row['condition'] ?? ''));
                $value = trim((string) ($row['value'] ?? ''));
                $brand = trim((string) ($row['brand'] ?? ''));
                $model = trim((string) ($row['model'] ?? ''));
                $purchaseDate = trim((string) ($row['purchase_date'] ?? ''));
                $description = trim((string) ($row['description'] ?? ''));

                // Wajib isi
                if ($categoryId === '') {
                    $errors[] = 'Kategori ID wajib diisi.';
                }
                if ($name === '') {
                    $errors[] = 'Nama asset wajib diisi.';
                }
                if ($status === '') {
                    $errors[] = 'Status asset wajib diisi.';
                }
                if ($condition === '') {
                    $errors[] = 'Kondisi asset wajib diisi.';
                }
                if ($value === '') {
                    $errors[] = 'Nilai asset wajib diisi.';
                }

                // Kategori ID valid dan eksis
                if ($categoryId !== '') {
                    if (! ctype_digit($categoryId)) {
                        $errors[] = 'Kategori ID harus berupa angka (integer).';
                    } elseif (! in_array((int) $categoryId, $allowedCategoryIds, true)) {
                        $errors[] = 'Kategori ID tidak ditemukan di referensi.';
                    }
                }

                // Status dan kondisi sesuai referensi (gunakan value persis)
                if ($status !== '' && ! in_array(strtolower($status), $allowedStatuses, true)) {
                    $errors[] = 'Status tidak valid. Gunakan nilai: '.implode(', ', $allowedStatuses).'.';
                }
                if ($condition !== '' && ! in_array(strtolower($condition), $allowedConditions, true)) {
                    $errors[] = 'Kondisi tidak valid. Gunakan nilai: '.implode(', ', $allowedConditions).'.';
                }

                // Nilai asset numerik (boleh desimal)
                if ($value !== '' && ! preg_match('/^\d+(\.\d{1,2})?$/', $value)) {
                    $errors[] = 'Nilai asset harus angka, maksimal dua desimal (contoh: 15000000.00).';
                }

                // Tanggal pembelian format YYYY-MM-DD
                if ($purchaseDate !== '') {
                    if (! preg_match('/^\d{4}-\d{2}-\d{2}$/', $purchaseDate)) {
                        $errors[] = 'Tanggal pembelian harus format YYYY-MM-DD.';
                    } else {
                        try {
                            Carbon::createFromFormat('Y-m-d', $purchaseDate);
                        } catch (\Exception $e) {
                            $errors[] = 'Tanggal pembelian tidak valid.';
                        }
                    }
                }

                // URL gambar bila diisi
                if ($image !== '' && ! filter_var($image, FILTER_VALIDATE_URL)) {
                    $errors[] = 'Link gambar harus berupa URL yang valid.';
                }

                if (count($errors) === 0) {
                    $this->summaryValid++;
                } else {
                    $this->summaryInvalid++;
                    $this->rowErrors[] = [
                        'row' => $excelRow,
                        'errors' => $errors,
                    ];
                }
            }

            $this->success('File diunggah: '.$originalName.' — total: '.$this->summaryTotal.', valid: '.$this->summaryValid.', tidak valid: '.$this->summaryInvalid);
        } catch (\Exception $e) {
            $this->error('Gagal mengunggah file: '.$e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.assets.batch-form');
    }
}
