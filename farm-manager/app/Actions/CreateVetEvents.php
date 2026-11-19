<?php

namespace App\Actions;

use App\Models\Farm;
use App\Models\Medication;
use App\Models\User;
use App\Models\VetEvent;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

// Нов ветеринарен запис за едно или няколко животни.
// Масов запис (няколко животни) = по един ред на животно с общ batch_id.
class CreateVetEvents
{
    public function __construct(private CalculateWithdrawalDates $calculateWithdrawalDates) {}

    public function handle(Farm $farm, User $vet, array $data): int
    {
        $eventDate = Carbon::parse($data['event_date']);
        $medication = Medication::find($data['medication_id'] ?? null);

        // Правило 1: карантинният срок се смята тук, в сървъра.
        $withdrawalDates = $this->calculateWithdrawalDates->handle($medication, $eventDate);

        $batchId = null;
        if (count($data['animal_ids']) > 1) {
            $batchId = (string) Str::uuid();
        }

        DB::transaction(function () use ($farm, $vet, $data, $eventDate, $medication, $withdrawalDates, $batchId) {
            foreach ($data['animal_ids'] as $animalId) {
                VetEvent::create([
                    'farm_id' => $farm->id,
                    'animal_id' => $animalId,
                    'vet_id' => $vet->id,
                    'type' => $data['type'],
                    'medication_id' => $medication?->id,
                    'dose' => $data['dose'] ?? null,
                    'event_date' => $eventDate,
                    'diagnosis' => $data['diagnosis'] ?? null,
                    'notes' => $data['notes'] ?? null,
                    'milk_withdrawal_until' => $withdrawalDates['milk_withdrawal_until'],
                    'meat_withdrawal_until' => $withdrawalDates['meat_withdrawal_until'],
                    'batch_id' => $batchId,
                ]);
            }
        });

        return count($data['animal_ids']);
    }
}
