<?php

namespace App\Filament\Resources\CountryResource\Pages;

use App\Filament\Concerns\InteractsWithTranslatableFilamentRecord;
use App\Filament\Resources\CountryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCountry extends EditRecord
{
    use InteractsWithTranslatableFilamentRecord;

    protected static string $resource = CountryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
