<?php

namespace Database\Seeders;

use App\Models\Animal;
use App\Models\AnimalCategory;
use Database\Seeders\Support\Bilingual;
use Database\Seeders\Support\SeedArabic;
use Illuminate\Database\Seeder;

class AnimalSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            'Cattle' => ['Holstein Cow', 'Baladi Cow', 'Friesian Cattle', 'Buffalo'],
            'Sheep' => ['Ossimi Sheep', 'Rahmani Sheep', 'Barki Sheep', 'Awassi Sheep'],
            'Goats' => ['Zaraibi Goat', 'Shami Goat', 'Baladi Goat', 'Nubian Goat'],
            'Poultry' => ['Broiler Chicken', 'Layer Chicken', 'Baladi Chicken', 'Duck', 'Turkey'],
            'Horses' => ['Arabian Horse', 'Thoroughbred', 'Donkey'],
            'Camels' => ['Dromedary Camel', 'Racing Camel'],
            'Rabbits' => ['New Zealand White', 'Baladi Rabbit', 'Rex Rabbit'],
            'Fish' => ['Nile Tilapia', 'Catfish', 'Mullet'],
        ];

        foreach ($data as $categoryEn => $animals) {
            $category = AnimalCategory::where('name->en', $categoryEn)->first();
            if (! $category) {
                continue;
            }
            foreach ($animals as $animalName) {
                Animal::updateOrCreate(
                    ['category_id' => $category->id, 'name->en' => $animalName],
                    [
                        'category_id' => $category->id,
                        'name' => Bilingual::map($animalName, SeedArabic::animalName($animalName)),
                    ],
                );
            }
        }
    }
}
