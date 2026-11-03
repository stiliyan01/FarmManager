<?php

namespace App\Models;

use App\Enums\InspectionResult;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Inspection extends Model
{
    use HasFactory;

    protected $fillable = [
        'farm_id',
        'inspector_id',
        'inspected_at',
        'result',
        'findings',
    ];

    protected function casts(): array
    {
        return [
            'result' => InspectionResult::class,
            'inspected_at' => 'date',
        ];
    }

    public function farm(): BelongsTo
    {
        return $this->belongsTo(Farm::class);
    }

    public function inspector(): BelongsTo
    {
        return $this->belongsTo(User::class, 'inspector_id');
    }
}
