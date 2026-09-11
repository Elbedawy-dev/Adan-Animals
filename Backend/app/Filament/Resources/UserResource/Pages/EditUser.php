<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Pre-populate the virtual Spatie roles field
        $data['spatie_roles'] = $this->record->getRoleNames()->all();

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (empty($data['password'])) {
            unset($data['password']);
        }

        // Remove before Eloquent save (it's not a real column)
        unset($data['spatie_roles']);

        return $data;
    }

    protected function afterSave(): void
    {
        $roles = $this->data['spatie_roles'] ?? [];
        $this->record->syncRoles($roles);
    }

    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }
}
