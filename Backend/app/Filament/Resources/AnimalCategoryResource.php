<?php

namespace App\Filament\Resources;

use App\Filament\Forms\TranslatableFields;
use App\Filament\Resources\AnimalCategoryResource\Pages;
use App\Filament\Traits\ChecksResourcePermissions;
use App\Models\AnimalCategory;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AnimalCategoryResource extends Resource
{
    use ChecksResourcePermissions;

    protected static string $permissionPrefix = 'animal_categories';
    protected static ?string $model = AnimalCategory::class;

    protected static ?string $navigationIcon = 'heroicon-o-tag';

    protected static ?int $navigationSort = 1;

    public static function getNavigationGroup(): ?string
    {
        return __('filament.nav_animals');
    }

    public static function getNavigationLabel(): string
    {
        return __('filament.resources.animal_category.navigation');
    }

    public static function getModelLabel(): string
    {
        return __('filament.resources.animal_category.model');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament.resources.animal_category.plural');
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            ...TranslatableFields::nameAndDescriptionSections(100, 4),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->sortable()->width(60),
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('description')->limit(60)->toggleable(),
                TextColumn::make('animals_count')->counts('animals')->label(__('filament.resources.animal.navigation'))->sortable(),
                TextColumn::make('created_at')->dateTime()->toggleable()->label(__('filament.labels.created_at')),
            ])
            ->actions([EditAction::make(), DeleteAction::make()])
            ->bulkActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAnimalCategories::route('/'),
            'create' => Pages\CreateAnimalCategory::route('/create'),
            'edit' => Pages\EditAnimalCategory::route('/{record}/edit'),
        ];
    }
}
