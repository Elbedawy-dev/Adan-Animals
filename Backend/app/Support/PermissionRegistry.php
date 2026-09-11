<?php

namespace App\Support;

class PermissionRegistry
{
    /** Returns a flat list of every permission name in the system. */
    public static function all(): array
    {
        return array_values(array_merge(...array_values(static::grouped())));
    }

    /**
     * Returns permissions grouped by module key.
     * The group key is used for the Section label in the Role form
     * and for the filament.permission_groups.{key} translation.
     *
     * @return array<string, list<string>>
     */
    public static function grouped(): array
    {
        return [
            'users' => [
                'view_users',
                'create_users',
                'edit_users',
                'delete_users',
            ],
            'disease_reports' => [
                'view_disease_reports',
                'edit_disease_reports',   // covers approve / reject
                'delete_disease_reports',
            ],
            'animals' => [
                'view_animals',
                'create_animals',
                'edit_animals',
                'delete_animals',
            ],
            'animal_categories' => [
                'view_animal_categories',
                'create_animal_categories',
                'edit_animal_categories',
                'delete_animal_categories',
            ],
            'vaccines' => [
                'view_vaccines',
                'create_vaccines',
                'edit_vaccines',
                'delete_vaccines',
            ],
            'locations' => [
                'view_locations',
                'create_locations',
                'edit_locations',
                'delete_locations',
            ],
            'notifications' => [
                'view_notifications',
                'send_push_notifications',
            ],
        ];
    }
}
