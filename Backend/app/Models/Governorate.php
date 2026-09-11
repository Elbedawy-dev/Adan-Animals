<?php

namespace App\Models;

use App\Models\Concerns\NormalizesTranslatableToArray;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\HasTranslations;

class Governorate extends Model
{
    use HasTranslations, NormalizesTranslatableToArray;

    public array $translatable = ['name'];

    protected $fillable = ['country_id', 'name'];

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function cities(): HasMany
    {
        return $this->hasMany(City::class);
    }
}
