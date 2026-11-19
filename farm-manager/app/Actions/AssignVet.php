<?php

namespace App\Actions;

use App\Models\Farm;
use App\Models\FarmVet;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

// Правило 4: назначаване или смяна на обслужващия ветеринар.
// Текущото назначение не се трие – затваря се с деня преди новото.
// Така старият ветеринар губи достъп, а историята и записите му остават.
class AssignVet
{
    public function handle(Farm $farm, User $vet, Carbon $validFrom, User $assignedBy): FarmVet
    {
        return DB::transaction(function () use ($farm, $vet, $validFrom, $assignedBy) {
            $currentAssignment = $farm->activeVetAssignment;

            if ($currentAssignment !== null) {
                $currentAssignment->update(['valid_to' => $validFrom->copy()->subDay()]);
            }

            return FarmVet::create([
                'farm_id' => $farm->id,
                'vet_id' => $vet->id,
                'assigned_by' => $assignedBy->id,
                'valid_from' => $validFrom,
            ]);
        });
    }
}
