<?php

namespace App\Filament\Resources\CountryResource\Pages;

use App\Filament\Concerns\InteractsWithTranslatableFilamentRecord;
use App\Filament\Resources\CountryResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCountry extends CreateRecord
{
    use InteractsWithTranslatableFilamentRecord;

    protected static string $resource = CountryResource::class;
}
