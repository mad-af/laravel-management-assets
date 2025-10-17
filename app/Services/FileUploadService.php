<?php

namespace App\Services;

use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileUploadService
{
    /**
     * Default base folder for permanent files (relative to public disk)
     */
    protected string $defaultFolder = 'files';

    /**
     * Temporary folder for staged uploads (relative to public disk)
     */
    protected string $tempFolder = 'temp';

    /**
     * Maximum file size in bytes (default: 20 MB)
     */
    protected int $maxSize = 20 * 1024 * 1024;

    /**
     * Allowed MIME types (supports wildcards like `image/*`)
     */
    protected array $allowedMimes = [
        'application/pdf',
        'text/plain',
        'application/zip',
        'application/x-7z-compressed',
        'application/x-rar-compressed',
        'application/msword',
        'application/vnd.ms-excel',
        'application/vnd.ms-powerpoint',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document', // docx
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',      // xlsx
        'application/vnd.openxmlformats-officedocument.presentationml.presentation', // pptx
        'text/csv',
        'image/*', // allow images as generic files, no processing
        'video/*',
        'audio/*',
    ];

    /**
     * Allowed file extensions (lowercase, without dot)
     */
    protected array $allowedExtensions = [
        'pdf','txt','zip','7z','rar','doc','docx','xls','xlsx','ppt','pptx','csv',
        'jpg','jpeg','png','gif','webp','svg','mp4','mov','avi','mp3','wav'
    ];

    /**
     * Whether to name files by SHA-256 hash to avoid duplicates
     */
    protected bool $avoidDuplicateByHash = false;

    /**
     * Upload a file to temporary storage (no content processing)
     */
    public function uploadTemporary(UploadedFile $file, array $options = []): ?string
    {
        try {
            $this->applyOptions($options);

            if (! $this->isValidFile($file)) {
                throw new Exception('File yang diupload tidak diizinkan atau melebihi ukuran maksimum');
            }

            $filename = $this->makeFilename($file, $options);
            $path = trim($this->tempFolder, '/').'/'.$filename;

            Storage::disk('public')->putFileAs(dirname($path), $file, basename($path));

            return $path;
        } catch (Exception $e) {
            throw new Exception('Gagal mengupload file sementara: '.$e->getMessage());
        }
    }

    /**
     * Pindah file dari temporary ke folder permanen
     */
    public function moveFromTemporary(string $tempPath, ?string $folder = null): ?string
    {
        try {
            if (! Storage::disk('public')->exists($tempPath)) {
                throw new Exception('File temporary tidak ditemukan');
            }

            $folder = $folder ? trim($folder, '/') : $this->defaultFolder;

            $filename = basename($tempPath);
            $permanentPath = $folder.'/'.$filename;

            // ensure destination directory exists
            Storage::disk('public')->makeDirectory($folder);

            if (Storage::disk('public')->move($tempPath, $permanentPath)) {
                return $permanentPath;
            }

            throw new Exception('Gagal memindahkan file dari temporary');
        } catch (Exception $e) {
            throw new Exception('Gagal memindahkan file: '.$e->getMessage());
        }
    }

    /**
     * Bersihkan file temporary yang lebih lama dari X jam
     * @return int jumlah file yang terhapus
     */
    public function cleanupTemporaryFiles(int $hoursOld = 24): int
    {
        try {
            $tempFolder = trim($this->tempFolder, '/');
            $tempFiles = Storage::disk('public')->files($tempFolder);
            $deletedCount = 0;
            $cutoffTime = now()->subHours($hoursOld)->timestamp;

            foreach ($tempFiles as $file) {
                $fileTime = Storage::disk('public')->lastModified($file);
                if ($fileTime < $cutoffTime) {
                    if (Storage::disk('public')->delete($file)) {
                        $deletedCount++;
                    }
                }
            }

            return $deletedCount;
        } catch (Exception $e) {
            return 0;
        }
    }

    /**
     * Upload file langsung ke folder permanen
     */
    public function upload(UploadedFile $file, ?string $folder = null, array $options = []): ?string
    {
        try {
            $this->applyOptions($options);

            if (! $this->isValidFile($file)) {
                throw new Exception('File yang diupload tidak diizinkan atau melebihi ukuran maksimum');
            }

            $folder = $folder ? trim($folder, '/') : $this->defaultFolder;

            $filename = $this->makeFilename($file, $options);
            $path = $folder.'/'.$filename;

            Storage::disk('public')->makeDirectory($folder);

            // If dedup by hash is on and a file with same name already exists, just return existing path
            if ($this->avoidDuplicateByHash && Storage::disk('public')->exists($path)) {
                return $path;
            }

            Storage::disk('public')->putFileAs($folder, $file, $filename);

            return $path;
        } catch (Exception $e) {
            throw new Exception('Gagal mengupload file: '.$e->getMessage());
        }
    }

    /**
     * Upload banyak file sekaligus
     */
    public function uploadMultiple(array $files, ?string $folder = null, array $options = []): array
    {
        $uploadedPaths = [];

        foreach ($files as $file) {
            if ($file instanceof UploadedFile) {
                try {
                    $path = $this->upload($file, $folder, $options);
                    if ($path) {
                        $uploadedPaths[] = $path;
                    }
                } catch (Exception $e) {
                    // bisa di-log, tapi lanjut ke file berikutnya
                    continue;
                }
            }
        }

        return $uploadedPaths;
    }

    /**
     * Hapus file yang sudah diupload
     */
    public function delete(string $path): bool
    {
        try {
            if (Storage::disk('public')->exists($path)) {
                return Storage::disk('public')->delete($path);
            }

            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Dapatkan URL publik dari file
     */
    public function getUrl(string $path): string
    {
        return asset('storage/'.ltrim($path, '/'));
    }

    /**
     * Validasi file berdasarkan mime, ekstensi, dan ukuran
     */
    protected function isValidFile(UploadedFile $file): bool
    {
        // Size check
        if ($file->getSize() > $this->maxSize) {
            return false;
        }

        // Extension check
        $ext = strtolower($file->getClientOriginalExtension());
        if (! in_array($ext, $this->allowedExtensions, true)) {
            return false;
        }

        // MIME check (prefer real mime from content if possible)
        $realMime = $this->detectMimeType($file);
        return $this->mimeAllowed($realMime, $this->allowedMimes);
    }

    /**
     * Buat nama file yang aman dan unik
     */
    protected function makeFilename(UploadedFile $file, array $options = []): string
    {
        $ext = strtolower($file->getClientOriginalExtension());

        if (($options['avoid_duplicate_by_hash'] ?? $this->avoidDuplicateByHash) === true) {
            $hash = hash_file('sha256', $file->getPathname());
            return $hash.'.'.$ext;
        }

        $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $sanitizedName = preg_replace('/[^a-zA-Z0-9_-]/', '', Str::slug($originalName, '_')) ?: 'file';
        $timestamp = time();
        $random = substr(md5(uniqid('', true)), 0, 8);

        return $sanitizedName.'_'.$timestamp.'_'.$random.'.'.$ext;
    }

    /**
     * Terapkan opsi runtime ke properti service
     */
    protected function applyOptions(array $options): void
    {
        if (isset($options['folder'])) {
            $this->defaultFolder = trim((string) $options['folder'], '/');
        }

        if (isset($options['temp_folder'])) {
            $this->tempFolder = trim((string) $options['temp_folder'], '/');
        }

        if (isset($options['max_size'])) {
            // allow passing in MB (int) or bytes
            $this->maxSize = (int) $options['max_size'];
        }

        if (isset($options['allowed_mimes']) && is_array($options['allowed_mimes'])) {
            $this->allowedMimes = array_values($options['allowed_mimes']);
        }

        if (isset($options['allowed_extensions']) && is_array($options['allowed_extensions'])) {
            $this->allowedExtensions = array_map(fn ($e) => strtolower($e), $options['allowed_extensions']);
        }

        if (isset($options['avoid_duplicate_by_hash'])) {
            $this->avoidDuplicateByHash = (bool) $options['avoid_duplicate_by_hash'];
        }
    }

    /**
     * Deteksi MIME dari konten file (lebih andal dari client MIME)
     */
    protected function detectMimeType(UploadedFile $file): string
    {
        $path = $file->getPathname();
        if (function_exists('finfo_open')) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            if ($finfo) {
                $mime = finfo_file($finfo, $path) ?: $file->getMimeType();
                finfo_close($finfo);
                return $mime;
            }
        }
        return $file->getMimeType();
    }

    /**
     * Cek apakah MIME diizinkan, dukung wildcard (mis. image/*)
     */
    protected function mimeAllowed(string $mime, array $allowed): bool
    {
        foreach ($allowed as $pattern) {
            if ($pattern === $mime) {
                return true;
            }

            if (str_contains($pattern, '/*')) {
                [$type] = explode('/', $pattern, 2);
                if (str_starts_with($mime, $type.'/')) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Set default configuration via array
     */
    public function setDefaults(array $config): self
    {
        if (isset($config['folder'])) {
            $this->defaultFolder = trim((string) $config['folder'], '/');
        }

        if (isset($config['temp_folder'])) {
            $this->tempFolder = trim((string) $config['temp_folder'], '/');
        }

        if (isset($config['max_size'])) {
            $this->maxSize = (int) $config['max_size'];
        }

        if (isset($config['allowed_mimes']) && is_array($config['allowed_mimes'])) {
            $this->allowedMimes = array_values($config['allowed_mimes']);
        }

        if (isset($config['allowed_extensions']) && is_array($config['allowed_extensions'])) {
            $this->allowedExtensions = array_map(fn ($e) => strtolower($e), $config['allowed_extensions']);
        }

        if (isset($config['avoid_duplicate_by_hash'])) {
            $this->avoidDuplicateByHash = (bool) $config['avoid_duplicate_by_hash'];
        }

        return $this;
    }
}
