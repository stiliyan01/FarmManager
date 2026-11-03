<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Breed extends Model
{
    use HasFactory;

    protected $fillable = ['species_id', 'name'];

    public function species(): BelongsTo
    {
        return $this->belongsTo(Species::class);
    }
}
