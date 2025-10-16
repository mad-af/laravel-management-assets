<?php

namespace App\Livewire\Assets;

use App\Exports\AssetsBatchTemplateExport;
use Livewire\Component;
use Livewire\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;
use Mary\Traits\Toast;

class BatchForm extends Component
{
    use WithFileUploads, Toast;

    public $file;

    protected $rules = [
        'file' => 'required|file|mimes:xlsx,xls,csv',
    ];

    public function downloadTemplate()
    {
        $filename = 'Assets_Batch_Template_'.now()->format('Y-m-d_H-i-s').'.xlsx';

        try {
            return Excel::download(new AssetsBatchTemplateExport(), $filename);
        } catch (\Exception $e) {
            $this->error('Gagal mengunduh template: '.$e->getMessage());
            return null;
        }
    }

    public function uploadBatch()
    {
        $this->validate();

        try {
            $storedPath = $this->file->store('imports/assets');
            $originalName = $this->file->getClientOriginalName();

            $this->success('File batch diunggah: '.$originalName.' (disimpan: '.$storedPath.')');
            // Implementasi import akan ditambahkan kemudian sesuai ketentuan kolom.
        } catch (\Exception $e) {
            $this->error('Gagal mengunggah file: '.$e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.assets.batch-form');
    }
}