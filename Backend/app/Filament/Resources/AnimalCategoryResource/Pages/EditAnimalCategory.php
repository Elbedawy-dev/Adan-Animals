<?php

namespace App\Filament\Resources\AnimalCategoryResource\Pages;

use App\Filament\Concerns\InteractsWithTranslatableFilamentRecord;
use App\Filament\Resources\AnimalCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAnimalCategory extends EditRecord
{
    use InteractsWithTranslatableFilamentRecord;

    protected static string $resource = AnimalCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
