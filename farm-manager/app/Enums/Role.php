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

    // За падащото меню във формата за потребител.
    public static function options(): array
    {
        $options = [];

        foreach (Role::cases() as $role) {
            $options[] = ['value' => $role->value, 'label' => $role->label()];
        }

        return $options;
    }

    // За картите с броя потребители по роли.
    public function pluralLabel(): string
    {
        return match ($this) {
            Role::Owner => 'Собственици',
            Role::Vet => 'Ветеринари',
            Role::Registrar => 'Регистратори',
            Role::Inspector => 'Инспектори',
            Role::Admin => 'Администратори',
        };
    }
}
