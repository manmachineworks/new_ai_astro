<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::job(new \App\Jobs\ChatBillingJob)->everyMinute();
Schedule::command('appointments:reminders')->everyMinute();
Schedule::command('slots:generate')->dailyAt('00:30');
