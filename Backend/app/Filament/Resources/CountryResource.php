<?php

namespace App\Filament\Resources;

use App\Filament\Forms\TranslatableFields;
use App\Filament\Resources\CountryResource\Pages;
use App\Filament\Traits\ChecksResourcePermissions;
use App\Models\Country;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CountryResource extends Resource
{
    use ChecksResourcePermissions;

    protected static string $permissionPrefix = 'locations';
    protected static ?string $model = Country::class;

    protected static ?string $navigationIcon = 'heroicon-o-globe-alt';

    protected static ?int $navigationSort = 1;

    public static function getNavigationGroup(): ?string
    {
        return __('filament.nav_locations');
    }

    public static function getNavigationLabel(): string
    {
        return __('filament.resources.country.navigation');
    }

    public static function getModelLabel(): string
    {
        return __('filament.resources.country.model');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament.resources.country.plural');
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            ...TranslatableFields::nameSection(100),
            TextInput::make('code')
                ->required()
                ->maxLength(5)
                ->label(__('filament.labels.iso_code'))
                ->helperText(__('filament.labels.iso_helper'))
                ->validationAttribute(__('filament.labels.iso_code'))
                ->extraInputAttributes(['class' => 'uppercase'])
                ->afterStateUpdated(function (Set $set, $state): void {
                    if (! is_string($state) || $state === '') {
                        return;
                    }
                    $normalized = strtoupper(trim($state));
                    if ($normalized !== $state) {
                        $set('code', $normalized);
                    }
                })
                ->dehydrateStateUsing(fn (?string $state): ?string => $state === null || $state === '' ? $state : strtoupper(trim($state)))
                ->unique(Country::class, 'code', ignoreRecord: true)
                ->validationMessages([
                    'unique' => __('filament.labels.iso_unique'),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->sortable()->width(60)->label(__('filament.labels.id')),
                TextColumn::make('name')->searchable()->sortable()->label(__('filament.labels.country_name')),
                TextColumn::make('code')->badge()->color('success'),
                TextColumn::make('governorates_count')
                    ->counts('governorates')
                    ->label(__('filament.labels.governorates_count'))
                    ->sortable(),
                TextColumn::make('created_at')->dateTime()->sortable()->toggleable()->label(__('filament.labels.created_at')),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([DeleteBulkAction::make()]),
            ])
            ->defaultSort('name');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCountries::route('/'),
            'create' => Pages\CreateCountry::route('/create'),
            'edit' => Pages\EditCountry::route('/{record}/edit'),
        ];
    }
}
