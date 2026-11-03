<?php

namespace App\Enums;

enum VetEventType: string
{
    case Vaccination = 'vaccination';
    case Treatment = 'treatment';
    case Exam = 'exam';
    case Test = 'test';

    public function label(): string
    {
        return match ($this) {
            VetEventType::Vaccination => 'Ваксинация',
            VetEventType::Treatment => 'Лечение',
            VetEventType::Exam => 'Преглед',
            VetEventType::Test => 'Изследване',
        };
    }
}
