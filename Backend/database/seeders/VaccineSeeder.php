<?php

namespace Database\Seeders;

use App\Models\AnimalCategory;
use App\Models\Vaccine;
use Database\Seeders\Support\Bilingual;
use Database\Seeders\Support\SeedArabic;
use Illuminate\Database\Seeder;

class VaccineSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            'Cattle' => [
                ['name' => 'FMD (Foot & Mouth)', 'doses_count' => 2, 'interval_days' => 180, 'is_lifetime' => false],
                ['name' => 'Brucellosis', 'doses_count' => 1, 'interval_days' => null, 'is_lifetime' => true],
                ['name' => 'Anthrax', 'doses_count' => 1, 'interval_days' => 365, 'is_lifetime' => false],
                ['name' => 'LSD (Lumpy Skin Disease)', 'doses_count' => 1, 'interval_days' => 365, 'is_lifetime' => false],
                ['name' => 'Blackleg', 'doses_count' => 2, 'interval_days' => 30, 'is_lifetime' => false],
            ],
            'Sheep' => [
                ['name' => 'FMD (Foot & Mouth)', 'doses_count' => 2, 'interval_days' => 180, 'is_lifetime' => false],
                ['name' => 'Sheep Pox', 'doses_count' => 1, 'interval_days' => 365, 'is_lifetime' => false],
                ['name' => 'Enterotoxemia', 'doses_count' => 2, 'interval_days' => 365, 'is_lifetime' => false],
                ['name' => 'Brucellosis', 'doses_count' => 1, 'interval_days' => null, 'is_lifetime' => true],
            ],
            'Goats' => [
                ['name' => 'FMD (Foot & Mouth)', 'doses_count' => 2, 'interval_days' => 180, 'is_lifetime' => false],
                ['name' => 'Goat Pox', 'doses_count' => 1, 'interval_days' => 365, 'is_lifetime' => false],
                ['name' => 'Enterotoxemia', 'doses_count' => 2, 'interval_days' => 365, 'is_lifetime' => false],
            ],
            'Poultry' => [
                ['name' => 'Newcastle Disease (NDV)', 'doses_count' => 3, 'interval_days' => 30, 'is_lifetime' => false],
                ['name' => 'Avian Influenza (H5N1)', 'doses_count' => 2, 'interval_days' => 180, 'is_lifetime' => false],
                ['name' => 'Gumboro (IBD)', 'doses_count' => 2, 'interval_days' => 21, 'is_lifetime' => false],
                ['name' => "Marek's Disease", 'doses_count' => 1, 'interval_days' => null, 'is_lifetime' => true],
                ['name' => 'Infectious Bronchitis', 'doses_count' => 2, 'interval_days' => 90, 'is_lifetime' => false],
            ],
        ];

        foreach ($data as $categoryEn => $vaccines) {
            $category = AnimalCategory::where('name->en', $categoryEn)->first();
            if (! $category) {
                continue;
            }
            foreach ($vaccines as $v) {
                $name = Bilingual::map($v['name'], SeedArabic::vaccineName($v['name']));
                unset($v['name']);
                Vaccine::updateOrCreate(
                    ['animal_category_id' => $category->id, 'name->en' => $name['en']],
                    array_merge($v, ['animal_category_id' => $category->id, 'name' => $name]),
                );
            }
        }
    }
}
