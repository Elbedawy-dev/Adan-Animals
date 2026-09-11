<?php

namespace App\Filament\Resources\RoleResource\Pages;

use App\Filament\Resources\RoleResource;
use App\Support\PermissionRegistry;
use Filament\Resources\Pages\CreateRecord;

class CreateRole extends CreateRecord
{
    protected static string $resource = RoleResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Ensure guard_name is always set to web for dashboard roles
        $data['guard_name'] = 'web';

        // Remove the perm_* keys — we handle them in afterCreate
        foreach (array_keys(PermissionRegistry::grouped()) as $group) {
            unset($data["perm_{$group}"]);
        }

        return $data;
    }

    protected function afterCreate(): void
    {
        $this->syncPermissions();
    }

    private function syncPermissions(): void
    {
        $selected = [];

        foreach (array_keys(PermissionRegistry::grouped()) as $group) {
            $selected = array_merge($selected, $this->data["perm_{$group}"] ?? []);
        }

        $this->record->syncPermissions($selected);
    }
}
