<?php

namespace App\Filament\Widgets;

use App\Models\Region;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\DB;

class RegionalAlertMapWidget extends Widget
{
    protected static ?int $sort = 4;

    protected static string $view = 'filament.widgets.regional-alert-map-widget';

    protected int|string|array $columnSpan = 'full';

    public function getViewData(): array
    {
        $rows = DB::table('disease_reports')
            ->where('status', 'approved')
            ->whereNotNull('region_id')
            ->whereMonth('reviewed_at', now()->month)
            ->whereYear('reviewed_at', now()->year)
            ->selectRaw('region_id, COUNT(*) as reports_count, MAX(reviewed_at) as last_report')
            ->groupBy('region_id')
            ->orderByDesc('reports_count')
            ->limit(10)
            ->get();

        $regionIds = $rows->pluck('region_id')->filter()->all();
        $regions = Region::with('city.governorate')->whereIn('id', $regionIds)->get()->keyBy('id');

        $topRegions = $rows->map(function ($row) use ($regions) {
            return (object) [
                'reports_count' => $row->reports_count,
                'last_report' => $row->last_report,
                'region' => $regions->get($row->region_id),
            ];
        });

        return ['topRegions' => $topRegions];
    }
}
