<?php

namespace App\Filament\Resources\AnimalResource\RelationManagers;

use App\Filament\Forms\TranslatableFields;
use App\Support\TranslatableFormData;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class VaccinesRelationManager extends RelationManager
{
    protected static string $relationship = 'vaccines';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('filament.relation_manager.vaccines_title');
    }

    public function form(Form $form): Form
    {
        return $form->schema([
            Section::make(TranslatableFields::bilingualSectionHeading())
                ->description(__('filament.form.bilingual_section_hint'))
                ->schema([TranslatableFields::nameGrid(100)])
                ->columnSpanFull()
                ->extraAttributes(['dir' => 'ltr']),
            Toggle::make('is_lifetime')
                ->label(__('filament.relation_manager.lifetime_toggle'))
                ->helperText(__('filament.relation_manager.lifetime_helper'))
                ->live()
                ->afterStateUpdated(function ($state, callable $set) {
                    if ($state) {
                        $set('doses_count', 1);
                        $set('interval_days', null);
                    }
                }),
            TextInput::make('doses_count')
                ->numeric()->required()->default(1)->minValue(1)
                ->label(__('filament.labels.number_of_doses'))
                ->disabled(fn (Get $get): bool => (bool) $get('is_lifetime'))
                ->dehydrated(),
            TextInput::make('interval_days')
                ->numeric()->nullable()->minValue(1)
                ->label(__('filament.labels.interval_days'))
                ->helperText(__('filament.relation_manager.interval_helper'))
                ->hidden(fn (Get $get): bool => (bool) $get('is_lifetime'))
                ->dehydrated(fn (Get $get): bool => ! (bool) $get('is_lifetime')),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('doses_count')->label(__('filament.labels.doses'))->sortable(),
                TextColumn::make('interval_days')->label(__('filament.labels.interval_days'))->placeholder('—'),
                IconColumn::make('is_lifetime')->label(__('filament.labels.lifetime'))->boolean(),
            ])
            ->headerActions([
                CreateAction::make()
                    ->mutateFormDataUsing(fn (array $data): array => TranslatableFormData::collapse($data, ['name'])),
            ])
            ->actions([
                EditAction::make()
                    ->fillForm(function (Model $record, Table $table): array {
                        $data = $record->attributesToArray();
                        unset($data['name']);

                        return array_merge($data, TranslatableFormData::expandForRecord($record, ['name']));
                    })
                    ->mutateFormDataUsing(fn (array $data): array => TranslatableFormData::collapse($data, ['name'])),
                DeleteAction::make(),
            ]);
    }
}
