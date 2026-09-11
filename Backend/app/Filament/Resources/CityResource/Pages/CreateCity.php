<?php

namespace App\Filament\Resources\CityResource\Pages;

use App\Filament\Concerns\InteractsWithTranslatableFilamentRecord;
use App\Filament\Resources\CityResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCity extends CreateRecord
{
    use InteractsWithTranslatableFilamentRecord;

    protected static string $resource = CityResource::class;
}
