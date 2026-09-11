<?php

namespace Database\Seeders;

use App\Models\Region;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $region = Region::first();

        $admin = User::firstOrCreate(
            ['email' => 'admin@adan.com'],
            [
                'name'               => 'Dr. Admin — د. المشرف',
                'phone'              => '+201000000000',
                'password'           => 'password',
                'role'               => 'doctor',
                'email_verified_at'  => now(),
                'region_id'          => $region?->id,
            ]
        );

        // Assign the admin Spatie role so this account has full dashboard access
        $admin->assignRole('admin');

        User::firstOrCreate(
            ['email' => 'doctor@adan.com'],
            [
                'name'               => 'Dr. Ahmed Hassan — د. أحمد حسن',
                'phone'              => '+201001234567',
                'password'           => 'password',
                'role'               => 'doctor',
                'email_verified_at'  => now(),
                'region_id'          => $region?->id,
            ]
        );

        User::firstOrCreate(
            ['email' => 'customer@adan.com'],
            [
                'name'               => 'Mohammed Fathy — محمد فتحي',
                'phone'              => '+201112345678',
                'password'           => 'password',
                'role'               => 'customer',
                'email_verified_at'  => now(),
                'region_id'          => $region?->id,
                'latitude'           => 31.0409,
                'longitude'          => 31.3785,
            ]
        );
    }
}
