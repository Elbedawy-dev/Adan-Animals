<?php

namespace App\Jobs;

use App\Models\DiseaseReport;
use App\Models\User;
use App\Notifications\DiseaseAlertNotification;
use App\Notifications\ReportStatusNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendDiseaseAlertJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public int $timeout = 120;

    public function __construct(public DiseaseReport $report) {}

    public function handle(): void
    {
        $report = $this->report->fresh(['region', 'category', 'reporter']);

        if ($report === null) {
            return;
        }

        if ($report->region_id !== null) {
            User::where('region_id', $report->region_id)
                ->where('role', 'customer')
                ->where('id', '!=', $report->user_id)
                ->chunk(50, function ($users) use ($report) {
                    foreach ($users as $user) {
                        if (! $user instanceof User) {
                            continue;
                        }
                        // Mail + database + FCM are all sent by DiseaseAlertNotification::via()
                        $user->notify(new DiseaseAlertNotification($report));
                    }
                });
        }

        // Notify the reporter — mail + database + FCM sent by ReportStatusNotification::via()
        $report->reporter?->notify(new ReportStatusNotification($report));
    }
}
