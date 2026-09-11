<?php

namespace App\Filament\Resources;

use App\Filament\Forms\TranslatableFields;
use App\Filament\Resources\CityResource\Pages;
use App\Filament\Traits\ChecksResourcePermissions;
use App\Models\City;
use App\Models\Governorate;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CityResource extends Resource
{
    use ChecksResourcePermissions;

    protected static string $permissionPrefix = 'locations';
    protected static ?string $model = City::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';

    protected static ?int $navigationSort = 3;

    public static function getNavigationGroup(): ?string
    {
        return __('filament.nav_locations');
    }

    public static function getNavigationLabel(): string
    {
        return __('filament.resources.city.navigation');
    }

    public static function getModelLabel(): string
    {
        return __('filament.resources.city.model');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament.resources.city.plural');
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Select::make('governorate_id')
                ->label(__('filament.labels.governorate'))
                ->options(
                    Governorate::with('country')
                        ->get()
                        ->mapWithKeys(fn ($g) => [$g->id => "{$g->name} ({$g->country->name})"])
                )
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
                TextColumn::make('name')->searchable()->sortable()->label(__('filament.labels.city_name')),
                TextColumn::make('governorate.name')->label(__('filament.labels.governorate'))->sortable(),
                TextColumn::make('governorate.country.name')->label(__('filament.labels.country'))->sortable(),
                TextColumn::make('regions_count')->counts('regions')->label(__('filament.labels.regions_count'))->sortable(),
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
            'index' => Pages\ListCities::route('/'),
            'create' => Pages\CreateCity::route('/create'),
            'edit' => Pages\EditCity::route('/{record}/edit'),
        ];
    }
}
