<?php

namespace App\Services;

use App\Models\UserAnimal;
use App\Models\Vaccine;
use App\Models\VaccineSchedule;

class VaccineScheduleService
{
    /**
     * Generate vaccine schedules for a newly registered UserAnimal.
     * Vaccines are now linked via the animal's category.
     */
    public function generateForUserAnimal(UserAnimal $userAnimal): void
    {
        $vaccines = $userAnimal->animal->category->vaccines;

        foreach ($vaccines as $vaccine) {
            if ($vaccine->is_lifetime) {
                $this->generateLifetimeSchedule($userAnimal, $vaccine);
            } else {
                $this->generateMultiDoseSchedule($userAnimal, $vaccine);
            }
        }
    }

    private function generateLifetimeSchedule(UserAnimal $userAnimal, Vaccine $vaccine): void
    {
        $alreadyExists = VaccineSchedule::where('user_animal_id', $userAnimal->id)
            ->where('vaccine_id', $vaccine->id)
            ->exists();

        if ($alreadyExists) {
            return;
        }

        $scheduledDate = $userAnimal->birth_date
            ? $userAnimal->birth_date->copy()->addDays(30)->toDateString()
            : now()->toDateString();

        VaccineSchedule::create([
            'user_animal_id' => $userAnimal->id,
            'vaccine_id' => $vaccine->id,
            'scheduled_date' => $scheduledDate,
            'status' => 'pending',
        ]);
    }

    private function generateMultiDoseSchedule(UserAnimal $userAnimal, Vaccine $vaccine): void
    {
        $startDate = $userAnimal->last_vaccine_date && $vaccine->interval_days
            ? $userAnimal->last_vaccine_date->copy()->addDays($vaccine->interval_days)
            : now();

        for ($dose = 1; $dose <= $vaccine->doses_count; $dose++) {
            $scheduledDate = $startDate->copy()->addDays(($dose - 1) * ($vaccine->interval_days ?? 0));

            if ($scheduledDate->isPast() && $scheduledDate->toDateString() !== now()->toDateString()) {
                continue;
            }

            $alreadyExists = VaccineSchedule::where('user_animal_id', $userAnimal->id)
                ->where('vaccine_id', $vaccine->id)
                ->where('scheduled_date', $scheduledDate->toDateString())
                ->exists();

            if ($alreadyExists) {
                continue;
            }

            VaccineSchedule::create([
                'user_animal_id' => $userAnimal->id,
                'vaccine_id' => $vaccine->id,
                'scheduled_date' => $scheduledDate->toDateString(),
                'status' => 'pending',
            ]);
        }
    }

    public function markAsDone(VaccineSchedule $schedule): VaccineSchedule
    {
        $schedule->update([
            'taken_at' => now(),
            'status' => 'done',
        ]);

        $vaccine = $schedule->vaccine;

        if (! $vaccine->is_lifetime && $vaccine->interval_days) {
            $nextDate = now()->addDays($vaccine->interval_days)->toDateString();

            $nextExists = VaccineSchedule::where('user_animal_id', $schedule->user_animal_id)
                ->where('vaccine_id', $schedule->vaccine_id)
                ->where('scheduled_date', $nextDate)
                ->where('status', 'pending')
                ->exists();

            if (! $nextExists) {
                VaccineSchedule::create([
                    'user_animal_id' => $schedule->user_animal_id,
                    'vaccine_id' => $schedule->vaccine_id,
                    'scheduled_date' => $nextDate,
                    'status' => 'pending',
                ]);
            }
        }

        return $schedule->fresh();
    }

    public function generateForAll(): int
    {
        $count = 0;
        UserAnimal::with('animal.category.vaccines')->chunk(100, function ($userAnimals) use (&$count) {
            foreach ($userAnimals as $userAnimal) {
                $this->generateForUserAnimal($userAnimal);
                $count++;
            }
        });

        return $count;
    }
}
