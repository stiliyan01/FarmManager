<?php

namespace App\Models;

use App\Enums\Role;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'region_id',
        'phone',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => Role::class,
        ];
    }

    // Областта – само за регистратор и инспектор.
    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class);
    }

    // Фермите, на които потребителят е собственик.
    public function ownedFarms(): HasMany
    {
        return $this->hasMany(Farm::class, 'owner_id');
    }

    // Всички назначения на ветеринаря – и текущите, и старите.
    public function vetAssignments(): HasMany
    {
        return $this->hasMany(FarmVet::class, 'vet_id');
    }

    public function vetEvents(): HasMany
    {
        return $this->hasMany(VetEvent::class, 'vet_id');
    }

    // Дали ветеринарят е назначен към фермата в момента (назначение без крайна дата).
    public function isActiveVetFor(Farm $farm): bool
    {
        return $this->vetAssignments()
            ->where('farm_id', $farm->id)
            ->whereNull('valid_to')
            ->exists();
    }
}
