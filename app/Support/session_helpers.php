<?php

use App\Support\SessionKey;

if (!function_exists('session_get')) {
    function session_get(SessionKey $key, $default = null) {
        return session($key->value, $default);
    }
}

if (!function_exists('session_put')) {
    function session_put(SessionKey $key, $value): void {
        session([$key->value => $value]);
    }
}