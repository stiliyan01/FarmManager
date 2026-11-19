<?php

namespace App\Actions;

use App\Models\Region;

// Обобщение по ферми за една област – за контролното табло на инспектора и за отчета по област.
class SummarizeRegion
{
    public function __construct(private CheckFarmAnimals $checkFarmAnimals) {}

    public function handle(Region $region): array
    {
        $farms = $region->farms()
            ->with('owner', 'activeVetAssignment.vet', 'vetEvents.animal', 'inspections')
            ->orderBy('name')
            ->get();

        $summaries = [];

        foreach ($farms as $farm) {
            $check = $this->checkFarmAnimals->handle($farm);

            // Правило 3: записите, въведени повече от 7 дни след мероприятието.
            $lateEvents = [];
            foreach ($farm->vetEvents as $event) {
                if ($event->isLate()) {
                    $lateEvents[] = $event;
                }
            }

            $summaries[] = [
                'farm' => $farm,
                'vet' => $farm->activeVetAssignment?->vet,
                'animals' => $check['animals']->count(),
                'quarantined' => count($check['quarantined']),
                'overdue_animals' => $check['overdue'],
                'vaccinations_total' => $check['vaccinations_total'],
                'vaccinations_on_time' => $check['vaccinations_on_time'],
                'late_events' => $lateEvents,
                'last_inspection' => $farm->inspections->sortByDesc('inspected_at')->first(),
            ];
        }

        return $summaries;
    }
}
