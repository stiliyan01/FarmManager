<?php

namespace App\Policies;

use App\Enums\Role;
use App\Models\Farm;
use App\Models\User;
use App\Models\VetEvent;

class VetEventPolicy
{
    // Записите се виждат от всеки, който вижда фермата – и записите на стари ветеринари.
    public function view(User $user, VetEvent $vetEvent): bool
    {
        return $user->can('view', $vetEvent->farm);
    }

    // Ветеринарни записи прави само ветеринар, назначен към фермата в момента.
    public function create(User $user, Farm $farm): bool
    {
        return $user->role === Role::Vet && $user->isActiveVetFor($farm);
    }

    public function update(User $user, VetEvent $vetEvent): bool
    {
        return $user->role === Role::Vet && $user->isActiveVetFor($vetEvent->farm);
    }

    public function delete(User $user, VetEvent $vetEvent): bool
    {
        return $this->update($user, $vetEvent);
    }
}
