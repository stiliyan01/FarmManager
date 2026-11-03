<?php

namespace App\Models;

use App\Enums\AnimalStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

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

    public function isActive(): bool
    {
        return $this->status === AnimalStatus::Active;
    }

    public function sexLabel(): string
    {
        if ($this->sex === 'female') {
            return 'женско';
        }

        return 'мъжко';
    }

    // Възрастта като текст, напр. „3 г. и 2 м.“
    public function ageText(): string
    {
        if ($this->birth_date === null) {
            return '—';
        }

        $allMonths = (int) $this->birth_date->diffInMonths(today());
        $years = intdiv($allMonths, 12);
        $months = $allMonths % 12;

        if ($years === 0) {
            return $months.' м.';
        }

        return $years.' г. и '.$months.' м.';
    }

    // Най-късната дата за мляко от всички записи (може и да е минала).
    public function milkWithdrawalUntil(): ?Carbon
    {
        return $this->vetEvents->max('milk_withdrawal_until');
    }

    // Най-късната дата за месо от всички записи (може и да е минала).
    public function meatWithdrawalUntil(): ?Carbon
    {
        return $this->vetEvents->max('meat_withdrawal_until');
    }

    // Правило 1: до кога животното е под карантина – по-късната от двете дати.
    // Връща null, ако животното не е под карантина.
    public function quarantineUntil(): ?Carbon
    {
        $milkUntil = $this->milkWithdrawalUntil();
        $meatUntil = $this->meatWithdrawalUntil();

        $until = $milkUntil;
        if ($meatUntil !== null && ($until === null || $meatUntil->gt($until))) {
            $until = $meatUntil;
        }

        if ($until === null || $until->lt(today())) {
            return null;
        }

        return $until;
    }

    public function isUnderQuarantine(): bool
    {
        return $this->quarantineUntil() !== null;
    }
}
