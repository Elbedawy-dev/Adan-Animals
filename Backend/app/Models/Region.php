<?php

namespace App\Models;

use App\Models\Concerns\NormalizesTranslatableToArray;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\HasTranslations;

class Region extends Model
{
    use HasTranslations, NormalizesTranslatableToArray;

    public array $translatable = ['name'];

    protected $fillable = ['city_id', 'name'];

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function diseaseReports(): HasMany
    {
        return $this->hasMany(DiseaseReport::class);
    }
}
