<?php

namespace App\Filament\Resources\CityResource\Pages;

use App\Filament\Concerns\InteractsWithTranslatableFilamentRecord;
use App\Filament\Resources\CityResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCity extends EditRecord
{
    use InteractsWithTranslatableFilamentRecord;

    protected static string $resource = CityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
