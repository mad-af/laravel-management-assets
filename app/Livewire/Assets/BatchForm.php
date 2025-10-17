<?php

namespace App\Livewire\Assets;

use App\Exports\AssetsBatchTemplateExport;
use App\Imports\AssetsBatchImport;
use App\Models\Asset;
use App\Services\FileUploadService;
use App\Support\SessionKey;
use Illuminate\Support\Facades\DB;
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

    /** Baris yang valid & sudah dinormalisasi dari updatedFile() */
    public array $validRows = [];

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
            $path = $uploader->uploadTemporary($this->file);
            $fullPath = storage_path('app/public/'.$path);

            // Pastikan file benar-benar ada sebelum diproses
            if (! file_exists($fullPath)) {
                $this->error('File sementara tidak ditemukan untuk diproses.');

                return;
            }

            $import = new AssetsBatchImport;
            // Ambil hanya sheet pertama
            $sheets = Excel::toCollection($import, $fullPath);
            $firstSheet = $sheets->first() ?? collect();

            // Jalankan import validasi hanya untuk sheet pertama
            $import->collection($firstSheet);

            // Ambil ringkasan dari import
            $summary = $import->getSummary();
            $this->summaryTotal = $summary['total'];
            $this->summaryValid = $summary['valid'];
            $this->summaryInvalid = $summary['invalid'];
            $this->rowErrors = $summary['errors'];

            // Simpan baris valid untuk proses simpan di save()
            $this->validRows = $import->getValidRows();
            $this->success('File diunggah');
        } catch (\Exception $e) {
            $this->error('Gagal memproses file: '.$e->getMessage());
        }
    }

    public function save()
    {
        $this->validate();

        try {
            $storedPath = $this->file->store('imports/assets');
            $originalName = $this->file->getClientOriginalName();
            // Pastikan konteks company & branch tersedia
            $companyId = session_get(SessionKey::CompanyId);
            $branchId = session_get(SessionKey::BranchId);

            if (! $companyId || ! $branchId) {
                $this->error('Context perusahaan/cabang belum dipilih.');

                return;
            }

            if (empty($this->validRows)) {
                $this->error('Tidak ada baris valid untuk disimpan. Periksa file dan pratinjau.');

                return;
            }

            // Simpan hanya baris valid yang sudah dinormalisasi
            DB::transaction(function () use ($companyId, $branchId) {
                foreach ($this->validRows as $row) {
                    $attributes = [
                        'code' => generate_asset_code($row['category_id'], $branchId),
                        'tag_code' => generate_asset_tag_code(),
                        'name' => $row['name'],
                        'category_id' => $row['category_id'],
                        'company_id' => $companyId,
                        'branch_id' => $branchId,
                        'brand' => $row['brand'] ?? null,
                        'model' => $row['model'] ?? null,
                        'image' => $row['image'] ?? null,
                        'value' => $row['value'],
                        'purchase_date' => $row['purchase_date'] ?? null,
                        'description' => $row['description'] ?? null,
                    ];

                    if (! empty($row['status'])) {
                        $attributes['status'] = $row['status'];
                    }
                    if (! empty($row['condition'])) {
                        $attributes['condition'] = $row['condition'];
                    }

                    Asset::create($attributes);
                }
            });

            $this->dispatch('asset-saved');
            $this->success('Disimpan: '.$this->summaryValid.' baris valid, dilewati: '.$this->summaryInvalid.' baris tidak valid.');
        } catch (\Exception $e) {
            $this->error('Gagal menyimpan data: '.$e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.assets.batch-form');
    }
}
