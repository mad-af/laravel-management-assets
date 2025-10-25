<?php

namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

class ClaimDocumentsCast implements CastsAttributes
{
    /**
     * Cast the given value to a normalized array of {doc_type, file_path, description}.
     *
     * @param  mixed  $value
     * @return array<int, array{doc_type:string, file_path:string, description:string}>
     */
    public function get($model, string $key, $value, array $attributes)
    {
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            $data = is_array($decoded) ? $decoded : [];
        } elseif (is_array($value)) {
            $data = $value;
        } elseif ($value === null) {
            $data = [];
        } else {
            $data = [];
        }

        $result = [];
        foreach ($data as $item) {
            if (is_array($item)) {
                $docType = (string) ($item['doc_type'] ?? ($item['type'] ?? ''));
                $filePath = (string) ($item['file_path'] ?? ($item['path'] ?? ''));
                $description = (string) ($item['description'] ?? ($item['desc'] ?? ''));
            } else {
                // Fallback: if item is a string, treat it as description
                $docType = '';
                $filePath = '';
                $description = is_string($item) ? $item : json_encode($item, JSON_UNESCAPED_UNICODE);
            }

            $docType = trim($docType);
            $filePath = trim($filePath);
            $description = trim($description);

            // Minimal validation: include if at least one field is non-empty
            if ($docType !== '' || $filePath !== '' || $description !== '') {
                $result[] = [
                    'doc_type' => $docType,
                    'file_path' => $filePath,
                    'description' => $description,
                ];
            }
        }

        return $result;
    }

    /**
     * Prepare the given value for storage as JSON string.
     *
     * @param  mixed  $value
     * @return string
     */
    public function set($model, string $key, $value, array $attributes)
    {
        $normalized = [];

        if (is_array($value)) {
            $isAssoc = array_keys($value) !== range(0, count($value) - 1);
            if ($isAssoc && (array_key_exists('doc_type', $value) || array_key_exists('file_path', $value) || array_key_exists('description', $value))) {
                $value = [$value];
                $isAssoc = false;
            }

            if (! $isAssoc) {
                foreach ($value as $item) {
                    if (is_array($item)) {
                        $docType = trim((string) ($item['doc_type'] ?? ($item['type'] ?? '')));
                        $filePath = trim((string) ($item['file_path'] ?? ($item['path'] ?? '')));
                        $description = trim((string) ($item['description'] ?? ($item['desc'] ?? '')));
                    } else {
                        // If item is a string, treat it as description
                        $docType = '';
                        $filePath = '';
                        $description = trim(is_string($item) ? $item : json_encode($item, JSON_UNESCAPED_UNICODE));
                    }

                    if ($docType !== '' || $filePath !== '' || $description !== '') {
                        $normalized[] = [
                            'doc_type' => $docType,
                            'file_path' => $filePath,
                            'description' => $description,
                        ];
                    }
                }
            } else {
                // associative array: try to normalize sensibly
                $docType = trim((string) ($value['doc_type'] ?? ($value['type'] ?? '')));
                $filePath = trim((string) ($value['file_path'] ?? ($value['path'] ?? '')));
                $description = trim((string) ($value['description'] ?? ($value['desc'] ?? '')));
                if ($docType !== '' || $filePath !== '' || $description !== '') {
                    $normalized[] = [
                        'doc_type' => $docType,
                        'file_path' => $filePath,
                        'description' => $description,
                    ];
                }
            }
        } elseif (is_string($value)) {
            $t = trim($value);
            if ($t !== '') {
                $normalized[] = [
                    'doc_type' => '',
                    'file_path' => '',
                    'description' => $t,
                ];
            }
        } elseif ($value === null) {
            $normalized = [];
        } else {
            $normalized[] = [
                'doc_type' => '',
                'file_path' => '',
                'description' => json_encode($value, JSON_UNESCAPED_UNICODE),
            ];
        }

        return json_encode($normalized, JSON_UNESCAPED_UNICODE);
    }
}