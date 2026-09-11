<?php

namespace App\Jobs;

use App\Models\VaccineSchedule;
use App\Notifications\VaccineReminderNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendVaccineRemindersJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public function handle(): void
    {
        $today = now()->toDateString();
        $tomorrow = now()->addDay()->toDateString();

        VaccineSchedule::query()
            ->where('status', 'pending')
            ->whereBetween('scheduled_date', [$today, $tomorrow])
            ->where(function ($q) {
                $q->whereNull('notified_at')
                    ->orWhere('notified_at', '<', now()->subHours(20));
            })
            ->with(['userAnimal.user', 'vaccine'])
            ->chunk(50, function ($schedules) {
                foreach ($schedules as $schedule) {
                    $user = $schedule->userAnimal?->user;

                    if (! $user || ! $user->hasVerifiedEmail()) {
                        continue;
                    }

                    $user->notify(new VaccineReminderNotification($schedule));

                    $schedule->update(['notified_at' => now()]);
                }
            });
    }
}
