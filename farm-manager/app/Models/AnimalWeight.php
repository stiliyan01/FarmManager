<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AnimalWeight extends Model
{
    use HasFactory;

    protected $fillable = ['animal_id', 'weight_kg', 'measured_at'];

    protected function casts(): array
    {
        return [
            'weight_kg' => 'decimal:1',
            'measured_at' => 'date',
        ];
    }

    public function animal(): BelongsTo
    {
        return $this->belongsTo(Animal::class);
    }
}
