<?php

namespace App\Filament\Widgets;

use App\Models\DiseaseReport;
use App\Models\User;
use App\Models\UserAnimal;
use App\Models\VaccineSchedule;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverviewWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        return [
            Stat::make(__('filament.stats.total_breeders'), User::where('role', 'customer')->count())
                ->description(__('filament.stats.total_breeders_desc'))
                ->descriptionIcon('heroicon-m-users')
                ->color('success'),

            Stat::make(__('filament.stats.animals_tracked'), UserAnimal::count())
                ->description(__('filament.stats.animals_tracked_desc'))
                ->descriptionIcon('heroicon-m-heart')
                ->color('info'),

            Stat::make(__('filament.stats.pending_reports'), DiseaseReport::pending()->count())
                ->description(__('filament.stats.pending_reports_desc'))
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),

            Stat::make(
                __('filament.stats.vaccines_due_week'),
                VaccineSchedule::upcoming(7)->count()
            )
                ->description(__('filament.stats.vaccines_due_week_desc'))
                ->descriptionIcon('heroicon-m-beaker')
                ->color('primary'),

            Stat::make(
                __('filament.stats.approved_reports_month'),
                DiseaseReport::approved()
                    ->whereMonth('reviewed_at', now()->month)
                    ->whereYear('reviewed_at', now()->year)
                    ->count()
            )
                ->description(__('filament.stats.approved_reports_month_desc'))
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),

            Stat::make(__('filament.stats.overdue_vaccines'), VaccineSchedule::overdue()->count())
                ->description(__('filament.stats.overdue_vaccines_desc'))
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color('danger'),
        ];
    }
}
