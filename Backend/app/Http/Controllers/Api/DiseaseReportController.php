<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DiseaseReport;
use App\Notifications\DiseaseReportSubmittedNotification;
use App\Services\PushNotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DiseaseReportController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $reports = DiseaseReport::where('user_id', $request->user()->id)
            ->with([
                'category',
                'region.city.governorate',
                'media',
            ])
            ->latest()
            ->get()
            ->map(fn ($r) => $this->formatReport($r));

        return response()->json(['data' => $reports]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:animal_categories,id',
            'region_id'   => 'required|exists:regions,id',
            'title'       => 'required|string|max:255',
            'description' => 'required|string|min:20',
            'severity'    => 'required|in:low,moderate,high,critical',
            'latitude'    => 'nullable|numeric|between:-90,90',
            'longitude'   => 'nullable|numeric|between:-180,180',
            'images'      => 'nullable|array|max:5',
            'images.*'    => 'image|mimes:jpg,jpeg,png,webp|max:10240',
        ]);

        $report = DiseaseReport::create([
            'user_id'     => $request->user()->id,
            'category_id' => $validated['category_id'],
            'region_id'   => $validated['region_id'],
            'title'       => $validated['title'],
            'description' => $validated['description'],
            'severity'    => $validated['severity'],
            'latitude'    => $validated['latitude'] ?? null,
            'longitude'   => $validated['longitude'] ?? null,
            'status'      => 'pending',
        ]);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $report->addMedia($image)
                    ->toMediaCollection('images');
            }
        }

        $request->user()->notify(new DiseaseReportSubmittedNotification($report));

        app(PushNotificationService::class)->toUsersWithRole('doctor', __('api.fcm_new_report_title'), __('api.fcm_new_report_body', ['title' => $report->title]), [
            'type' => 'new_report',
            'report_id' => (string) $report->id,
        ]);

        $report->load('category', 'region.city.governorate', 'media');

        return response()->json([
            'message' => __('api.report_submitted'),
            'data'    => $this->formatReport($report),
        ], 201);
    }

    public function show(Request $request, DiseaseReport $report): JsonResponse
    {
        if ($report->user_id !== $request->user()->id) {
            return response()->json(['message' => __('api.report_unauthorized')], 403);
        }

        $report->load([
            'category',
            'region.city.governorate',
            'reporter',
            'reviewer',
            'media',
        ]);

        return response()->json(['data' => $this->formatReport($report)]);
    }

    public function approved(): JsonResponse
    {
        $reports = DiseaseReport::approved()
            ->with([
                'category',
                'region.city.governorate',
            ])
            ->latest('reviewed_at')
            ->get()
            ->map(function ($r) {
                return [
                    'id' => $r->id,
                    'title' => $r->title,
                    'severity' => $r->severity,
                    'latitude' => $r->latitude,
                    'longitude' => $r->longitude,
                    'animal' => $r->category->name,
                    'category' => $r->category->name,
                    'region' => $r->region?->name,
                    'city' => $r->region?->city?->name,
                    'governorate' => $r->region?->city?->governorate?->name,
                    'reviewed_at' => $r->reviewed_at,
                    'thumbnail' => $r->getFirstMediaUrl('images', 'thumb'),
                ];
            });

        return response()->json(['data' => $reports]);
    }

    private function formatReport(DiseaseReport $report): array
    {
        return [
            'id' => $report->id,
            'title' => $report->title,
            'description' => $report->description,
            'severity' => $report->severity,
            'status' => $report->status,
            'rejection_reason' => $report->rejection_reason,
            'latitude' => $report->latitude,
            'longitude' => $report->longitude,
            'reviewed_at' => $report->reviewed_at,
            'created_at' => $report->created_at,
            'category' => $report->category ? [
                'id' => $report->category->id,
                'name' => $report->category->name,
            ] : null,
            'region' => $report->region ? [
                'id' => $report->region->id,
                'name' => $report->region->name,
                'city' => $report->region->city?->name,
                'governorate' => $report->region->city?->governorate?->name,
            ] : null,
            'images' => $report->getMedia('images')->map(fn ($m) => [
                'url' => $m->getUrl(),
                'thumb' => $m->getUrl('thumb'),
            ])->toArray(),
        ];
    }
}
