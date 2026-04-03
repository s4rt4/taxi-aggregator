<?php

use App\Jobs\ProcessWeeklyPayouts;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Weekly payout processing - every Monday at 6am
Schedule::job(new ProcessWeeklyPayouts)->weeklyOn(1, '06:00');
