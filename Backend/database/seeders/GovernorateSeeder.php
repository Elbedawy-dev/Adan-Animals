<?php

namespace Database\Seeders;

use App\Models\Country;
use App\Models\Governorate;
use Database\Seeders\Support\Bilingual;
use Illuminate\Database\Seeder;

class GovernorateSeeder extends Seeder
{
    public function run(): void
    {
        $egypt = Country::where('code', 'EG')->first();
        if (! $egypt) {
            return;
        }

        /** @var array<int, array{en: string, ar: string}> */
        $governorates = [
            Bilingual::map('Cairo', 'القاهرة'),
            Bilingual::map('Giza', 'الجيزة'),
            Bilingual::map('Alexandria', 'الإسكندرية'),
            Bilingual::map('Dakahlia', 'الدقهلية'),
            Bilingual::map('Red Sea', 'البحر الأحمر'),
            Bilingual::map('Beheira', 'البحيرة'),
            Bilingual::map('Fayoum', 'الفيوم'),
            Bilingual::map('Gharbeya', 'الغربية'),
            Bilingual::map('Ismailia', 'الإسماعيلية'),
            Bilingual::map('Menofia', 'المنوفية'),
            Bilingual::map('Minya', 'المنيا'),
            Bilingual::map('Qaliubeya', 'القليوبية'),
            Bilingual::map('New Valley', 'الوادي الجديد'),
            Bilingual::map('Suez', 'السويس'),
            Bilingual::map('Asyut', 'أسيوط'),
            Bilingual::map('South Sinai', 'جنوب سيناء'),
            Bilingual::map('Kafr el-Sheikh', 'كفر الشيخ'),
            Bilingual::map('Matruh', 'مطروح'),
            Bilingual::map('Luxor', 'الأقصر'),
            Bilingual::map('Qena', 'قنا'),
            Bilingual::map('North Sinai', 'شمال سيناء'),
            Bilingual::map('Sohag', 'سوهاج'),
            Bilingual::map('Beni Suef', 'بني سويف'),
            Bilingual::map('Port Said', 'بورسعيد'),
            Bilingual::map('Damietta', 'دمياط'),
            Bilingual::map('Aswan', 'أسوان'),
            Bilingual::map('Sharqia', 'الشرقية'),
        ];

        foreach ($governorates as $names) {
            Governorate::updateOrCreate(
                ['country_id' => $egypt->id, 'name->en' => $names['en']],
                ['country_id' => $egypt->id, 'name' => $names],
            );
        }
    }
}
