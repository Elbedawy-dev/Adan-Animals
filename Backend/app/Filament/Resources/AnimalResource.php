<?php

namespace App\Filament\Resources;

use App\Filament\Forms\TranslatableFields;
use App\Filament\Resources\AnimalResource\Pages;
use App\Filament\Resources\AnimalResource\RelationManagers\VaccinesRelationManager;
use App\Filament\Traits\ChecksResourcePermissions;
use App\Models\Animal;
use App\Models\AnimalCategory;
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

class AnimalResource extends Resource
{
    use ChecksResourcePermissions;

    protected static string $permissionPrefix = 'animals';
    protected static ?string $model = Animal::class;

    protected static ?string $navigationIcon = 'heroicon-o-heart';

    protected static ?int $navigationSort = 2;

    public static function getNavigationGroup(): ?string
    {
        return __('filament.nav_animals');
    }

    public static function getNavigationLabel(): string
    {
        return __('filament.resources.animal.navigation');
    }

    public static function getModelLabel(): string
    {
        return __('filament.resources.animal.model');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament.resources.animal.plural');
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Select::make('category_id')
                ->label(__('filament.labels.category'))
                ->options(AnimalCategory::all()->pluck('name', 'id'))
                ->searchable()
                ->required(),
            ...TranslatableFields::nameAndDescriptionSections(100, 4),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->sortable()->width(60)->label(__('filament.labels.id')),
                TextColumn::make('name')->searchable()->sortable()->label(__('filament.labels.animal_name')),
                TextColumn::make('category.name')->label(__('filament.labels.category'))->badge()->color('success')->sortable(),
                TextColumn::make('vaccines_count')->counts('vaccines')->label(__('filament.labels.vaccines'))->sortable(),
                TextColumn::make('created_at')->dateTime()->toggleable()->label(__('filament.labels.created_at')),
            ])
            ->filters([
                SelectFilter::make('category_id')
                    ->label(__('filament.labels.category'))
                    ->options(AnimalCategory::all()->pluck('name', 'id')),
            ])
            ->actions([EditAction::make(), DeleteAction::make()])
            ->bulkActions([BulkActionGroup::make([DeleteBulkAction::make()])])
            ->defaultSort('name');
    }

    public static function getRelations(): array
    {
        return [
            VaccinesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAnimals::route('/'),
            'create' => Pages\CreateAnimal::route('/create'),
            'edit' => Pages\EditAnimal::route('/{record}/edit'),
        ];
    }
}
