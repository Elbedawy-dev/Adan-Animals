<?php

namespace App\Support;

use Illuminate\Database\Eloquent\Model;

final class TranslatableFormData
{
    /**
     * @return list<string>
     */
    public static function locales(): array
    {
        return array_values(config('localization.supported_locales', ['en', 'ar']));
    }

    /**
     * Flatten name.en / name.ar style keys into Spatie translation arrays.
     *
     * @param  list<string>  $attributes
     * @return array<string, mixed>
     */
    public static function collapse(array $data, array $attributes): array
    {
        $locales = self::locales();

        foreach ($attributes as $attr) {
            if (isset($data[$attr]) && is_array($data[$attr]) && self::isLocaleKeyedArray($data[$attr], $locales)) {
                continue;
            }

            $bucket = [];
            foreach ($locales as $loc) {
                $dotKey = "{$attr}.{$loc}";
                if (array_key_exists($dotKey, $data)) {
                    $bucket[$loc] = $data[$dotKey];
                    unset($data[$dotKey]);
                }
            }

            if ($bucket !== []) {
                $data[$attr] = $bucket;
            }
        }

        return $data;
    }

    /**
     * Build form fill state for Spatie translatable attributes.
     *
     * Filament resolves `TextInput::make('name.en')` to `data.name.en` (nested keys), not a literal `name.en` key.
     *
     * @param  list<string>  $attributes
     * @return array<string, mixed>
     */
    public static function expandForRecord(Model $record, array $attributes): array
    {
        if (! method_exists($record, 'getTranslations')) {
            return [];
        }

        $locales = self::locales();
        $out = [];

        foreach ($attributes as $attr) {
            if (! in_array($attr, $record->getTranslatableAttributes(), true)) {
                continue;
            }

            $translations = $record->getTranslations($attr);
            $nested = [];

            foreach ($locales as $loc) {
                $nested[$loc] = $translations[$loc] ?? '';
            }

            $out[$attr] = $nested;
        }

        return $out;
    }

    /**
     * @param  list<string>  $locales
     */
    private static function isLocaleKeyedArray(mixed $value, array $locales): bool
    {
        if (! is_array($value)) {
            return false;
        }

        foreach ($locales as $loc) {
            if (! array_key_exists($loc, $value)) {
                return false;
            }
        }

        return true;
    }
}
