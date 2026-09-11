<?php

namespace App\Models\Concerns;

/**
 * Ensures JSON API responses expose translatable fields as a single string for the current app locale.
 */
trait NormalizesTranslatableToArray
{
    public function toArray(): array
    {
        $array = parent::toArray();

        if (! method_exists($this, 'getTranslatableAttributes')) {
            return $array;
        }

        foreach ($this->getTranslatableAttributes() as $attr) {
            $array[$attr] = $this->getTranslation($attr, app()->getLocale(), true);
        }

        return $array;
    }
}
