<?php

use App\Jobs\GenerateCorporateInvoices;
use App\Jobs\ProcessWeeklyPayouts;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Weekly payout processing - every Monday at 6am
Schedule::job(new ProcessWeeklyPayouts)->weeklyOn(1, '06:00');

// Corporate invoices
Schedule::job(new GenerateCorporateInvoices('weekly'))->weeklyOn(1, '07:00');
Schedule::job(new GenerateCorporateInvoices('monthly'))->monthlyOn(1, '07:00');
