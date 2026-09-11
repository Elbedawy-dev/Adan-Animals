<?php

namespace Tests\Unit;

use App\Models\Country;
use App\Support\TranslatableFormData;
use Tests\TestCase;

class TranslatableFormDataTest extends TestCase
{
    public function test_collapse_merges_dotted_keys_into_translation_array(): void
    {
        $data = TranslatableFormData::collapse([
            'code' => 'EG',
            'name.en' => 'Egypt',
            'name.ar' => 'مصر',
        ], ['name']);

        $this->assertSame('EG', $data['code']);
        $this->assertSame(['en' => 'Egypt', 'ar' => 'مصر'], $data['name']);
        $this->assertArrayNotHasKey('name.en', $data);
        $this->assertArrayNotHasKey('name.ar', $data);
    }

    public function test_collapse_skips_when_name_already_locale_array(): void
    {
        $data = TranslatableFormData::collapse([
            'name' => ['en' => 'Egypt', 'ar' => 'مصر'],
        ], ['name']);

        $this->assertSame(['en' => 'Egypt', 'ar' => 'مصر'], $data['name']);
    }

    public function test_expand_for_record_outputs_nested_locale_arrays(): void
    {
        $country = new Country;
        $country->setTranslations('name', ['en' => 'Egypt', 'ar' => 'مصر']);

        $out = TranslatableFormData::expandForRecord($country, ['name']);

        $this->assertSame(['en' => 'Egypt', 'ar' => 'مصر'], $out['name']);
    }
}
