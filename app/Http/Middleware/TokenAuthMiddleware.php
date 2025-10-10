<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class TokenAuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->route('token') ?? $request->get('token');
        
        if (!$token) {
            abort(403, 'Token akses diperlukan');
        }

        // Check if token exists in cache
        $assetId = Cache::get("asset_token_{$token}");
        
        if (!$assetId) {
            abort(403, 'Token tidak valid atau sudah kedaluwarsa');
        }

        // Add asset ID to request for controller use
        $request->merge(['validated_asset_id' => $assetId]);
        
        return $next($request);
    }
}
