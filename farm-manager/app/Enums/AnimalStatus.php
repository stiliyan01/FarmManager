<?php

namespace App\Enums;

enum AnimalStatus: string
{
    case Active = 'active';
    case Sold = 'sold';
    case Dead = 'dead';

    public function label(): string
    {
        return match ($this) {
            AnimalStatus::Active => 'Активно',
            AnimalStatus::Sold => 'Продадено',
            AnimalStatus::Dead => 'Умряло',
        };
    }
}
