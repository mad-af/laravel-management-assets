<?php

declare(strict_types=1);

namespace App\Imports;

use App\Enums\AssetCondition;
use App\Enums\AssetStatus;
use App\Models\Category;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

/**
 * Import validator & normalizer for Assets batch template.
 * - Clean error handling & messages (ID)
 * - Case-insensitive enum validation
 * - Excel numeric date support -> Y-m-d
 * - Collects valid, normalized rows for later insert
 */
class AssetsBatchImport implements SkipsEmptyRows, ToCollection, WithHeadingRow
{
    /** Summary counters */
    public int $summaryTotal = 0;

    public int $summaryValid = 0;

    public int $summaryInvalid = 0;

    /** Row-level errors: [ ['row' => int, 'errors' => string[]], ... ] */
    public array $rowErrors = [];

    /** Valid, normalized rows ready for persistence */
    protected array $validRows = [];

    /** Cached reference values */
    protected array $allowedCategoryIds = [];

    protected array $allowedStatuses = [];

    protected array $allowedConditions = [];

    /** Heading row index of the template (1-based) */
    public function headingRow(): int
    {
        return 4;
    }

    /**
     * Entry point called by Maatwebsite/Excel with all data rows.
     */
    public function collection(Collection $rows): void
    {
        $this->bootAllowedValues();
        $this->resetSummary();

        // count rows as received (post-heading, pre-filter)
        $this->summaryTotal = $rows->count();
        logger()->info('Total rows received: '.$this->summaryTotal);

        foreach ($rows as $index => $row) {
            // Translate zero-based $index to actual Excel row number
            $excelRow = $index + $this->headingRow() + 1; // first data row = headingRow + 1

            [$errors, $normalized] = $this->validateAndNormalizeRow($row);

            if (empty($errors)) {
                $this->summaryValid++;
                $this->validRows[] = $normalized;
            } else {
                $this->summaryInvalid++;
                $this->rowErrors[] = [
                    'row' => $excelRow,
                    'errors' => $errors,
                ];
            }
        }
    }

    /** Prepare reference sets (lowercased for case-insensitive match). */
    protected function bootAllowedValues(): void
    {
        $this->allowedCategoryIds = Category::query()->pluck('id')->all(); // UUID strings
        $this->allowedStatuses = array_map('strtolower', AssetStatus::values());
        $this->allowedConditions = array_map('strtolower', AssetCondition::values());
    }

    /** Reset counters and buffers. */
    protected function resetSummary(): void
    {
        $this->summaryTotal = 0;
        $this->summaryValid = 0;
        $this->summaryInvalid = 0;
        $this->rowErrors = [];
        $this->validRows = [];
    }

    /**
     * Validate and normalize a single row.
     *
     * @param  array|\ArrayAccess  $row  Row from WithHeadingRow (array-like)
     * @return array{0: array<int,string>, 1: array<string,mixed>} [errors, normalized]
     */
    protected function validateAndNormalizeRow($row): array
    {
        // Raw values (trim strings early)
        $categoryId = trim((string) ($row['category_id'] ?? ''));
        $image = trim((string) ($row['image'] ?? ''));
        $name = trim((string) ($row['name'] ?? ''));
        $statusRaw = trim((string) ($row['status'] ?? ''));
        $conditionRaw = trim((string) ($row['condition'] ?? ''));
        $valueRaw = $row['value'] ?? '';
        $brand = trim((string) ($row['brand'] ?? ''));
        $model = trim((string) ($row['model'] ?? ''));
        $purchaseDateRaw = $row['purchase_date'] ?? '';
        $description = trim((string) ($row['description'] ?? ''));

        $errors = [];

        // Requireds
        if ($categoryId === '') {
            $errors[] = 'Kategori ID wajib diisi.';
        }
        if ($name === '') {
            $errors[] = 'Nama asset wajib diisi.';
        }
        if ($statusRaw === '') {
            $errors[] = 'Status asset wajib diisi.';
        }
        if ($conditionRaw === '') {
            $errors[] = 'Kondisi asset wajib diisi.';
        }
        if ($valueRaw === '' || (is_string($valueRaw) && trim($valueRaw) === '')) {
            $errors[] = 'Nilai asset wajib diisi.';
        }

        // Category UUID & existence
        if ($categoryId !== '') {
            if (! Str::isUuid($categoryId)) {
                $errors[] = 'Kategori ID harus berupa UUID.';
            } elseif (! in_array($categoryId, $this->allowedCategoryIds, true)) {
                $errors[] = 'Kategori ID tidak ditemukan di referensi.';
            }
        }

        // Enum checks (case-insensitive)
        $status = strtolower($statusRaw);
        $condition = strtolower($conditionRaw);

        if ($statusRaw !== '' && ! in_array($status, $this->allowedStatuses, true)) {
            $errors[] = 'Status tidak valid. Gunakan nilai: '.implode(', ', $this->allowedStatuses).'.';
        }
        if ($conditionRaw !== '' && ! in_array($condition, $this->allowedConditions, true)) {
            $errors[] = 'Kondisi tidak valid. Gunakan nilai: '.implode(', ', $this->allowedConditions).'.';
        }

        // Value numeric (accept numeric types or numeric strings; normalize to float)
        $value = null;
        $valueString = is_string($valueRaw) ? str_replace([',', ' '], '', $valueRaw) : (string) $valueRaw;
        if ($valueRaw !== '' && ! is_numeric($valueString)) {
            $errors[] = 'Nilai asset harus angka, maksimal dua desimal (contoh: 15000000.00).';
        } else {
            $value = ($valueRaw === '' ? null : (float) $valueString);
        }

        // Purchase date (supports Excel serial numbers and free text parse)
        $purchaseDate = null;
        if ($purchaseDateRaw !== '' && $purchaseDateRaw !== null) {
            try {
                if (is_numeric($purchaseDateRaw)) {
                    // Excel serial -> DateTime -> Carbon (midnight startOfDay)
                    $purchaseDate = Carbon::instance(ExcelDate::excelToDateTimeObject((float) $purchaseDateRaw))
                        ->startOfDay();
                } else {
                    $purchaseDate = Carbon::parse((string) $purchaseDateRaw);
                }
            } catch (\Throwable $e) {
                $errors[] = 'Tanggal pembelian tidak valid.';
            }
        }

        // Image URL (if provided)
        if ($image !== '' && ! filter_var($image, FILTER_VALIDATE_URL)) {
            $errors[] = 'Link gambar harus berupa URL yang valid.';
        }

        // Normalized payload
        $normalized = [
            'category_id' => $categoryId ?: null,
            'image' => $image ?: null,
            'name' => $name ?: null,
            'status' => $status ?: null,      // lowercased for consistency
            'condition' => $condition ?: null,   // lowercased for consistency
            'value' => $value,               // float|null
            'brand' => $brand ?: null,
            'model' => $model ?: null,
            'purchase_date' => $purchaseDate?->toDateString(), // 'Y-m-d' or null
            'description' => $description ?: null,
        ];

        return [$errors, $normalized];
    }

    /** Get all valid, normalized rows ready for DB insert. */
    public function getValidRows(): array
    {
        return $this->validRows;
    }

    /** Compact summary for controllers/services. */
    public function getSummary(): array
    {
        return [
            'total' => $this->summaryTotal,
            'valid' => $this->summaryValid,
            'invalid' => $this->summaryInvalid,
            'errors' => $this->rowErrors,
        ];
    }
}
