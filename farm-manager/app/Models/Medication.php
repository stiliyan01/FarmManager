<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Medication extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'active_substance',
        'withdrawal_milk_days',
        'withdrawal_meat_days',
    ];

    public function vetEvents(): HasMany
    {
        return $this->hasMany(VetEvent::class);
    }
}
