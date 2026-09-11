<?php

namespace App\Filament\Resources\VaccineResource\Pages;

use App\Filament\Concerns\InteractsWithTranslatableFilamentRecord;
use App\Filament\Resources\VaccineResource;
use Filament\Resources\Pages\CreateRecord;

class CreateVaccine extends CreateRecord
{
    use InteractsWithTranslatableFilamentRecord;

    protected static string $resource = VaccineResource::class;
}
