<?php

use App\Jobs\SendVaccineRemindersJob;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::job(new SendVaccineRemindersJob())->dailyAt('08:00')
    ->name('send-vaccine-reminders')
    ->withoutOverlapping();
