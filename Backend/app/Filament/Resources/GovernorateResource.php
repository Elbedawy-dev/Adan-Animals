<?php

namespace App\Filament\Resources;

use App\Filament\Forms\TranslatableFields;
use App\Filament\Resources\GovernorateResource\Pages;
use App\Filament\Traits\ChecksResourcePermissions;
use App\Models\Country;
use App\Models\Governorate;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class GovernorateResource extends Resource
{
    use ChecksResourcePermissions;

    protected static string $permissionPrefix = 'locations';
    protected static ?string $model = Governorate::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office';

    protected static ?int $navigationSort = 2;

    public static function getNavigationGroup(): ?string
    {
        return __('filament.nav_locations');
    }

    public static function getNavigationLabel(): string
    {
        return __('filament.resources.governorate.navigation');
    }

    public static function getModelLabel(): string
    {
        return __('filament.resources.governorate.model');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament.resources.governorate.plural');
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Select::make('country_id')
                ->label(__('filament.labels.country'))
                ->options(Country::all()->pluck('name', 'id'))
                ->searchable()
                ->required(),
            ...TranslatableFields::nameSection(100),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->sortable()->width(60)->label(__('filament.labels.id')),
                TextColumn::make('name')->searchable()->sortable()->label(__('filament.labels.governorate_name')),
                TextColumn::make('country.name')->label(__('filament.labels.country'))->sortable()->badge()->color('info'),
                TextColumn::make('cities_count')->counts('cities')->label(__('filament.labels.cities_count'))->sortable(),
                TextColumn::make('created_at')->dateTime()->toggleable()->label(__('filament.labels.created_at')),
            ])
            ->filters([
                SelectFilter::make('country_id')
                    ->label(__('filament.labels.country'))
                    ->options(Country::all()->pluck('name', 'id'))
                    ->searchable(),
            ])
            ->actions([EditAction::make(), DeleteAction::make()])
            ->bulkActions([BulkActionGroup::make([DeleteBulkAction::make()])])
            ->defaultSort('name');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListGovernorates::route('/'),
            'create' => Pages\CreateGovernorate::route('/create'),
            'edit' => Pages\EditGovernorate::route('/{record}/edit'),
        ];
    }
}
