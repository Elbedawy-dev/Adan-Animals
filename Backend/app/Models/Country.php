<?php

namespace App\Models;

use App\Models\Concerns\NormalizesTranslatableToArray;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\HasTranslations;

class Country extends Model
{
    use HasTranslations, NormalizesTranslatableToArray;

    public array $translatable = ['name'];

    protected $fillable = ['name', 'code'];

    public function governorates(): HasMany
    {
        return $this->hasMany(Governorate::class);
    }
}
