<?php

namespace Database\Seeders;

use App\Models\AnimalCategory;
use Database\Seeders\Support\Bilingual;
use Illuminate\Database\Seeder;

class AnimalCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => Bilingual::map('Cattle', 'أبقار وماشية'),
                'description' => Bilingual::map(
                    'Cows, bulls, and buffalo used for dairy, meat, and labor',
                    'أبقار وثيران وجاموس تُربى للحليب واللحم والعمل.'
                ),
            ],
            [
                'name' => Bilingual::map('Sheep', 'أغنام'),
                'description' => Bilingual::map(
                    'Domestic sheep raised for meat, wool, and dairy',
                    'أغنام مُربّاة للّحم والصوف والحليب.'
                ),
            ],
            [
                'name' => Bilingual::map('Goats', 'ماعز'),
                'description' => Bilingual::map(
                    'Domestic goats raised for meat, milk, and fiber',
                    'ماعز مُربّاة للّحم والحليب والألياف.'
                ),
            ],
            [
                'name' => Bilingual::map('Poultry', 'دواجن'),
                'description' => Bilingual::map(
                    'Chickens, ducks, turkeys, and other domesticated birds',
                    'دجاج وبط ورومي وطيور أخرى.'
                ),
            ],
            [
                'name' => Bilingual::map('Horses', 'خيول'),
                'description' => Bilingual::map(
                    'Horses and donkeys used for work and sport',
                    'خيول وحمير للعمل والرياضة.'
                ),
            ],
            [
                'name' => Bilingual::map('Camels', 'إبل'),
                'description' => Bilingual::map(
                    'Dromedary and Bactrian camels',
                    'إبل مجهودة ومزدوجة السنام.'
                ),
            ],
            [
                'name' => Bilingual::map('Rabbits', 'أرانب'),
                'description' => Bilingual::map(
                    'Domestic rabbits raised for meat and fur',
                    'أرانب مُربّاة للّحم والفراء.'
                ),
            ],
            [
                'name' => Bilingual::map('Fish', 'أسماك'),
                'description' => Bilingual::map(
                    'Farmed fish including tilapia and catfish',
                    'أسماك مُربّاة مثل البلطي والقرموط.'
                ),
            ],
        ];

        foreach ($categories as $cat) {
            AnimalCategory::updateOrCreate(
                ['name->en' => $cat['name']['en']],
                $cat,
            );
        }
    }
}
