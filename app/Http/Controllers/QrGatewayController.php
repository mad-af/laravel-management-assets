<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class QrGatewayController extends Controller
{
    /**
     * QR Code Gateway - generates secure token and redirects to public asset view
     */
    public function gateway(Request $request, $tag_code)
    {
        try {
            // Validate that asset exists
            $asset = Asset::with(['category', 'branch', 'company'])->where('tag_code', $tag_code)->first();

        if (! $asset) {
            abort(404, 'Asset tidak ditemukan');
        }

        // Generate secure token
        $token = Str::random(64);

        // Store token in cache with asset ID for 1 hour
        Cache::put("asset_token_{$token}", $asset->id, now()->addHour());

        // Redirect to public asset view with token
        return redirect()->route('public.asset.show', ['token' => $token]);
        } catch (\Throwable $th) {
            dd($th);
        }
    }

    /**
     * Public Asset Display - shows asset details with token authentication
     */
    public function showPublicAsset(Request $request, $token)
    {
        // Validate token and get asset ID
        $assetId = Cache::get("asset_token_{$token}");

        if (! $assetId) {
            abort(403, 'Token tidak valid atau sudah kedaluwarsa');
        }

        // Get asset with relationships
        $asset = Asset::with([
            'category',
            'branch',
            'company',
            'vehicleProfile',
            'currentLoan.employee',
        ])->find($assetId);

        if (! $asset) {
            abort(404, 'Asset tidak ditemukan');
        }

        // Return public asset view
        return view('public.asset-detail', compact('asset', 'token'));
    }
}
