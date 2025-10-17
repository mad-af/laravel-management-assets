<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class CleanupPublicTempFiles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'storage:cleanup-temp {--days=3 : Delete files older than N days} {--path=temp : Subdirectory under public storage to clean}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete files in public storage temp directory older than specified days (default: 3).';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $disk = 'public';
        $days = (int) $this->option('days');
        $subPath = trim((string) $this->option('path'), '/');
        $basePath = $subPath !== '' ? $subPath : 'temp';

        if (! Storage::disk($disk)->exists($basePath)) {
            $this->info("Path 'public/{$basePath}' does not exist. Nothing to clean.");

            return self::SUCCESS;
        }

        $threshold = Carbon::now()->subDays($days);
        $files = Storage::disk($disk)->allFiles($basePath);

        if (empty($files)) {
            $this->info("No files found under 'public/{$basePath}'.");

            return self::SUCCESS;
        }

        $deleted = 0;
        $skipped = 0;

        foreach ($files as $file) {
            try {
                $basename = basename($file);
                if ($basename === '.gitkeep' || str_starts_with($basename, '.')) {
                    $skipped++;

                    continue;
                }

                $lastModified = Storage::disk($disk)->lastModified($file);
                $fileTime = Carbon::createFromTimestamp($lastModified);

                if ($fileTime->lessThan($threshold)) {
                    Storage::disk($disk)->delete($file);
                    $deleted++;
                    $this->line("Deleted: {$file} (last modified: {$fileTime->toDateTimeString()})");
                } else {
                    $skipped++;
                }
            } catch (\Throwable $e) {
                $this->error("Failed processing {$file}: ".$e->getMessage());
            }
        }

        $this->info("Cleanup complete. Deleted: {$deleted}, Skipped: {$skipped}, Total scanned: ".count($files));

        // Optionally remove empty directories left behind
        $directories = Storage::disk($disk)->allDirectories($basePath);
        foreach ($directories as $dir) {
            $filesInDir = Storage::disk($disk)->files($dir);
            if (empty($filesInDir)) {
                Storage::disk($disk)->deleteDirectory($dir);
                $this->line("Removed empty directory: {$dir}");
            }
        }

        return self::SUCCESS;
    }
}
