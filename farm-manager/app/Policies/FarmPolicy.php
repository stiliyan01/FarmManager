<?php

namespace App\Policies;

use App\Enums\Role;
use App\Models\Farm;
use App\Models\User;

class FarmPolicy
{
    // Кой може да вижда фермата – основата на правата за животни, записи и проверки.
    public function view(User $user, Farm $farm): bool
    {
        if ($user->role === Role::Admin) {
            return true;
        }

        if ($user->role === Role::Owner) {
            return $farm->owner_id === $user->id;
        }

        // Ветеринарят вижда само фермите, към които е назначен в момента.
        if ($user->role === Role::Vet) {
            return $user->isActiveVetFor($farm);
        }

        // Служителите на ОДБХ виждат фермите в своята област.
        if ($user->role === Role::Registrar || $user->role === Role::Inspector) {
            return $farm->region_id === $user->region_id;
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->role === Role::Registrar || $user->role === Role::Admin;
    }

    public function update(User $user, Farm $farm): bool
    {
        if ($user->role === Role::Admin) {
            return true;
        }

        return $user->role === Role::Registrar && $farm->region_id === $user->region_id;
    }

    // Покана към собственик и назначаване / смяна на ветеринар.
    public function manage(User $user, Farm $farm): bool
    {
        return $this->update($user, $farm);
    }
}
