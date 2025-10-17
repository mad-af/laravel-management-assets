<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule the vehicle tax due date check to run daily at 00:00
Schedule::command('vehicle-tax:check-due-dates')->dailyAt('00:00')->timezone('Asia/Jakarta');

// Schedule the unpaid vehicle tax notification email to run daily at 08:00
Schedule::command('vehicle-tax:notify-unpaid')->dailyAt('08:00')->timezone('Asia/Jakarta');

// Schedule cleanup of public storage temp files older than 3 days, daily at 01:00
Schedule::command('storage:cleanup-temp')->dailyAt('01:00')->timezone('Asia/Jakarta');
