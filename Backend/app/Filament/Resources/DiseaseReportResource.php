<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DiseaseReportResource\Pages;
use App\Filament\Traits\ChecksResourcePermissions;
use App\Jobs\SendDiseaseAlertJob;
use App\Models\DiseaseReport;
use App\Models\Region;
use App\Notifications\ReportStatusNotification;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class DiseaseReportResource extends Resource
{
    use ChecksResourcePermissions;

    protected static string $permissionPrefix = 'disease_reports';
    protected static ?string $model = DiseaseReport::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-magnifying-glass';

    protected static ?string $navigationBadge = null;

    public static function getNavigationGroup(): ?string
    {
        return __('filament.nav_reports');
    }

    public static function getNavigationLabel(): string
    {
        return __('filament.resources.disease_report.navigation');
    }

    public static function getModelLabel(): string
    {
        return __('filament.resources.disease_report.model');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament.resources.disease_report.plural');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['reporter', 'category', 'region']);
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) DiseaseReport::pending()->count() ?: null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make(__('filament.disease_report.section_report'))
                    ->schema([
                        TextEntry::make('title'),
                        TextEntry::make('description')->columnSpanFull(),
                        TextEntry::make('severity')
                            ->formatStateUsing(fn (?string $state): string => $state ? __("filament.severity.{$state}") : '')
                            ->badge()
                            ->color(fn (string $state) => match ($state) {
                                'low' => 'success',
                                'moderate' => 'warning',
                                'high' => 'danger',
                                default => 'gray',
                            }),
                        TextEntry::make('status')
                            ->formatStateUsing(fn (?string $state): string => $state ? __("filament.report_status.{$state}") : '')
                            ->badge()
                            ->color(fn (string $state) => match ($state) {
                                'pending' => 'warning',
                                'approved' => 'success',
                                'rejected' => 'danger',
                                default => 'gray',
                            }),
                        TextEntry::make('rejection_reason')
                            ->label(__('filament.labels.rejection_reason'))
                            ->placeholder('—')
                            ->columnSpanFull()
                            ->visible(fn (DiseaseReport $record) => $record->isRejected()),
                        TextEntry::make('latitude')
                            ->label(__('filament.labels.latitude'))
                            ->placeholder('—'),
                        TextEntry::make('longitude')
                            ->label(__('filament.labels.longitude'))
                            ->placeholder('—'),
                        TextEntry::make('created_at')->dateTime()->label(__('filament.labels.submitted')),
                        TextEntry::make('reviewed_at')->dateTime()->label(__('filament.labels.reviewed'))->placeholder('—'),
                    ])
                    ->columns(2),
                Section::make(__('filament.disease_report.section_context'))
                    ->schema([
                        TextEntry::make('reporter.name')->label(__('filament.labels.reporter')),
                        TextEntry::make('reporter.email')->label(__('filament.labels.reporter_email')),
                        TextEntry::make('category.name')->label(__('filament.labels.animal_category')),
                        TextEntry::make('region.name')->label(__('filament.labels.region'))->placeholder('—'),
                    ])
                    ->columns(2),
                Section::make(__('filament.disease_report.section_images'))
                    ->schema([
                        ImageEntry::make('images')
                            ->label('')
                            ->getStateUsing(fn (DiseaseReport $record): array => $record->getMedia('images')
                                ->map(fn ($media) => $media->getUrl())
                                ->filter()
                                ->values()
                                ->all())
                            ->height('10rem')
                            ->columnSpanFull()
                            ->defaultImageUrl(null),
                    ])
                    ->visible(fn (DiseaseReport $record) => $record->getMedia('images')->isNotEmpty()),
            ]);
    }

    public static function form(Form $form): Form
    {
        $severityOptions = [
            'low' => __('filament.severity.low'),
            'moderate' => __('filament.severity.moderate'),
            'high' => __('filament.severity.high'),
        ];
        $statusOptions = [
            'pending' => __('filament.report_status.pending'),
            'approved' => __('filament.report_status.approved'),
            'rejected' => __('filament.report_status.rejected'),
        ];

        return $form->schema([
            TextInput::make('title')->disabled()->columnSpanFull(),
            Textarea::make('description')->disabled()->rows(4)->columnSpanFull(),
            TextInput::make('reporter.name')->disabled()->label(__('filament.labels.submitted_by')),
            TextInput::make('category.name')->disabled()->label(__('filament.labels.animal_category')),
            TextInput::make('region.name')->disabled()->label(__('filament.labels.region')),
            Select::make('severity')
                ->options($severityOptions)
                ->disabled(),
            Select::make('status')
                ->options($statusOptions)
                ->required()
                ->live(),
            Textarea::make('rejection_reason')
                ->nullable()
                ->rows(3)
                ->label(__('filament.labels.rejection_reason_label'))
                ->helperText(__('filament.disease_report.helper_reject'))
                ->visible(fn (Get $get) => $get('status') === 'rejected'),
        ]);
    }

    public static function table(Table $table): Table
    {
        $severityOptions = [
            'low' => __('filament.severity.low'),
            'moderate' => __('filament.severity.moderate'),
            'high' => __('filament.severity.high'),
        ];
        $statusOptions = [
            'pending' => __('filament.report_status.pending'),
            'approved' => __('filament.report_status.approved'),
            'rejected' => __('filament.report_status.rejected'),
        ];

        return $table
            ->columns([
                TextColumn::make('id')->sortable()->width(60)->label(__('filament.labels.id')),
                TextColumn::make('title')->searchable()->limit(40)->sortable()->label(__('filament.labels.title')),
                TextColumn::make('reporter.name')->label(__('filament.labels.reporter'))->searchable(),
                TextColumn::make('category.name')->label(__('filament.labels.animal_category'))->sortable(),
                TextColumn::make('region.name')->label(__('filament.labels.region'))->sortable(),
                TextColumn::make('severity')
                    ->formatStateUsing(fn (?string $state): string => $state ? __("filament.severity.{$state}") : '')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'low' => 'success',
                        'moderate' => 'warning',
                        'high' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('status')
                    ->formatStateUsing(fn (?string $state): string => $state ? __("filament.report_status.{$state}") : '')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'pending' => 'warning',
                        'approved' => 'success',
                        'rejected' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('created_at')->dateTime()->sortable()->label(__('filament.labels.submitted')),
            ])
            ->filters([
                SelectFilter::make('status')->options($statusOptions),
                SelectFilter::make('severity')->options($severityOptions),
                SelectFilter::make('region_id')
                    ->label(__('filament.labels.region'))
                    ->options(Region::all()->pluck('name', 'id'))
                    ->searchable(),
            ])
            ->actions([
                ViewAction::make(),
                Action::make('approve')
                    ->label(__('filament.disease_report.action_approve'))
                    ->color('success')
                    ->icon('heroicon-o-check-circle')
                    ->requiresConfirmation()
                    ->modalHeading(__('filament.disease_report.modal_approve_heading'))
                    ->modalDescription(__('filament.disease_report.modal_approve_description'))
                    ->visible(fn (DiseaseReport $record) => $record->isPending())
                    ->action(function (DiseaseReport $record) {
                        $record->update([
                            'status' => 'approved',
                            'reviewed_by' => auth()->id(),
                            'reviewed_at' => now(),
                        ]);
                        $fresh = $record->fresh();
                        SendDiseaseAlertJob::dispatch($fresh);
                        $regionLabel = $fresh->region?->name ?? __('filament.disease_report.affected_region_fallback');
                        Notification::make()
                            ->title(__('filament.disease_report.notification_approved_title'))
                            ->body(__('filament.disease_report.notification_approved_body', ['region' => $regionLabel]))
                            ->success()
                            ->send();
                    }),
                Action::make('reject')
                    ->label(__('filament.disease_report.action_reject'))
                    ->color('danger')
                    ->icon('heroicon-o-x-circle')
                    ->visible(fn (DiseaseReport $record) => $record->isPending())
                    ->form([
                        Textarea::make('rejection_reason')
                            ->label(__('filament.labels.reason_for_rejection'))
                            ->required()
                            ->rows(4),
                    ])
                    ->action(function (DiseaseReport $record, array $data) {
                        $record->update([
                            'status' => 'rejected',
                            'rejection_reason' => $data['rejection_reason'],
                            'reviewed_by' => auth()->id(),
                            'reviewed_at' => now(),
                        ]);
                        $record->reporter?->notify(
                            new ReportStatusNotification($record->fresh())
                        );
                        Notification::make()
                            ->title(__('filament.disease_report.notification_rejected_title'))
                            ->warning()
                            ->send();
                    }),
            ])
            ->bulkActions([BulkActionGroup::make([DeleteBulkAction::make()])])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDiseaseReports::route('/'),
            'view' => Pages\ViewDiseaseReport::route('/{record}'),
        ];
    }
}
