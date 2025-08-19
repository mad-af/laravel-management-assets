<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

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
});
