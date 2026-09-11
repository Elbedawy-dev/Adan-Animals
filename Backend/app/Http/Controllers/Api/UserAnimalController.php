<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserAnimal;
use App\Services\VaccineScheduleService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserAnimalController extends Controller
{
    public function __construct(private VaccineScheduleService $scheduleService) {}

    public function index(Request $request): JsonResponse
    {
        $animals = $request->user()
            ->userAnimals()
            ->with([
                'animal.category',
                'animal.vaccines',
                'pendingSchedules.vaccine',
            ])
            ->withCount([
                'overdueSchedules as overdue_count',
            ])
            ->latest()
            ->get();

        return response()->json(['data' => $animals]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'animal_id' => 'required|exists:animals,id',
            'nickname' => 'nullable|string|max:100',
            'birth_date' => 'nullable|date|before:today',
            'last_vaccine_date' => 'nullable|date|before_or_equal:today',
            'notes' => 'nullable|string|max:500',
        ]);

        $userAnimal = $request->user()->userAnimals()->create($validated);
        $userAnimal->load('animal.category', 'animal.vaccines');

        $this->scheduleService->generateForUserAnimal($userAnimal);

        $userAnimal->load('pendingSchedules.vaccine');

        return response()->json([
            'message' => __('api.animal_registered'),
            'data' => $userAnimal,
        ], 201);
    }

    public function show(Request $request, UserAnimal $userAnimal): JsonResponse
    {
        if ($userAnimal->user_id !== $request->user()->id) {
            return response()->json(['message' => __('api.forbidden')], 403);
        }

        $userAnimal->load([
            'animal.category',
            'animal.vaccines',
            'vaccineSchedules.vaccine',
        ]);

        return response()->json(['data' => $userAnimal]);
    }

    public function destroy(Request $request, UserAnimal $userAnimal): JsonResponse
    {
        if ($userAnimal->user_id !== $request->user()->id) {
            return response()->json(['message' => __('api.forbidden')], 403);
        }

        $userAnimal->delete();

        return response()->json(['message' => __('api.animal_removed')]);
    }
}
