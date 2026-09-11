<?php

namespace App\Filament\Resources\GovernorateResource\Pages;

use App\Filament\Concerns\InteractsWithTranslatableFilamentRecord;
use App\Filament\Resources\GovernorateResource;
use Filament\Resources\Pages\CreateRecord;

class CreateGovernorate extends CreateRecord
{
    use InteractsWithTranslatableFilamentRecord;

    protected static string $resource = GovernorateResource::class;
}
