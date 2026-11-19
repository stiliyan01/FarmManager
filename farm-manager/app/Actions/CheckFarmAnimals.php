<?php

namespace App\Actions;

use App\Enums\AnimalStatus;
use App\Models\Farm;
use App\Models\VaccinationScheme;

// Преглед на активните животни във фермата: кои са под карантина, кои имат
// просрочена ваксинация и кои ваксинации предстоят в следващите 14 дни.
// Ползва се от таблата на собственика, ветеринаря и инспектора и от напомнянията.
class CheckFarmAnimals
{
    public function __construct(private CalculateVaccinationDueDates $calculateVaccinationDueDates) {}

    public function handle(Farm $farm): array
    {
        $animals = $farm->animals()
            ->where('status', AnimalStatus::Active)
            ->with('species', 'vetEvents')
            ->orderBy('ear_tag')
            ->get();

        $mandatorySchemes = VaccinationScheme::where('mandatory', true)->get();

        $quarantined = [];
        $overdue = [];
        $upcoming = [];
        $vaccinationsTotal = 0;
        $vaccinationsOnTime = 0;

        foreach ($animals as $animal) {
            $quarantineUntil = $animal->quarantineUntil();
            if ($quarantineUntil !== null) {
                $quarantined[] = [
                    'id' => $animal->id,
                    'ear_tag' => $animal->ear_tag,
                    'until' => $quarantineUntil->format('d.m.Y'),
                    'days_left' => (int) today()->diffInDays($quarantineUntil),
                ];
            }

            $overdueSchemes = [];

            foreach ($this->calculateVaccinationDueDates->handle($animal, $mandatorySchemes) as $vaccination) {
                $vaccinationsTotal++;

                if ($vaccination['is_overdue']) {
                    $overdueSchemes[] = $vaccination['scheme'];

                    continue;
                }

                $vaccinationsOnTime++;

                if ($vaccination['due_date']->lte(today()->addDays(14))) {
                    $upcoming[] = [
                        'id' => $animal->id,
                        'ear_tag' => $animal->ear_tag,
                        'scheme' => $vaccination['scheme'],
                        'due_date' => $vaccination['due_date'],
                    ];
                }
            }

            if (count($overdueSchemes) > 0) {
                $overdue[] = [
                    'id' => $animal->id,
                    'ear_tag' => $animal->ear_tag,
                    'schemes' => implode(', ', $overdueSchemes),
                ];
            }
        }

        // Първо животните, чиято карантина изтича най-скоро.
        usort($quarantined, fn ($a, $b) => $a['days_left'] <=> $b['days_left']);

        return [
            'animals' => $animals,
            'quarantined' => $quarantined,
            'overdue' => $overdue,
            'upcoming' => $upcoming,
            // Изпълнение на програмата: колко от задължителните ваксинации са в срок.
            'vaccinations_total' => $vaccinationsTotal,
            'vaccinations_on_time' => $vaccinationsOnTime,
        ];
    }
}
