<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Remove the virtual spatie_roles field before Eloquent create
        unset($data['spatie_roles']);

        return $data;
    }

    protected function afterCreate(): void
    {
        $roles = $this->data['spatie_roles'] ?? [];
        $this->record->syncRoles($roles);
    }
}
