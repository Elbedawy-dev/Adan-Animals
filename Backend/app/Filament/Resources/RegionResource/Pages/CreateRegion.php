<?php

namespace App\Filament\Resources\RegionResource\Pages;

use App\Filament\Concerns\InteractsWithTranslatableFilamentRecord;
use App\Filament\Resources\RegionResource;
use Filament\Resources\Pages\CreateRecord;

class CreateRegion extends CreateRecord
{
    use InteractsWithTranslatableFilamentRecord;

    protected static string $resource = RegionResource::class;
}
