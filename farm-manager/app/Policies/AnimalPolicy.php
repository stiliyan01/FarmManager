<?php

namespace App\Policies;

use App\Enums\Role;
use App\Models\Animal;
use App\Models\Farm;
use App\Models\User;

class AnimalPolicy
{
    // Животното се вижда от всеки, който вижда фермата му.
    public function view(User $user, Animal $animal): bool
    {
        return $user->can('view', $animal->farm);
    }

    // Животни добавя само собственикът на фермата.
    public function create(User $user, Farm $farm): bool
    {
        return $user->role === Role::Owner && $farm->owner_id === $user->id;
    }

    // Редакция и смяна на статус (продадено / умряло) – пак само собственикът.
    public function update(User $user, Animal $animal): bool
    {
        return $user->role === Role::Owner && $animal->farm->owner_id === $user->id;
    }
}
