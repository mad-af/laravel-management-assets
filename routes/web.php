<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\AssetLogController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\LocationController;

Route::get('/', function () {
    return view('welcome');
});

// Authentication Routes (only for guests)
Route::middleware('guest')->group(function () {
    Route::get('/login', function () {
        return view('auth.login');
    })->name('login');

    Route::post('/login', [LoginController::class, 'authenticate'])->name('login.authenticate');

    // Password Reset Routes
    Route::get('/forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])
        ->name('password.request');

    Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])
        ->name('password.email');

    Route::get('/reset-password/{token}', [ResetPasswordController::class, 'showResetForm'])
        ->name('password.reset');

    Route::post('/reset-password', [ResetPasswordController::class, 'reset'])
        ->name('password.update');
});

// Logout route (for authenticated users)
Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

// Dashboard Routes
Route::prefix('dashboard')->middleware('auth')->group(function () {
    Route::get('/', function () {
        return view('dashboard.index');
    })->name('dashboard');
    
    // User Management Routes
    Route::resource('users', UserController::class);
    
    // Asset Management Routes
    Route::resource('assets', AssetController::class);
    Route::patch('assets/{asset}/status', [AssetController::class, 'updateStatus'])->name('assets.update-status');
    Route::get('assets/export', [AssetController::class, 'export'])->name('assets.export');
    Route::get('assets/statistics', [AssetController::class, 'statistics'])->name('assets.statistics');
    
    // Asset Log Routes
    Route::get('asset-logs/export', [AssetLogController::class, 'export'])->name('asset-logs.export');
    Route::get('asset-logs/statistics', [AssetLogController::class, 'statistics'])->name('asset-logs.statistics');
    Route::get('assets/{asset}/logs', [AssetLogController::class, 'forAsset'])->name('assets.logs');
    Route::get('asset-logs/for-asset/{asset}', [AssetLogController::class, 'forAsset'])->name('asset-logs.for-asset');
    Route::resource('asset-logs', AssetLogController::class)->only(['index', 'show']);
    
    // Category Management Routes
    Route::resource('categories', CategoryController::class);
    Route::patch('categories/{category}/activate', [CategoryController::class, 'activate'])->name('categories.activate');
    
    // Location Management Routes
    Route::resource('locations', LocationController::class);
    Route::patch('locations/{location}/activate', [LocationController::class, 'activate'])->name('locations.activate');
});
