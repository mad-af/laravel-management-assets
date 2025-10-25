<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule the vehicle tax due date check to run daily at 00:00
Schedule::command('vehicle-tax:check-due-dates')
    ->dailyAt('00:00')
    ->timezone('Asia/Jakarta')
    ->appendOutputTo(storage_path('logs/vehicle-tax-check.log'))
    ->onSuccess(function () {
        Log::info('Schedule vehicle-tax:check-due-dates succeeded', ['timestamp' => now()->toDateTimeString()]);
    })
    ->onFailure(function () {
        Log::error('Schedule vehicle-tax:check-due-dates failed', ['timestamp' => now()->toDateTimeString()]);
    });

// Schedule the unpaid vehicle tax notification email to run daily at 08:00
Schedule::command('vehicle-tax:notify-unpaid')
    ->dailyAt('08:00')
    ->timezone('Asia/Jakarta')
    ->appendOutputTo(storage_path('logs/vehicle-tax-notify-unpaid.log'))
    ->onSuccess(function () {
        Log::info('Schedule vehicle-tax:notify-unpaid succeeded', ['timestamp' => now()->toDateTimeString()]);
    })
    ->onFailure(function () {
        Log::error('Schedule vehicle-tax:notify-unpaid failed', ['timestamp' => now()->toDateTimeString()]);
    });

// Schedule cleanup of public storage temp files older than 3 days, daily at 01:00
Schedule::command('storage:cleanup-temp')
    ->dailyAt('01:00')
    ->timezone('Asia/Jakarta')
    ->appendOutputTo(storage_path('logs/storage-cleanup-temp.log'))
    ->onSuccess(function () {
        Log::info('Schedule storage:cleanup-temp succeeded', ['timestamp' => now()->toDateTimeString()]);
    })
    ->onFailure(function () {
        Log::error('Schedule storage:cleanup-temp failed', ['timestamp' => now()->toDateTimeString()]);
    });

// Schedule to deactivate expired insurance policies daily at 00:10
Schedule::command('insurance-policy:deactivate-expired')
    ->dailyAt('00:10')
    ->timezone('Asia/Jakarta')
    ->appendOutputTo(storage_path('logs/insurance-policy-deactivate-expired.log'))
    ->onSuccess(function () {
        Log::info('Schedule insurance-policy:deactivate-expired succeeded', ['timestamp' => now()->toDateTimeString()]);
    })
    ->onFailure(function () {
        Log::error('Schedule insurance-policy:deactivate-expired failed', ['timestamp' => now()->toDateTimeString()]);
    });
