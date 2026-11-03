<?php

namespace App\Enums;

enum Role: string
{
    case Owner = 'owner';
    case Vet = 'vet';
    case Registrar = 'registrar';
    case Inspector = 'inspector';
    case Admin = 'admin';

    public function label(): string
    {
        return match ($this) {
            Role::Owner => 'Собственик',
            Role::Vet => 'Ветеринарен лекар',
            Role::Registrar => 'Регистратор (ОДБХ)',
            Role::Inspector => 'Инспектор (ОДБХ)',
            Role::Admin => 'Администратор',
        };
    }
}
