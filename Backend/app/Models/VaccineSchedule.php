<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VaccineSchedule extends Model
{
    protected $fillable = [
        'user_animal_id', 'vaccine_id', 'scheduled_date',
        'taken_at', 'status', 'notified_at',
    ];

    protected function casts(): array
    {
        return [
            'scheduled_date' => 'date',
            'taken_at' => 'datetime',
            'notified_at' => 'datetime',
        ];
    }

    public function userAnimal(): BelongsTo
    {
        return $this->belongsTo(UserAnimal::class);
    }

    public function vaccine(): BelongsTo
    {
        return $this->belongsTo(Vaccine::class);
    }

    public function isOverdue(): bool
    {
        return $this->status === 'pending'
            && $this->scheduled_date->isPast();
    }

    // ─── Scopes ───
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeOverdue($query)
    {
        return $query->where('status', 'pending')
            ->where('scheduled_date', '<', now()->toDateString());
    }

    public function scopeUpcoming($query, int $days = 7)
    {
        return $query->where('status', 'pending')
            ->whereBetween('scheduled_date', [
                now()->toDateString(),
                now()->addDays($days)->toDateString(),
            ]);
    }
}
