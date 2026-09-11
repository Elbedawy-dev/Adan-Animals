<?php

namespace App\Console\Commands;

use App\Jobs\SendVaccineRemindersJob;
use Illuminate\Console\Command;

class SendVaccineReminders extends Command
{
    protected $signature = 'adan:vaccine-reminders';

    protected $description = 'Dispatch vaccine reminder notifications to all users with upcoming vaccines';

    public function handle(): void
    {
        $this->info('Dispatching vaccine reminder job...');
        dispatch(new SendVaccineRemindersJob());
        $this->info('Done! Check your queue worker.');
    }
}
