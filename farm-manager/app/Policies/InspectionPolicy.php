<?php

namespace App\Policies;

use App\Enums\Role;
use App\Models\Farm;
use App\Models\Inspection;
use App\Models\User;

class InspectionPolicy
{
    public function view(User $user, Inspection $inspection): bool
    {
        return $user->can('view', $inspection->farm);
    }

    // Проверка прави само инспектор от областта на фермата.
    public function create(User $user, Farm $farm): bool
    {
        return $user->role === Role::Inspector && $farm->region_id === $user->region_id;
    }
}
