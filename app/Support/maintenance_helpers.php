<?php

use App\Models\AssetMaintenance;
use Illuminate\Support\Str;

if (! function_exists('generate_maintenance_code')) {
    /**
     * Generate unique maintenance code
     * Format: MNT-[YEAR][MONTH]-[SEQUENTIAL_NUMBER]
     * Example: MNT-202412-001, MNT-202412-025
     *
     * @return string
     */
    function generate_maintenance_code()
    {
        // Get current year and month
        $yearMonth = date('Ym');
        
        // Generate prefix
        $prefix = 'MNT-' . $yearMonth . '-';
        
        // Get the last maintenance code for this month
        $lastMaintenance = AssetMaintenance::where('id', 'LIKE', $prefix . '%')
            ->orderBy('id', 'desc')
            ->first();
        
        $nextNumber = 1;
        if ($lastMaintenance) {
            // Extract the sequential number from the last maintenance code
            $lastNumber = (int) substr($lastMaintenance->id, -3);
            $nextNumber = $lastNumber + 1;
        }
        
        // Format the sequential number with leading zeros (3 digits)
        $sequentialNumber = str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
        
        return $prefix . $sequentialNumber;
    }
}

if (! function_exists('validate_maintenance_code')) {
    /**
     * Validate maintenance code format
     *
     * @param  string  $code
     * @return bool
     */
    function validate_maintenance_code($code)
    {
        // Pattern: MNT-YYYYMM-XXX (MNT- + 6 digits for year/month + - + 3 digits)
        return preg_match('/^MNT-\d{6}-\d{3}$/', $code);
    }
}

if (! function_exists('parse_maintenance_code')) {
    /**
     * Parse maintenance code to extract components
     *
     * @param  string  $code
     * @return array|null
     */
    function parse_maintenance_code($code)
    {
        if (! validate_maintenance_code($code)) {
            return null;
        }

        $parts = explode('-', $code);
        $yearMonth = $parts[1];
        
        return [
            'prefix' => 'MNT',
            'year' => substr($yearMonth, 0, 4),
            'month' => substr($yearMonth, 4, 2),
            'sequential_number' => (int) $parts[2],
            'full_code' => $code,
        ];
    }
}

if (! function_exists('generate_work_order_number')) {
    /**
     * Generate work order number for maintenance
     * Format: WO-[YEAR][MONTH][DAY]-[SEQUENTIAL_NUMBER]
     * Example: WO-20241225-001, WO-20241225-025
     *
     * @return string
     */
    function generate_work_order_number()
    {
        // Get current date
        $date = date('Ymd');
        
        // Generate prefix
        $prefix = 'WO-' . $date . '-';
        
        // Get the last work order for today
        $lastMaintenance = AssetMaintenance::whereDate('created_at', today())
            ->orderBy('created_at', 'desc')
            ->first();
        
        $nextNumber = 1;
        if ($lastMaintenance) {
            // Count maintenances created today
            $count = AssetMaintenance::whereDate('created_at', today())->count();
            $nextNumber = $count + 1;
        }
        
        // Format the sequential number with leading zeros (3 digits)
        $sequentialNumber = str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
        
        return $prefix . $sequentialNumber;
    }
}

if (! function_exists('validate_work_order_number')) {
    /**
     * Validate work order number format
     *
     * @param  string  $workOrder
     * @return bool
     */
    function validate_work_order_number($workOrder)
    {
        // Pattern: WO-YYYYMMDD-XXX (WO- + 8 digits for date + - + 3 digits)
        return preg_match('/^WO-\d{8}-\d{3}$/', $workOrder);
    }
}

if (! function_exists('parse_work_order_number')) {
    /**
     * Parse work order number to extract components
     *
     * @param  string  $workOrder
     * @return array|null
     */
    function parse_work_order_number($workOrder)
    {
        if (! validate_work_order_number($workOrder)) {
            return null;
        }

        $parts = explode('-', $workOrder);
        $date = $parts[1];
        
        return [
            'prefix' => 'WO',
            'year' => substr($date, 0, 4),
            'month' => substr($date, 4, 2),
            'day' => substr($date, 6, 2),
            'sequential_number' => (int) $parts[2],
            'full_work_order' => $workOrder,
        ];
    }
}

if (! function_exists('generate_maintenance_reference')) {
    /**
     * Generate maintenance reference using ULID approach
     * Format: 8 character string (first 4 + last 4 characters of ULID)
     * Example: 01AR3NDK, 01BX5ZZK
     *
     * @return string
     */
    function generate_maintenance_reference()
    {
        $attempts = 0;
        $maxAttempts = 100;

        do {
            // Generate ULID (26 characters)
            $ulid = (string) Str::ulid();
            // Take first 4 and last 4 characters (8 characters total)
            $reference = substr($ulid, 0, 4) . substr($ulid, -4);

            // Check if reference already exists
            $exists = AssetMaintenance::where('id', $reference)->exists();
            $attempts++;

        } while ($exists && $attempts < $maxAttempts);

        if ($attempts >= $maxAttempts) {
            throw new \RuntimeException('Unable to generate unique maintenance reference after ' . $maxAttempts . ' attempts');
        }

        return strtoupper($reference);
    }
}

if (! function_exists('validate_maintenance_reference')) {
    /**
     * Validate maintenance reference format (ULID-based)
     *
     * @param  string  $reference
     * @return bool
     */
    function validate_maintenance_reference($reference)
    {
        // Pattern: 8 alphanumeric characters (ULID format)
        return preg_match('/^[A-Z0-9]{8}$/', strtoupper($reference));
    }
}

if (! function_exists('parse_maintenance_reference')) {
    /**
     * Parse maintenance reference to extract components (ULID-based)
     *
     * @param  string  $reference
     * @return array|null
     */
    function parse_maintenance_reference($reference)
    {
        if (! validate_maintenance_reference($reference)) {
            return null;
        }

        return [
            'first_part' => substr($reference, 0, 4), // First 4 characters
            'last_part' => substr($reference, -4),   // Last 4 characters
            'full_reference' => strtoupper($reference),
        ];
    }
}