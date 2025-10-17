<?php

namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

class ServiceDetailsCast implements CastsAttributes
{
    /**
     * Cast the given value to a normalized array of {name, qty}.
     *
     * @param  mixed  $value
     * @return array<int, array{name:string, qty:int}>
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
                $name = isset($item['name'])
                    ? (string) $item['name']
                    : (is_string($item['value'] ?? null) ? (string) $item['value'] : json_encode($item, JSON_UNESCAPED_UNICODE));
                $qty = (int) ($item['qty'] ?? 0);
            } else {
                // If it's a plain string, treat it as name with qty=1
                $name = is_string($item) ? $item : json_encode($item, JSON_UNESCAPED_UNICODE);
                $qty = 1;
            }

            $name = trim($name);
            if ($name !== '') {
                $result[] = [
                    'name' => $name,
                    'qty' => max(0, $qty),
                ];
            }
        }

        return $result;
    }

    /**
     * Prepare the given value for storage.
     *
     * @param  mixed  $value
     * @return string JSON string
     */
    public function set($model, string $key, $value, array $attributes)
    {
        $normalized = [];

        if (is_array($value)) {
            $isAssoc = array_keys($value) !== range(0, count($value) - 1);
            if ($isAssoc && array_key_exists('name', $value)) {
                $value = [$value];
                $isAssoc = false;
            }

            if (! $isAssoc) {
                foreach ($value as $item) {
                    if (is_array($item)) {
                        $name = isset($item['name'])
                            ? (string) $item['name']
                            : (is_string($item['value'] ?? null) ? (string) $item['value'] : json_encode($item, JSON_UNESCAPED_UNICODE));
                        $qty = (int) ($item['qty'] ?? 0);
                    } else {
                        $name = is_string($item) ? $item : json_encode($item, JSON_UNESCAPED_UNICODE);
                        $qty = 1;
                    }

                    $name = trim($name);
                    if ($name !== '') {
                        $normalized[] = [
                            'name' => $name,
                            'qty' => max(0, $qty),
                        ];
                    }
                }
            } else {
                if (isset($value['name'])) {
                    $normalized[] = [
                        'name' => trim((string) $value['name']),
                        'qty' => max(0, (int) ($value['qty'] ?? 0)),
                    ];
                } else {
                    foreach ($value as $k => $v) {
                        if (is_string($v)) {
                            $t = trim($v);
                            if ($t !== '') {
                                $normalized[] = [
                                    'name' => $t,
                                    'qty' => 1,
                                ];
                            }
                        }
                    }
                }
            }
        } elseif (is_string($value)) {
            $t = trim($value);
            if ($t !== '') {
                $normalized[] = [
                    'name' => $t,
                    'qty' => 1,
                ];
            }
        } elseif ($value === null) {
            $normalized = [];
        } else {
            $normalized[] = [
                'name' => json_encode($value, JSON_UNESCAPED_UNICODE),
                'qty' => 1,
            ];
        }

        return json_encode($normalized, JSON_UNESCAPED_UNICODE);
    }
}