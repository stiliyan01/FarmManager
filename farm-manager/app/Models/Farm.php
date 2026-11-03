<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Farm extends Model
{
    use HasFactory;

    protected $fillable = [
        'region_id',
        'reg_number',
        'name',
        'settlement',
        'owner_id',
        'capacity',
        'status',
    ];

    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class);
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function animals(): HasMany
    {
        return $this->hasMany(Animal::class);
    }

    public function vetEvents(): HasMany
    {
        return $this->hasMany(VetEvent::class);
    }

    public function inspections(): HasMany
    {
        return $this->hasMany(Inspection::class);
    }

    public function invitations(): HasMany
    {
        return $this->hasMany(Invitation::class);
    }

    // Цялата история на назначените ветеринари.
    public function vetAssignments(): HasMany
    {
        return $this->hasMany(FarmVet::class);
    }

    // Текущото назначение – това, което още няма крайна дата.
    public function activeVetAssignment(): HasOne
    {
        return $this->hasOne(FarmVet::class)->whereNull('valid_to');
    }
}
