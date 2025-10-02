<?php

namespace App\Services;

use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class ImageUploadService
{
    protected ImageManager $imageManager;

    protected string $defaultFolder = 'images';

    protected string $tempFolder = 'temp';

    protected int $defaultQuality = 80;

    protected int $maxWidth = 1366;

    protected int $maxHeight = 768;

    public function __construct()
    {
        $this->imageManager = new ImageManager(new Driver);
    }

    /**
     * Upload image to temporary folder
     */
    public function uploadTemporary(UploadedFile $file, array $options = []): ?string
    {
        try {
            // Validate file
            if (! $this->isValidImage($file)) {
                throw new Exception('File yang diupload bukan gambar yang valid');
            }

            // Set options
            $quality = $options['quality'] ?? $this->defaultQuality;
            $maxWidth = $options['max_width'] ?? $this->maxWidth;
            $maxHeight = $options['max_height'] ?? $this->maxHeight;
            $convertToWebp = $options['convert_to_webp'] ?? true;

            // Generate unique filename
            $filename = $this->generateFilename($file, $convertToWebp);
            $path = $this->tempFolder.'/'.$filename;

            // Process image
            $image = $this->imageManager->read($file->getPathname());

            // Resize if needed
            if ($image->width() > $maxWidth || $image->height() > $maxHeight) {
                $image->scale(width: $maxWidth, height: $maxHeight);
            }

            // Convert to WebP and compress
            if ($convertToWebp) {
                $processedImage = $image->toWebp($quality);
            } else {
                $processedImage = $image->toJpeg($quality);
            }

            // Save to temporary storage
            Storage::disk('public')->put($path, $processedImage);

            return $path;

        } catch (Exception $e) {
            throw new Exception('Gagal mengupload gambar temporary: '.$e->getMessage());
        }
    }

    /**
     * Move file from temporary to permanent storage
     */
    public function moveFromTemporary(string $tempPath, ?string $folder = null): ?string
    {
        try {
            // Check if temporary file exists
            if (! Storage::disk('public')->exists($tempPath)) {
                throw new Exception('File temporary tidak ditemukan');
            }

            // Set destination folder
            $folder = $folder ?? $this->defaultFolder;

            // Get filename from temp path
            $filename = basename($tempPath);
            $permanentPath = $folder.'/'.$filename;

            // Move file from temp to permanent location
            if (Storage::disk('public')->move($tempPath, $permanentPath)) {
                return $permanentPath;
            }

            throw new Exception('Gagal memindahkan file dari temporary');
        } catch (Exception $e) {
            throw new Exception('Gagal memindahkan file: '.$e->getMessage());
        }
    }

    /**
     * Clean up temporary files older than specified hours
     */
    public function cleanupTemporaryFiles(int $hoursOld = 24): int
    {
        try {
            $tempFiles = Storage::disk('public')->files($this->tempFolder);
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
     * Upload and process image file
     */
    public function upload(UploadedFile $file, ?string $folder = null, array $options = []): ?string
    {
        try {
            // Validate file
            if (! $this->isValidImage($file)) {
                throw new Exception('File yang diupload bukan gambar yang valid');
            }

            // Set folder
            $folder = $folder ?? $this->defaultFolder;

            // Set options
            $quality = $options['quality'] ?? $this->defaultQuality;
            $maxWidth = $options['max_width'] ?? $this->maxWidth;
            $maxHeight = $options['max_height'] ?? $this->maxHeight;
            $convertToWebp = $options['convert_to_webp'] ?? true;

            // Generate unique filename
            $filename = $this->generateFilename($file, $convertToWebp);
            $path = $folder.'/'.$filename;

            // Process image
            $image = $this->imageManager->read($file->getPathname());

            // Resize if needed
            if ($image->width() > $maxWidth || $image->height() > $maxHeight) {
                $image->scale(width: $maxWidth, height: $maxHeight);
            }

            // Convert to WebP and compress
            if ($convertToWebp) {
                $processedImage = $image->toWebp($quality);
            } else {
                $processedImage = $image->toJpeg($quality);
            }

            // Save to storage
            Storage::disk('public')->put($path, $processedImage);

            return $path;

        } catch (Exception $e) {
            throw new Exception('Gagal mengupload gambar: '.$e->getMessage());
        }
    }

    /**
     * Upload multiple images
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
                    // Log error but continue with other files
                    continue;
                }
            }
        }

        return $uploadedPaths;
    }

    /**
     * Delete uploaded image
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
     * Get full URL of uploaded image
     */
    public function getUrl(string $path): string
    {
        return asset('storage/'.$path);
    }

    /**
     * Validate if file is a valid image
     */
    protected function isValidImage(UploadedFile $file): bool
    {
        $allowedMimes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

        return in_array($file->getMimeType(), $allowedMimes) &&
               in_array(strtolower($file->getClientOriginalExtension()), $allowedExtensions);
    }

    /**
     * Generate unique filename
     */
    protected function generateFilename(UploadedFile $file, bool $convertToWebp = true): string
    {
        $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $sanitizedName = preg_replace('/[^a-zA-Z0-9_-]/', '', $originalName);
        $timestamp = time();
        $random = substr(md5(uniqid()), 0, 8);

        $extension = $convertToWebp ? 'webp' : 'jpg';

        return $sanitizedName.'_'.$timestamp.'_'.$random.'.'.$extension;
    }

    /**
     * Set default configuration
     */
    public function setDefaults(array $config): self
    {
        if (isset($config['folder'])) {
            $this->defaultFolder = $config['folder'];
        }

        if (isset($config['quality'])) {
            $this->defaultQuality = $config['quality'];
        }

        if (isset($config['max_width'])) {
            $this->maxWidth = $config['max_width'];
        }

        if (isset($config['max_height'])) {
            $this->maxHeight = $config['max_height'];
        }

        return $this;
    }
}
