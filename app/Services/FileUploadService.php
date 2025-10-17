<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileUploadService
{
    protected string $defaultFolder = 'files';

    protected string $tempFolder = 'temp/uploads';

    /**
     * Upload file ke storage sementara dengan nama unik.
     * Mengembalikan metadata berguna untuk proses selanjutnya.
     *
     * @return array{path:string, full_path:string, original_name:string, mime:string|null, size:int|null, extension:string, disk:string}
     */
    public function uploadTemporary(UploadedFile $file, ?string $directory = null, ?string $disk = 'local'): array
    {
        $disk = $disk ?? 'local';
        $directory = $directory ?? $this->tempFolder;

        $ext = strtolower($file->getClientOriginalExtension());
        $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $sanitizedBase = Str::slug($originalName) ?: 'file';
        $unique = (string) Str::uuid();
        $filename = $sanitizedBase.'-'.$unique.'.'.$ext;

        // Simpan dengan nama unik
        $path = $file->storeAs($directory, $filename, $disk);

        // Full path untuk akses langsung di server
        $fullPath = $disk === 'local'
            ? storage_path('app/'.$path)
            : Storage::disk($disk)->path($path);

        return [
            'path' => $path,
            'full_path' => $fullPath,
            'original_name' => $file->getClientOriginalName(),
            'mime' => $file->getMimeType(),
            'size' => $file->getSize(),
            'extension' => $ext,
            'disk' => $disk,
        ];
    }

    /**
     * Pindahkan file dari folder temporary ke folder permanen.
     */
    public function moveFromTemporary(string $tempPath, ?string $folder = null, ?string $disk = 'local'): ?string
    {
        $disk = $disk ?? 'local';
        if (! Storage::disk($disk)->exists($tempPath)) {
            return null;
        }

        $folder = $folder ?? $this->defaultFolder;
        $filename = basename($tempPath);
        $permanentPath = trim($folder, '/').'/'.$filename;

        if (Storage::disk($disk)->move($tempPath, $permanentPath)) {
            return $permanentPath;
        }

        return null;
    }

    /**
     * Hapus file pada disk yang ditentukan.
     */
    public function delete(string $path, ?string $disk = 'local'): bool
    {
        $disk = $disk ?? 'local';
        return Storage::disk($disk)->delete($path);
    }

    /**
     * Ambil URL file (hanya untuk disk public).
     */
    public function getUrl(string $path, ?string $disk = 'public'): ?string
    {
        $disk = $disk ?? 'public';
        if ($disk !== 'public') {
            return null;
        }

        return asset('storage/'.$path);
    }
}