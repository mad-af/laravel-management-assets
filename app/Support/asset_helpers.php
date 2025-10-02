<?php

use App\Models\Asset;
use App\Models\Category;
use App\Models\Branch;
use Illuminate\Support\Str;

if (!function_exists('generate_asset_code')) {
    /**
     * Generate unique asset code based on category and branch
     * Format: [CATEGORY_CODE]-[BRANCH_CODE]-[SEQUENTIAL_NUMBER]
     * Example: COMP-JKT-001, FURN-BDG-025
     *
     * @param string $categoryId
     * @param string $branchId
     * @return string
     */
    function generate_asset_code($categoryId, $branchId)
    {
        // Get category and branch information
        $category = Category::find($categoryId);
        $branch = Branch::with('company')->find($branchId);
        
        if (!$category || !$branch || !$branch->company) {
            throw new \InvalidArgumentException('Invalid category or branch ID');
        }
        
        // Generate category code (first 4 letters of category name, uppercase)
        $categoryCode = strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $category->name), 0, 4));
        if (strlen($categoryCode) < 4) {
            $categoryCode = str_pad($categoryCode, 4, 'X');
        }
        
        // Generate company code (first 3 letters of company name, uppercase)
        $companyCode = strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $branch->company->name), 0, 3));
        if (strlen($companyCode) < 3) {
            $companyCode = str_pad($companyCode, 3, 'X');
        }
        
        // Get the next sequential number for this category-company combination
        $prefix = $companyCode . '-' . $categoryCode . '-';
        $lastAsset = Asset::where('code', 'LIKE', $prefix . '%')
            ->orderBy('code', 'desc')
            ->first();
        
        $nextNumber = 1;
        if ($lastAsset) {
            $lastNumber = (int) substr($lastAsset->code, -3);
            $nextNumber = $lastNumber + 1;
        }
        
        // Format the sequential number with leading zeros (3 digits)
        $sequentialNumber = str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
        
        return $prefix . $sequentialNumber;
    }
}

if (!function_exists('generate_asset_tag_code')) {
    /**
     * Generate unique asset tag code
     * Format: INV-[YEAR]-[RANDOM_STRING]
     * Example: INV-2025-A1B2C3, INV-2025-X9Y8Z7
     *
     * @param int $length Length of random string (default: 6)
     * @return string
     */
    function generate_asset_tag_code($length = 6)
    {
        $year = date('Y');
        $attempts = 0;
        $maxAttempts = 100;
        
        do {
            // Generate random alphanumeric string
            $randomString = strtoupper(Str::random($length));
            // Remove confusing characters (0, O, I, 1)
            $randomString = str_replace(['0', 'O', 'I', '1'], ['A', 'B', 'C', 'D'], $randomString);
            
            $tagCode = 'INV-' . $year . '-' . $randomString;
            
            // Check if tag code already exists
            $exists = Asset::where('tag_code', $tagCode)->exists();
            $attempts++;
            
        } while ($exists && $attempts < $maxAttempts);
        
        if ($attempts >= $maxAttempts) {
            throw new \RuntimeException('Unable to generate unique tag code after ' . $maxAttempts . ' attempts');
        }
        
        return $tagCode;
    }
}

if (!function_exists('validate_asset_code')) {
    /**
     * Validate asset code format
     *
     * @param string $code
     * @return bool
     */
    function validate_asset_code($code)
    {
        // Pattern: 4 letters - 3 letters - 3 digits
        return preg_match('/^[A-Z]{4}-[A-Z]{3}-\d{3}$/', $code);
    }
}

if (!function_exists('validate_asset_tag_code')) {
    /**
     * Validate asset tag code format
     *
     * @param string $tagCode
     * @return bool
     */
    function validate_asset_tag_code($tagCode)
    {
        // Pattern: INV-YYYY-XXXXXX (where X is alphanumeric)
        return preg_match('/^INV-\d{4}-[A-Z0-9]{6}$/', $tagCode);
    }
}

if (!function_exists('parse_asset_code')) {
    /**
     * Parse asset code to extract components
     *
     * @param string $code
     * @return array|null
     */
    function parse_asset_code($code)
    {
        if (!validate_asset_code($code)) {
            return null;
        }
        
        $parts = explode('-', $code);
        
        return [
            'category_code' => $parts[0],
            'branch_code' => $parts[1],
            'sequential_number' => (int) $parts[2],
            'full_code' => $code
        ];
    }
}

if (!function_exists('parse_asset_tag_code')) {
    /**
     * Parse asset tag code to extract components
     *
     * @param string $tagCode
     * @return array|null
     */
    function parse_asset_tag_code($tagCode)
    {
        if (!validate_asset_tag_code($tagCode)) {
            return null;
        }
        
        $parts = explode('-', $tagCode);
        
        return [
            'prefix' => $parts[0], // TAG
            'year' => (int) $parts[1],
            'random_string' => $parts[2],
            'full_tag' => $tagCode
        ];
    }
}