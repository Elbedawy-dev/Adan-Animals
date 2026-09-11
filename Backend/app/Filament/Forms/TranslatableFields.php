<?php

namespace App\Filament\Forms;

use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;

final class TranslatableFields
{
    public static function bilingualSectionHeading(): string
    {
        return __('filament.form.bilingual_section');
    }

    public static function nameGrid(int $maxLength = 100): Grid
    {
        return Grid::make(2)->schema([
            TextInput::make('name.en')
                ->label(__('filament.form.name_en'))
                ->maxLength($maxLength)
                ->required(),
            TextInput::make('name.ar')
                ->label(__('filament.form.name_ar'))
                ->maxLength($maxLength)
                ->required(),
        ]);
    }

    public static function descriptionGrid(int $rows = 4): Grid
    {
        return Grid::make(2)->schema([
            Textarea::make('description.en')
                ->label(__('filament.form.description_en'))
                ->rows($rows)
                ->nullable(),
            Textarea::make('description.ar')
                ->label(__('filament.form.description_ar'))
                ->rows($rows)
                ->nullable(),
        ]);
    }

    /**
     * @return array<int, Section>
     */
    public static function nameSection(int $maxLength = 100): array
    {
        return [
            Section::make(self::bilingualSectionHeading())
                ->description(__('filament.form.bilingual_section_hint'))
                ->schema([self::nameGrid($maxLength)])
                ->columns(1)
                ->extraAttributes(['dir' => 'ltr']),
        ];
    }

    /**
     * @return array<int, Section>
     */
    public static function nameAndDescriptionSections(int $nameMax = 100, int $descriptionRows = 4): array
    {
        return [
            Section::make(self::bilingualSectionHeading())
                ->description(__('filament.form.bilingual_section_hint'))
                ->schema([
                    self::nameGrid($nameMax),
                    self::descriptionGrid($descriptionRows),
                ])
                ->columns(1)
                ->extraAttributes(['dir' => 'ltr']),
        ];
    }
}
