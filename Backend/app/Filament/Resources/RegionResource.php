<?php

namespace App\Filament\Resources;

use App\Filament\Forms\TranslatableFields;
use App\Filament\Resources\RegionResource\Pages;
use App\Filament\Traits\ChecksResourcePermissions;
use App\Models\City;
use App\Models\Region;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class RegionResource extends Resource
{
    use ChecksResourcePermissions;

    protected static string $permissionPrefix = 'locations';
    protected static ?string $model = Region::class;

    protected static ?string $navigationIcon = 'heroicon-o-map';

    protected static ?int $navigationSort = 4;

    public static function getNavigationGroup(): ?string
    {
        return __('filament.nav_locations');
    }

    public static function getNavigationLabel(): string
    {
        return __('filament.resources.region.navigation');
    }

    public static function getModelLabel(): string
    {
        return __('filament.resources.region.model');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament.resources.region.plural');
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Select::make('city_id')
                ->label(__('filament.labels.city'))
                ->options(
                    City::with('governorate')
                        ->get()
                        ->mapWithKeys(fn ($c) => [$c->id => "{$c->name} — {$c->governorate->name}"])
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
                TextColumn::make('name')->searchable()->sortable()->label(__('filament.labels.region_name')),
                TextColumn::make('city.name')->label(__('filament.labels.city'))->sortable(),
                TextColumn::make('city.governorate.name')->label(__('filament.labels.governorate'))->sortable(),
                TextColumn::make('users_count')->counts('users')->label(__('filament.labels.users_count'))->sortable(),
                TextColumn::make('created_at')->dateTime()->toggleable()->label(__('filament.labels.created_at')),
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
            'index' => Pages\ListRegions::route('/'),
            'create' => Pages\CreateRegion::route('/create'),
            'edit' => Pages\EditRegion::route('/{record}/edit'),
        ];
    }
}
