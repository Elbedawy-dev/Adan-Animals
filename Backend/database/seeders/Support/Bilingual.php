<?php

namespace Database\Seeders\Support;

final class Bilingual
{
    /**
     * @return array{en: string, ar: string}
     */
    public static function map(string $english, ?string $arabic = null): array
    {
        return [
            'en' => $english,
            'ar' => $arabic ?? $english,
        ];
    }
}
