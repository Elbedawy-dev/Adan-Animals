<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Animal;
use App\Models\AnimalCategory;
use Illuminate\Http\JsonResponse;

class AnimalController extends Controller
{
    public function categories(): JsonResponse
    {
        $categories = AnimalCategory::withCount('animals')->orderBy('name')->get();

        return response()->json(['data' => $categories]);
    }

    public function byCategory(AnimalCategory $category): JsonResponse
    {
        $animals = $category->animals()->with('vaccines')->orderBy('name')->get();

        return response()->json([
            'data' => $animals,
            'category' => $category,
        ]);
    }

    public function show(Animal $animal): JsonResponse
    {
        $animal->load('category', 'vaccines');

        return response()->json(['data' => $animal]);
    }
}
