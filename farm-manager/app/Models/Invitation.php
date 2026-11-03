<?php

namespace App\Models;

use App\Enums\Role;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Invitation extends Model
{
    use HasFactory;

    protected $fillable = [
        'farm_id',
        'email',
        'role',
        'token',
        'accepted_at',
        'expires_at',
    ];

    protected function casts(): array
    {
        return [
            'role' => Role::class,
            'accepted_at' => 'datetime',
            'expires_at' => 'datetime',
        ];
    }

    public function farm(): BelongsTo
    {
        return $this->belongsTo(Farm::class);
    }
}
