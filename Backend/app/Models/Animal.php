<?php

namespace App\Models;

use App\Models\Concerns\NormalizesTranslatableToArray;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Spatie\Translatable\HasTranslations;

class Animal extends Model
{
    use HasTranslations, NormalizesTranslatableToArray;

    public array $translatable = ['name', 'description'];

    protected $fillable = ['category_id', 'name', 'description'];

    public function category(): BelongsTo
    {
        return $this->belongsTo(AnimalCategory::class, 'category_id');
    }

    public function vaccines(): HasManyThrough
    {
        return $this->hasManyThrough(
            Vaccine::class,
            AnimalCategory::class,
            'id',
            'animal_category_id',
            'category_id',
            'id'
        );
    }

    public function userAnimals(): HasMany
    {
        return $this->hasMany(UserAnimal::class);
    }
}
