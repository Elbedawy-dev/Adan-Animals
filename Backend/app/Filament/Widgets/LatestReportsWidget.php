<?php

namespace App\Filament\Widgets;

use App\Models\DiseaseReport;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestReportsWidget extends BaseWidget
{
    protected static ?int $sort = 3;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->heading(__('filament.widgets.latest_reports_heading'))
            ->query(
                DiseaseReport::query()
                    ->with(['reporter', 'category', 'region'])
                    ->latest()
                    ->limit(8)
            )
            ->paginated(false)
            ->columns([
                TextColumn::make('title')->limit(40)->searchable(),
                TextColumn::make('reporter.name')->label(__('filament.labels.reporter')),
                TextColumn::make('category.name')->label(__('filament.labels.animal_category'))->badge()->color('info'),
                TextColumn::make('region.name')->label(__('filament.labels.region'))->placeholder('—'),
                TextColumn::make('severity')
                    ->formatStateUsing(fn (?string $state): string => $state ? __("filament.severity.{$state}") : '')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'low' => 'success',
                        'moderate' => 'warning',
                        'high' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('status')
                    ->formatStateUsing(fn (?string $state): string => $state ? __("filament.report_status.{$state}") : '')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'pending' => 'warning',
                        'approved' => 'success',
                        'rejected' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('created_at')->since()->label(__('filament.labels.submitted')),
            ]);
    }
}
