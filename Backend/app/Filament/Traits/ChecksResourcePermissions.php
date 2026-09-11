<?php

namespace App\Filament\Traits;

use Illuminate\Database\Eloquent\Model;

/**
 * Drop this trait on any Filament Resource and set `$permissionPrefix`.
 *
 * Access rules:
 *  • Users with NO Spatie roles        → full access (legacy / super-admin equivalent)
 *  • Users with the `super_admin` role → full access (Gate::before fires first)
 *  • Users with other roles            → limited by their role's permissions
 */
trait ChecksResourcePermissions
{
    protected static function userCanResource(string $action): bool
    {
        $user = auth()->user();
        if (! $user) {
            return false;
        }

        // No Spatie roles assigned yet → treat as super admin (backward compat)
        if ($user->getRoleNames()->isEmpty()) {
            return true;
        }

        // Gate::before in AppServiceProvider already returns true for super_admin / admin,
        // so $user->can() below is effectively a no-op for those roles.
        return $user->can($action . '_' . static::$permissionPrefix);
    }

    public static function canAccess(): bool
    {
        return static::userCanResource('view');
    }

    public static function canCreate(): bool
    {
        return static::userCanResource('create');
    }

    public static function canEdit(Model $record): bool
    {
        return static::userCanResource('edit');
    }

    public static function canDelete(Model $record): bool
    {
        return static::userCanResource('delete');
    }

    public static function canDeleteAny(): bool
    {
        return static::userCanResource('delete');
    }
}
