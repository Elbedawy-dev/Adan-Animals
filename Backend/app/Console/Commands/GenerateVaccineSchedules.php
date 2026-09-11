<?php

namespace App\Console\Commands;

use App\Services\VaccineScheduleService;
use Illuminate\Console\Command;

class GenerateVaccineSchedules extends Command
{
    protected $signature = 'adan:generate-schedules';

    protected $description = 'Generate vaccine schedules for all user animals that are missing schedules';

    public function __construct(private VaccineScheduleService $service)
    {
        parent::__construct();
    }

    public function handle(): void
    {
        $this->info('Generating vaccine schedules...');
        $count = $this->service->generateForAll();
        $this->info("Done! Processed {$count} animals.");
    }
}
