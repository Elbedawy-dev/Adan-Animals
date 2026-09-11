<?php

namespace App\Models;

use App\Models\Concerns\NormalizesTranslatableToArray;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\HasTranslations;

class City extends Model
{
    use HasTranslations, NormalizesTranslatableToArray;

    public array $translatable = ['name'];

    protected $fillable = ['governorate_id', 'name'];

    public function governorate(): BelongsTo
    {
        return $this->belongsTo(Governorate::class);
    }

    public function regions(): HasMany
    {
        return $this->hasMany(Region::class);
    }
}
