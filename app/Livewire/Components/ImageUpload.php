<?php

namespace App\Livewire\Components;

use App\Services\ImageUploadService;
use Livewire\Component;
use Livewire\WithFileUploads;
use Mary\Traits\Toast;

class ImageUpload extends Component
{
    use WithFileUploads, Toast;

    public $imageFile;
    public $tempImagePath;
    public $currentImage;
    public $label = 'Upload Gambar';
    public $hint = 'Format: JPG, PNG, WebP. Maksimal 2MB. Gambar akan dikompresi otomatis.';
    public $accept = 'image/png, image/jpeg, image/jpg, image/webp';
    public $maxSize = 2048; // KB
    public $quality = 85;
    public $maxWidth = 1200;
    public $maxHeight = 800;
    public $convertToWebp = true;

    protected $listeners = ['resetImageUpload'];

    public function mount($currentImage = null, $label = null, $hint = null)
    {
        $this->currentImage = $currentImage;
        if ($label) $this->label = $label;
        if ($hint) $this->hint = $hint;
    }

    public function updatedImageFile()
    {
        // Reset tempImagePath first
        $this->tempImagePath = null;
        
        $this->validate([
            'imageFile' => "required|image|max:{$this->maxSize}",
        ]);

        try {
            $imageUploadService = new ImageUploadService;

            // Upload to temporary folder
            $tempPath = $imageUploadService->uploadTemporary($this->imageFile, [
                'quality' => $this->quality,
                'max_width' => $this->maxWidth,
                'max_height' => $this->maxHeight,
                'convert_to_webp' => $this->convertToWebp,
            ]);

            if ($tempPath) {
                $this->tempImagePath = $tempPath;
                $this->success('Gambar berhasil diupload');
                
                // Emit event to parent component with temp path
                $this->dispatch('imageUploaded', tempPath: $tempPath);
            } else {
                $this->error('Gagal mengupload gambar');
            }

        } catch (\Exception $e) {
            $this->error('Gagal mengupload gambar: ' . $e->getMessage());
            $this->tempImagePath = null;
        }
    }

    public function removeImage()
    {
        if ($this->tempImagePath) {
            $imageUploadService = new ImageUploadService;
            $imageUploadService->delete($this->tempImagePath);
            $this->tempImagePath = null;
        }

        $this->imageFile = null;
        $this->success('Gambar berhasil dihapus');
        
        // Emit event to parent component
        $this->dispatch('imageRemoved');
    }

    public function resetImageUpload()
    {
        $this->tempImagePath = null;
        $this->imageFile = null;
    }

    public function getTempImageUrl()
    {
        return $this->tempImagePath ? asset('storage/' . $this->tempImagePath) : null;
    }

    public function getCurrentImageUrl()
    {
        return $this->currentImage ? asset('storage/' . $this->currentImage) : null;
    }

    public function render()
    {
        return view('livewire.components.image-upload');
    }
}