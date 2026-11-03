<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VaccinationScheme extends Model
{
    use HasFactory;

    protected $fillable = [
        'species_id',
        'name',
        'medication_id',
        'interval_days',
        'mandatory',
    ];

    protected function casts(): array
    {
        return [
            'mandatory' => 'boolean',
        ];
    }

    public function species(): BelongsTo
    {
        return $this->belongsTo(Species::class);
    }

    public function medication(): BelongsTo
    {
        return $this->belongsTo(Medication::class);
    }
}
