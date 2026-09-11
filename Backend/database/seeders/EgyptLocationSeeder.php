<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\Country;
use App\Models\Governorate;
use App\Models\Region;
use Database\Seeders\Support\Bilingual;
use Database\Seeders\Support\SeedArabic;
use Illuminate\Database\Seeder;

/**
 * Seeds cities and regions for every governorate under Egypt (country code EG).
 * Uses firstOrCreate so it is safe to re-run.
 */
class EgyptLocationSeeder extends Seeder
{
    /**
     * Governorate name (must match GovernorateSeeder) => [ cityName => [ region names ] ].
     *
     * @var array<string, array<string, array<int, string>>>
     */
    private function tree(): array
    {
        return [
            'Cairo' => [
                'Nasr City' => ['Nasr City 1', 'Nasr City 2', 'Al Hay Al Ashir'],
                'Heliopolis' => ['Roxy', 'Korba', 'Mirghani'],
                'Maadi' => ['Degla', 'Sarayat', 'Corniche Maadi'],
                'Zamalek' => ['Gezira Center', '26 July Street', 'Mariette'],
                'Shubra' => ['Shubra North', 'Shubra South', 'Rod El Farag'],
                'New Cairo' => ['First District', 'Fifth Settlement', 'Narges'],
            ],
            'Giza' => [
                'Giza City' => ['Dokki Side', 'Faisal Street', 'Haram Street'],
                '6th of October' => ['Industrial Zone', 'Residential A', 'Hosary'],
                'Sheikh Zayed' => ['Zayed 1', 'Zayed 2', 'Green Belt'],
                'Haram' => ['Pyramids Road', 'Nazlet El Samman', 'Remaya'],
                'Dokki' => ['Orman', 'Mesaha', 'Tahrir Extension'],
            ],
            'Alexandria' => [
                'Alexandria City' => ['Mansheya', 'Raml Station', 'Sidi Gaber'],
                'Borg El Arab' => ['New Borg', 'Industrial', 'Residential West'],
                'Amreya' => ['Amreya Center', 'King Mariout', 'North Coast Strip'],
            ],
            'Dakahlia' => [
                'Mansoura' => ['Al Mansurah Center', 'Gesr El Suez', 'Bahr El Saghir'],
                'Mit Ghamr' => ['Mit Ghamr Center', 'Al Barha', 'Samalut Road'],
                'Talkha' => ['Talkha Center', 'Nabarouh Road', 'Belqas Link'],
                'Aga' => ['Aga Center', 'East Villages', 'West Villages'],
                'Belqas' => ['Belqas Center', 'Farmland North', 'Farmland South'],
                'Dikirnis' => ['Dikirnis Center', 'Highway Side', 'Rail Side'],
                'Sinbillawin' => ['Sinbillawin Center', 'Villages Cluster', 'Market Area'],
                'Sherbin' => ['Sherbin Center', 'East Delta', 'Canal Side'],
            ],
            'Red Sea' => [
                'Hurghada' => ['Dahar', 'Sakala', 'El Gouna Road'],
                'Safaga' => ['Port Area', 'Residential', 'Industrial'],
                'El Qoseir' => ['Old Town', 'New Corniche', 'South Villages'],
            ],
            'Beheira' => [
                'Damanhour' => ['City Center', 'Railway', 'University Area'],
                'Kafr El Dawwar' => ['Textile Zone', 'Residential', 'Canal'],
                'Rashid' => ['Historic Core', 'Port Quarter', 'Delta Edge'],
                'Idku' => ['Lake Side', 'Main Street', 'Farms'],
            ],
            'Fayoum' => [
                'Fayoum City' => ['Sinai', 'Lake Quarter', 'University'],
                'Ibshaway' => ['Ibshaway Center', 'Canal Villages', 'Desert Edge'],
                'Tamiya' => ['Tamiya Center', 'Oasis Road', 'Agricultural'],
                'Sinnuris' => ['Sinnuris Center', 'North Fields', 'South Fields'],
            ],
            'Gharbeya' => [
                'Tanta' => ['Railway', 'University', 'Market'],
                'El Mahalla El Kubra' => ['Spinning District', 'Residential', 'Industrial'],
                'Zefta' => ['Zefta Center', 'Canal East', 'Canal West'],
                'Samannoud' => ['Samannoud Center', 'Villages', 'Highway'],
            ],
            'Ismailia' => [
                'Ismailia City' => ['Ferry Area', 'Third District', 'Lake View'],
                'Fayed' => ['Fayed Center', 'Border Zone', 'Farms'],
                'Qantara West' => ['Canal Bridge', 'Villages', 'Desert Road'],
            ],
            'Menofia' => [
                'Shibin El Kom' => ['University', 'Hospital Area', 'Old Market'],
                'Menouf' => ['Menouf Center', 'Rail Villages', 'Canal'],
                'Ashmoun' => ['Ashmoun Center', 'Ashmoun Bridge', 'Fields'],
                'Sadat City' => ['Industrial City', 'Residential', 'Logistics'],
            ],
            'Minya' => [
                'Minya City' => ['Palace Area', 'Corniche', 'New Minya'],
                'Mallawi' => ['Mallawi Center', 'Upper Egypt Road', 'Villages'],
                'Beni Mazar' => ['Beni Mazar Center', 'Canal', 'Desert'],
                'Maghagha' => ['Maghagha Center', 'Agricultural', 'Nile Side'],
            ],
            'Qaliubeya' => [
                'Banha' => ['Banha Center', 'University', 'Ring Road'],
                'Qalyub' => ['Qalyub Center', 'Canal', 'Industrial'],
                'Khanka' => ['Khanka Center', 'Desert Extension', 'Villages'],
                'Shubra El Kheima' => ['Shubra Kheima 1', 'Shubra Kheima 2', 'Factories'],
            ],
            'New Valley' => [
                'Kharga' => ['Kharga Center', 'Oasis Ring', 'Desert Road'],
                'Mut' => ['Mut Center', 'Dakhla Link', 'Farms'],
                'Dakhla' => ['Dakhla Town', 'Hot Springs', 'Agricultural'],
            ],
            'Suez' => [
                'Suez City' => ['Arbaeen', 'Faisal', 'Port Tawfiq'],
                'Arbaeen' => ['Arbaeen Center', 'Industrial', 'Residential'],
                'Faisal' => ['Faisal Center', 'Canal View', 'Logistics'],
                'Ganayen' => ['Ganayen Center', 'Petrochemical', 'Workers Housing'],
            ],
            'Asyut' => [
                'Asyut City' => ['University', 'Corniche', 'Third District'],
                'Dayrout' => ['Dayrout Center', 'Canal', 'Villages'],
                'Abnoub' => ['Abnoub Center', 'Highway', 'Fields'],
                'Manfalut' => ['Manfalut Center', 'Rail', 'Agricultural'],
            ],
            'South Sinai' => [
                'Sharm El Sheikh' => ['Naama Bay', 'Hadaba', 'Nabq'],
                'Dahab' => ['Masbat', 'Assalah', 'Blue Hole Road'],
                'Nuweiba' => ['Port', 'Colored Canyon Road', 'Beach'],
                'Saint Catherine' => ['Monastery Area', 'Mountain Trail', 'Bedouin'],
            ],
            'Kafr el-Sheikh' => [
                'Kafr El Sheikh City' => ['Center', 'University', 'Lake Side'],
                'Desouk' => ['Desouk Center', 'Market', 'Canal'],
                'Baltim' => ['Baltim Beach', 'Center', 'Lagoon'],
                'Sidi Salem' => ['Sidi Salem Center', 'Rice Fields', 'Road'],
            ],
            'Matruh' => [
                'Marsa Matruh' => ['Corniche', 'Cleopatra', 'Agiba'],
                'El Alamein' => ['North Coast', 'Memorial', 'New Marina'],
                'Siwa' => ['Shali', 'Fatnas', 'Desert Springs'],
                'El Negaila' => ['Negaila Center', 'Coast', 'Farms'],
            ],
            'Luxor' => [
                'Luxor City' => ['East Bank', 'West Bank Ferry', 'Karnak'],
                'Esna' => ['Esna Lock', 'Town Center', 'Canal'],
                'Armant' => ['Armant Center', 'Sugar Factory', 'Fields'],
                'El Tod' => ['El Tod Center', 'Desert Edge', 'Villages'],
            ],
            'Qena' => [
                'Qena City' => ['Railway', 'Corniche', 'Industrial'],
                'Nag Hammadi' => ['Nag Hammadi Center', 'Sugar', 'Bridge'],
                'Dishna' => ['Dishna Center', 'Upper Villages', 'Canal'],
                'Farshut' => ['Farshut Center', 'Road', 'Agricultural'],
            ],
            'North Sinai' => [
                'Arish' => ['Arish Center', 'Coastal', 'University'],
                'Sheikh Zuweid' => ['Sheikh Zuweid Center', 'Border Villages', 'Road'],
                'Rafah' => ['Rafah Crossing Area', 'Center', 'East'],
                'Bir El Abd' => ['Bir El Abd Center', 'Desert', 'Oasis'],
            ],
            'Sohag' => [
                'Sohag City' => ['University', 'Corniche', 'Third District'],
                'Akhmim' => ['Akhmim Center', 'Antiquities', 'Nile'],
                'Girga' => ['Girga Center', 'Canal', 'Fields'],
                'Tahta' => ['Tahta Center', 'Rail', 'Villages'],
            ],
            'Beni Suef' => [
                'Beni Suef City' => ['University', 'Corniche', 'Industrial'],
                'Nasser' => ['Nasser Center', 'Canal', 'Farms'],
                'Ihnasiya' => ['Ihnasiya Center', 'Historic', 'Agricultural'],
                'El Wasta' => ['El Wasta Center', 'Desert Road', 'Villages'],
            ],
            'Port Said' => [
                'Port Said City' => ['Arab District', 'Port Area', 'Gardens'],
                'Port Fouad' => ['Port Fouad Center', 'Corniche', 'Residential'],
                'Arab Quarter' => ['Market', 'Fishing Port', 'Schools'],
            ],
            'Damietta' => [
                'Damietta City' => ['Corniche', 'New Damietta', 'Furniture'],
                'Ras El Bar' => ['Beach', 'Center', 'Lagoon'],
                'Faraskour' => ['Faraskour Center', 'Canal', 'Fields'],
                'Kafr Saad' => ['Kafr Saad Center', 'Industry', 'Road'],
            ],
            'Aswan' => [
                'Aswan City' => ['Elephantine', 'Corniche', 'High Dam'],
                'Kom Ombo' => ['Temple Area', 'Sugar', 'Nile'],
                'Edfu' => ['Edfu Temple', 'Town Center', 'Canal'],
                'Daraw' => ['Daraw Center', 'Camel Market', 'Villages'],
            ],
            'Sharqia' => [
                'Zagazig' => ['University', 'Railway', 'Tenth'],
                'Belbeis' => ['Belbeis Center', 'Desert Road', 'Canal'],
                'Minya El Qamh' => ['Minya Qamh Center', 'Villages', 'Highway'],
                'Abu Hammad' => ['Abu Hammad Center', 'Market', 'Fields'],
            ],
        ];
    }

    public function run(): void
    {
        $egypt = Country::where('code', 'EG')->first();
        if (! $egypt) {
            $this->command?->warn('Egypt (EG) not found — run CountrySeeder first.');

            return;
        }

        $tree = $this->tree();

        foreach ($egypt->governorates()->orderBy('id')->get() as $gov) {
            $govKey = $gov->getTranslation('name', 'en');
            $legacyBlock = $tree[$govKey] ?? $this->fallbackCityBlock($govKey);

            foreach ($this->legacyBlockToRows($legacyBlock) as $row) {
                $city = City::updateOrCreate(
                    ['governorate_id' => $gov->id, 'name->en' => $row['city']['en']],
                    ['governorate_id' => $gov->id, 'name' => $row['city']],
                );

                foreach ($row['regions'] as $regionNames) {
                    Region::updateOrCreate(
                        ['city_id' => $city->id, 'name->en' => $regionNames['en']],
                        ['city_id' => $city->id, 'name' => $regionNames],
                    );
                }
            }
        }

        $this->command?->info('Egypt locations seeded: '.City::whereHas('governorate.country', fn ($q) => $q->where('code', 'EG'))->count().' cities, '.
            Region::whereHas('city.governorate.country', fn ($q) => $q->where('code', 'EG'))->count().' regions.');
    }

    /**
     * @return array<string, array<int, string>>
     */
    private function fallbackCityBlock(string $governorateName): array
    {
        $main = $governorateName.' — Main';

        return [
            $main.' City' => ['Center', 'North district', 'South district'],
            $main.' — East' => ['East center', 'Industrial', 'Residential'],
        ];
    }

    /**
     * @param  array<string, array<int, string>>  $legacy
     * @return array<int, array{city: array{en: string, ar: string}, regions: array<int, array{en: string, ar: string}>}>
     */
    private function legacyBlockToRows(array $legacy): array
    {
        $rows = [];
        foreach ($legacy as $cityKey => $regionStrings) {
            $rows[] = [
                'city' => Bilingual::map($cityKey, SeedArabic::location($cityKey)),
                'regions' => array_map(
                    static fn (string $label) => Bilingual::map($label, SeedArabic::location($label)),
                    $regionStrings,
                ),
            ];
        }

        return $rows;
    }
}
