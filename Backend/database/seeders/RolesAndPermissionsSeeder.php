<?php

namespace Database\Seeders;

use App\Support\PermissionRegistry;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Clear the Spatie permission cache first
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // ── 1. Create every permission ────────────────────────────────────────────
        foreach (PermissionRegistry::all() as $name) {
            Permission::firstOrCreate(['name' => $name, 'guard_name' => 'web']);
        }

        // ── 2. Admin + Super Admin — both get ALL permissions ─────────────────────
        $allPermissions = Permission::where('guard_name', 'web')->get();

        $admin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $admin->syncPermissions($allPermissions);

        $superAdmin = Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
        $superAdmin->syncPermissions($allPermissions);

        // ── 3. Moderator — disease reports + push notifications + read-only users ─
        $moderator = Role::firstOrCreate(['name' => 'moderator', 'guard_name' => 'web']);
        $moderator->syncPermissions([
            'view_users',
            'view_disease_reports',
            'edit_disease_reports',
            'delete_disease_reports',
            'view_notifications',
            'send_push_notifications',
        ]);

        // ── 4. Data manager — catalog & location management ───────────────────────
        $dataManager = Role::firstOrCreate(['name' => 'data_manager', 'guard_name' => 'web']);
        $dataManager->syncPermissions([
            'view_animals',
            'create_animals',
            'edit_animals',
            'delete_animals',
            'view_animal_categories',
            'create_animal_categories',
            'edit_animal_categories',
            'delete_animal_categories',
            'view_vaccines',
            'create_vaccines',
            'edit_vaccines',
            'delete_vaccines',
            'view_locations',
            'create_locations',
            'edit_locations',
            'delete_locations',
        ]);

        $this->command->info('Roles and permissions seeded successfully.');
        $this->command->table(
            ['Role', 'Permissions'],
            [
                [$admin->name, $admin->permissions()->count()],
                [$superAdmin->name, $superAdmin->permissions()->count()],
                [$moderator->name, $moderator->permissions()->count()],
                [$dataManager->name, $dataManager->permissions()->count()],
            ]
        );
    }
}
