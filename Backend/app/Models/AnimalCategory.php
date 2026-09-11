<?php

namespace App\Models;

use App\Models\Concerns\NormalizesTranslatableToArray;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\HasTranslations;

class AnimalCategory extends Model
{
    use HasTranslations, NormalizesTranslatableToArray;

    public array $translatable = ['name', 'description'];

    protected $fillable = ['name', 'description'];

    public function animals(): HasMany
    {
        return $this->hasMany(Animal::class, 'category_id');
    }

    public function vaccines(): HasMany
    {
        return $this->hasMany(Vaccine::class);
    }

    public function diseaseReports(): HasMany
    {
        return $this->hasMany(DiseaseReport::class, 'category_id');
    }
}
