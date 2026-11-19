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

    // Ветеринарят коригира само своите записи и само докато е назначен към фермата.
    public function update(User $user, VetEvent $vetEvent): bool
    {
        if ($user->role !== Role::Vet || $vetEvent->vet_id !== $user->id) {
            return false;
        }

        return $user->isActiveVetFor($vetEvent->farm);
    }

    public function delete(User $user, VetEvent $vetEvent): bool
    {
        return $this->update($user, $vetEvent);
    }
}
