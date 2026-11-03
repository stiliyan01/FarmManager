<?php

namespace App\Models;

use App\Enums\AnimalStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Animal extends Model
{
    use HasFactory;

    protected $fillable = [
        'farm_id',
        'ear_tag',
        'species_id',
        'breed_id',
        'sex',
        'birth_date',
        'mother_id',
        'group_name',
        'status',
        'status_changed_at',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'status' => AnimalStatus::class,
            'birth_date' => 'date',
            'status_changed_at' => 'datetime',
        ];
    }

    public function farm(): BelongsTo
    {
        return $this->belongsTo(Farm::class);
    }

    public function species(): BelongsTo
    {
        return $this->belongsTo(Species::class);
    }

    public function breed(): BelongsTo
    {
        return $this->belongsTo(Breed::class);
    }

    public function mother(): BelongsTo
    {
        return $this->belongsTo(Animal::class, 'mother_id');
    }

    // Малките на това животно (когато то е майка).
    public function offspring(): HasMany
    {
        return $this->hasMany(Animal::class, 'mother_id');
    }

    public function weights(): HasMany
    {
        return $this->hasMany(AnimalWeight::class);
    }

    public function vetEvents(): HasMany
    {
        return $this->hasMany(VetEvent::class);
    }
}
