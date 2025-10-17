<?php

namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

class ServiceTasksCast implements CastsAttributes
{
    /**
     * Cast the given value.
     *
     * @param  mixed  $value
     * @return array<int, array{task:string, completed:bool}>
     */
    public function get($model, string $key, $value, array $attributes)
    {
        // Convert storage value (json string or array) to normalized array of {task, completed}
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
                $taskText = isset($item['task'])
                    ? (string) $item['task']
                    : (is_string($item['value'] ?? null) ? (string) $item['value'] : json_encode($item, JSON_UNESCAPED_UNICODE));
                $completed = (bool) ($item['completed'] ?? false);
            } else {
                $taskText = is_string($item) ? $item : json_encode($item, JSON_UNESCAPED_UNICODE);
                $completed = false;
            }

            $taskText = trim($taskText);
            if ($taskText !== '') {
                $result[] = [
                    'task' => $taskText,
                    'completed' => $completed,
                ];
            }
        }

        return $result;
    }

    /**
     * Prepare the given value for storage.
     *
     * @param  mixed  $value
     * @return string JSON string suitable for storage
     */
    public function set($model, string $key, $value, array $attributes)
    {
        $normalized = [];

        if (is_array($value)) {
            // Treat associative single object as one item
            $isAssoc = array_keys($value) !== range(0, count($value) - 1);
            if ($isAssoc && array_key_exists('task', $value)) {
                $value = [$value];
                $isAssoc = false;
            }

            if (! $isAssoc) {
                foreach ($value as $item) {
                    if (is_array($item)) {
                        $taskText = isset($item['task'])
                            ? (string) $item['task']
                            : (is_string($item['value'] ?? null) ? (string) $item['value'] : json_encode($item, JSON_UNESCAPED_UNICODE));
                        $completed = (bool) ($item['completed'] ?? false);
                    } else {
                        $taskText = is_string($item) ? $item : json_encode($item, JSON_UNESCAPED_UNICODE);
                        $completed = false;
                    }

                    $taskText = trim($taskText);
                    if ($taskText !== '') {
                        $normalized[] = [
                            'task' => $taskText,
                            'completed' => $completed,
                        ];
                    }
                }
            } else {
                // Attempt to normalize associative arrays sensibly
                if (isset($value['task'])) {
                    $normalized[] = [
                        'task' => trim((string) $value['task']),
                        'completed' => (bool) ($value['completed'] ?? false),
                    ];
                } else {
                    foreach ($value as $k => $v) {
                        if (is_string($v)) {
                            $t = trim($v);
                            if ($t !== '') {
                                $normalized[] = [
                                    'task' => $t,
                                    'completed' => false,
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
                    'task' => $t,
                    'completed' => false,
                ];
            }
        } elseif ($value === null) {
            $normalized = [];
        } else {
            $normalized[] = [
                'task' => json_encode($value, JSON_UNESCAPED_UNICODE),
                'completed' => false,
            ];
        }

        // Store as JSON string to be DB-driver agnostic
        return json_encode($normalized, JSON_UNESCAPED_UNICODE);
    }
}
