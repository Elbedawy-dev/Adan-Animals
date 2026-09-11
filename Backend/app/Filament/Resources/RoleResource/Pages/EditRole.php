<?php

namespace App\Filament\Resources\RoleResource\Pages;

use App\Filament\Resources\RoleResource;
use App\Support\PermissionRegistry;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRole extends EditRecord
{
    protected static string $resource = RoleResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }

    /** Pre-fill each per-group CheckboxList with the role's current permissions. */
    protected function mutateFormDataBeforeFill(array $data): array
    {
        $current = $this->record->permissions->pluck('name')->all();

        foreach (PermissionRegistry::grouped() as $group => $perms) {
            $data["perm_{$group}"] = array_values(array_intersect($current, $perms));
        }

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['guard_name'] = 'web';

        foreach (array_keys(PermissionRegistry::grouped()) as $group) {
            unset($data["perm_{$group}"]);
        }

        return $data;
    }

    protected function afterSave(): void
    {
        $selected = [];

        foreach (array_keys(PermissionRegistry::grouped()) as $group) {
            $selected = array_merge($selected, $this->data["perm_{$group}"] ?? []);
        }

        $this->record->syncPermissions($selected);
    }
}
