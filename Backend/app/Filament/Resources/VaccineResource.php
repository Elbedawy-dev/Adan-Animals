<?php

namespace App\Filament\Resources;

use App\Filament\Forms\TranslatableFields;
use App\Filament\Resources\VaccineResource\Pages;
use App\Filament\Traits\ChecksResourcePermissions;
use App\Models\AnimalCategory;
use App\Models\Vaccine;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class VaccineResource extends Resource
{
    use ChecksResourcePermissions;

    protected static string $permissionPrefix = 'vaccines';
    protected static ?string $model = Vaccine::class;

    protected static ?string $navigationIcon = 'heroicon-o-beaker';

    protected static ?int $navigationSort = 3;

    public static function getNavigationGroup(): ?string
    {
        return __('filament.nav_animals');
    }

    public static function getNavigationLabel(): string
    {
        return __('filament.resources.vaccine.navigation');
    }

    public static function getModelLabel(): string
    {
        return __('filament.resources.vaccine.model');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament.resources.vaccine.plural');
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Select::make('animal_category_id')
                ->label(__('filament.labels.animal_category'))
                ->options(AnimalCategory::all()->pluck('name', 'id'))
                ->searchable()
                ->required(),
            ...TranslatableFields::nameSection(100),
            Toggle::make('is_lifetime')
                ->label(__('filament.labels.lifetime_vaccine'))
                ->live()
                ->afterStateUpdated(function ($state, callable $set) {
                    if ($state) {
                        $set('doses_count', 1);
                        $set('interval_days', null);
                    }
                }),
            TextInput::make('doses_count')
                ->numeric()
                ->required()
                ->default(1)
                ->minValue(1)
                ->label(__('filament.labels.number_of_doses'))
                ->disabled(fn (Get $get): bool => (bool) $get('is_lifetime'))
                ->dehydrated(),
            TextInput::make('interval_days')
                ->numeric()->nullable()->minValue(1)
                ->label(__('filament.labels.interval_between_doses'))
                ->helperText(__('filament.labels.interval_helper'))
                ->hidden(fn (Get $get): bool => (bool) $get('is_lifetime'))
                ->dehydrated(fn (Get $get): bool => ! (bool) $get('is_lifetime')),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->sortable()->width(60)->label(__('filament.labels.id')),
                TextColumn::make('name')->searchable()->sortable()->label(__('filament.labels.vaccine_name')),
                TextColumn::make('animalCategory.name')->label(__('filament.labels.animal_category'))->sortable()->badge()->color('info'),
                TextColumn::make('doses_count')->label(__('filament.labels.doses')),
                TextColumn::make('interval_days')->label(__('filament.labels.interval_days'))->placeholder('—'),
                IconColumn::make('is_lifetime')->label(__('filament.labels.lifetime'))->boolean(),
                TextColumn::make('created_at')->dateTime()->toggleable()->label(__('filament.labels.created_at')),
            ])
            ->filters([
                SelectFilter::make('animal_category_id')
                    ->label(__('filament.labels.animal_category'))
                    ->options(AnimalCategory::all()->pluck('name', 'id'))
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
            'index' => Pages\ListVaccines::route('/'),
            'create' => Pages\CreateVaccine::route('/create'),
            'edit' => Pages\EditVaccine::route('/{record}/edit'),
        ];
    }
}
