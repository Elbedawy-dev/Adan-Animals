<?php

namespace Database\Seeders;

use App\Models\Country;
use Database\Seeders\Support\Bilingual;
use Illuminate\Database\Seeder;

class CountrySeeder extends Seeder
{
    public function run(): void
    {
        $countries = [
            ['code' => 'EG', 'name' => Bilingual::map('Egypt', 'مصر')],
            ['code' => 'SA', 'name' => Bilingual::map('Saudi Arabia', 'المملكة العربية السعودية')],
            ['code' => 'JO', 'name' => Bilingual::map('Jordan', 'الأردن')],
            ['code' => 'SD', 'name' => Bilingual::map('Sudan', 'السودان')],
        ];

        foreach ($countries as $c) {
            Country::updateOrCreate(
                ['code' => $c['code']],
                ['name' => $c['name']],
            );
        }
    }
}
