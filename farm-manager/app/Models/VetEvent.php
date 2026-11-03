<?php

namespace App\Models;

use App\Enums\VetEventType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class VetEvent extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'farm_id',
        'animal_id',
        'vet_id',
        'type',
        'medication_id',
        'dose',
        'event_date',
        'diagnosis',
        'notes',
        'milk_withdrawal_until',
        'meat_withdrawal_until',
        'batch_id',
    ];

    protected function casts(): array
    {
        return [
            'type' => VetEventType::class,
            'event_date' => 'date',
            'milk_withdrawal_until' => 'date',
            'meat_withdrawal_until' => 'date',
        ];
    }

    // Всяка промяна по записа отива в журнала – със старата и новата стойност.
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public function farm(): BelongsTo
    {
        return $this->belongsTo(Farm::class);
    }

    public function animal(): BelongsTo
    {
        return $this->belongsTo(Animal::class);
    }

    public function vet(): BelongsTo
    {
        return $this->belongsTo(User::class, 'vet_id');
    }

    public function medication(): BelongsTo
    {
        return $this->belongsTo(Medication::class);
    }
}
