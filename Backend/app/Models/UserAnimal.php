<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UserAnimal extends Model
{
    protected $fillable = [
        'user_id', 'animal_id', 'nickname',
        'birth_date', 'last_vaccine_date', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'birth_date' => 'date',
            'last_vaccine_date' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function animal(): BelongsTo
    {
        return $this->belongsTo(Animal::class);
    }

    public function vaccineSchedules(): HasMany
    {
        return $this->hasMany(VaccineSchedule::class);
    }

    public function pendingSchedules(): HasMany
    {
        return $this->hasMany(VaccineSchedule::class)
            ->where('status', 'pending')
            ->orderBy('scheduled_date');
    }

    public function overdueSchedules(): HasMany
    {
        return $this->hasMany(VaccineSchedule::class)
            ->where('status', 'pending')
            ->where('scheduled_date', '<', now()->toDateString());
    }
}
