<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\UserController;

Route::get('/', function () {
    return view('welcome');
});

// Authentication Routes
Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::post('/login', [LoginController::class, 'authenticate'])->name('login.authenticate');

Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Password Reset Routes
Route::get('/forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])
    ->name('password.request');

Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])
    ->name('password.email');

Route::get('/reset-password/{token}', [ResetPasswordController::class, 'showResetForm'])
    ->name('password.reset');

Route::post('/reset-password', [ResetPasswordController::class, 'reset'])
    ->name('password.update');

// Dashboard Routes
Route::prefix('dashboard')->group(function () {
    Route::get('/', function () {
        return view('dashboard.index');
    })->name('dashboard');
    
    Route::get('/tables', function () {
        return view('dashboard.tables');
    })->name('dashboard.tables');
    
    Route::get('/forms', function () {
        return view('dashboard.forms');
    })->name('dashboard.forms');
    
    Route::get('/components', function () {
        return view('dashboard.components');
    })->name('dashboard.components');
    
    Route::get('/settings', function () {
        return view('dashboard.settings');
    })->name('dashboard.settings');
    
    // User Management Routes
    Route::resource('users', UserController::class);
});
