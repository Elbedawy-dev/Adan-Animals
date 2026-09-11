<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FilamentUser, HasMedia, MustVerifyEmail
{
    use HasApiTokens, HasFactory, HasRoles, InteractsWithMedia, Notifiable;

    protected $fillable = [
        'name', 'email', 'phone', 'password',
        'role', 'otp', 'otp_expires_at',
        'region_id', 'latitude', 'longitude',
        'email_verified_at', 'fcm_token',
    ];

    protected $hidden = [
        'password', 'remember_token', 'otp', 'fcm_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'otp_expires_at' => 'datetime',
            'password' => 'hashed',
            'latitude' => 'float',
            'longitude' => 'float',
        ];
    }

    // ─── Notification routing ───
    public function routeNotificationForFcm(): ?string
    {
        return $this->fcm_token ?: null;
    }

    // ─── Filament access control ───
    public function canAccessPanel(Panel $panel): bool
    {
        return $this->role === 'doctor';
    }

    // ─── Helper methods ───
    public function isDoctor(): bool
    {
        return $this->role === 'doctor';
    }

    public function isCustomer(): bool
    {
        return $this->role === 'customer';
    }

    public function isOtpValid(): bool
    {
        return $this->otp !== null && $this->otp_expires_at !== null
            && $this->otp_expires_at->isFuture();
    }

    // ─── Relationships ───
    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class);
    }

    public function userAnimals(): HasMany
    {
        return $this->hasMany(UserAnimal::class);
    }

    public function diseaseReports(): HasMany
    {
        return $this->hasMany(DiseaseReport::class);
    }

    public function reviewedReports(): HasMany
    {
        return $this->hasMany(DiseaseReport::class, 'reviewed_by');
    }
}
