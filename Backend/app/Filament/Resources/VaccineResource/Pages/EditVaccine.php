<?php

namespace App\Filament\Resources\VaccineResource\Pages;

use App\Filament\Concerns\InteractsWithTranslatableFilamentRecord;
use App\Filament\Resources\VaccineResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditVaccine extends EditRecord
{
    use InteractsWithTranslatableFilamentRecord;

    protected static string $resource = VaccineResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
