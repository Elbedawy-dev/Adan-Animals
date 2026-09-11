<?php

namespace App\Filament\Resources\AnimalCategoryResource\Pages;

use App\Filament\Concerns\InteractsWithTranslatableFilamentRecord;
use App\Filament\Resources\AnimalCategoryResource;
use Filament\Resources\Pages\CreateRecord;

class CreateAnimalCategory extends CreateRecord
{
    use InteractsWithTranslatableFilamentRecord;

    protected static string $resource = AnimalCategoryResource::class;
}
