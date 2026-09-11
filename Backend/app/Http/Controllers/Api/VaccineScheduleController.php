<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\VaccineSchedule;
use App\Services\VaccineScheduleService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VaccineScheduleController extends Controller
{
    public function __construct(private VaccineScheduleService $scheduleService) {}

    public function index(Request $request): JsonResponse
    {
        $query = VaccineSchedule::query()
            ->whereHas('userAnimal', fn ($q) => $q->where('user_id', $request->user()->id))
            ->with(['userAnimal.animal', 'vaccine'])
            ->orderBy('scheduled_date');

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('user_animal_id')) {
            $query->where('user_animal_id', $request->user_animal_id);
        }

        $schedules = $query->get();

        $grouped = [
            'overdue' => $schedules->filter(fn ($s) => $s->isOverdue())->values(),
            'upcoming' => $schedules->filter(fn ($s) => $s->status === 'pending' && ! $s->isOverdue())->values(),
            'done' => $schedules->filter(fn ($s) => $s->status === 'done')->values(),
        ];

        return response()->json([
            'data' => $schedules,
            'grouped' => $grouped,
            'counts' => [
                'overdue' => $grouped['overdue']->count(),
                'upcoming' => $grouped['upcoming']->count(),
                'done' => $grouped['done']->count(),
            ],
        ]);
    }

    public function markDone(Request $request, VaccineSchedule $schedule): JsonResponse
    {
        if ($schedule->userAnimal->user_id !== $request->user()->id) {
            return response()->json(['message' => __('api.vaccine_unauthorized')], 403);
        }

        if ($schedule->status === 'done') {
            return response()->json(['message' => __('api.vaccine_already_done')], 422);
        }

        $updated = $this->scheduleService->markAsDone($schedule);

        return response()->json([
            'message' => __('api.vaccine_marked_done'),
            'data' => $updated->load('vaccine', 'userAnimal.animal'),
        ]);
    }
}
