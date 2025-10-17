<?php

namespace App\Livewire\Assets;

use App\Exports\AssetsBatchTemplateExport;
use App\Imports\AssetsBatchImport;
use App\Services\FileUploadService;
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
            // Ambil hanya sheet pertama
            $sheets = Excel::toCollection(new AssetsBatchImport, $fullPath);
            $firstSheet = $sheets->first() ?? collect();

            // Jalankan import validasi hanya untuk sheet pertama
            $import = new AssetsBatchImport;
            $import->collection($firstSheet);

            // Ambil ringkasan dari import
            $summary = $import->getSummary();
            $this->summaryTotal = $summary['total'];
            $this->summaryValid = $summary['valid'];
            $this->summaryInvalid = $summary['invalid'];
            $this->rowErrors = $summary['errors'];

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
