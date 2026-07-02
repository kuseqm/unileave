<?php

use Illuminate\Support\Facades\Schedule;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('leave:check-exceeded')->dailyAt('09:00');

// Run every hour to check for unresponsive acting staff
Schedule::command('leave:escalate-unresponsive')->hourly();