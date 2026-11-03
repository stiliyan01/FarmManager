<?php

namespace App\Enums;

enum InspectionResult: string
{
    case Ok = 'ok';
    case Violation = 'violation';

    public function label(): string
    {
        return match ($this) {
            InspectionResult::Ok => 'Без нарушения',
            InspectionResult::Violation => 'Нарушение',
        };
    }
}
