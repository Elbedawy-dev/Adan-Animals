<?php

namespace App\Filament\Resources\AnimalResource\Pages;

use App\Filament\Concerns\InteractsWithTranslatableFilamentRecord;
use App\Filament\Resources\AnimalResource;
use Filament\Resources\Pages\CreateRecord;

class CreateAnimal extends CreateRecord
{
    use InteractsWithTranslatableFilamentRecord;

    protected static string $resource = AnimalResource::class;
}
