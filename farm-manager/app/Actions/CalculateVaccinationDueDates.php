<?php

namespace App\Actions;

use App\Enums\VetEventType;
use App\Models\Animal;
use Illuminate\Support\Collection;

// Правило 2: кога е следващата задължителна ваксинация на животното.
// Следваща = последната ваксинация + интервала от схемата. Ако животното никога
// не е ваксинирано с този препарат, ваксинацията се води просрочена.
class CalculateVaccinationDueDates
{
    public function handle(Animal $animal, Collection $mandatorySchemes): array
    {
        $result = [];

        foreach ($mandatorySchemes as $scheme) {
            if ($scheme->species_id !== $animal->species_id) {
                continue;
            }

            $lastVaccination = $animal->vetEvents
                ->where('type', VetEventType::Vaccination)
                ->where('medication_id', $scheme->medication_id)
                ->sortByDesc('event_date')
                ->first();

            $lastDate = null;
            $dueDate = null;
            $isOverdue = true;

            if ($lastVaccination !== null) {
                $lastDate = $lastVaccination->event_date;
                $dueDate = $lastDate->copy()->addDays($scheme->interval_days);
                $isOverdue = $dueDate->lt(today());
            }

            $result[] = [
                'scheme' => $scheme->name,
                'last_date' => $lastDate,
                'due_date' => $dueDate,
                'is_overdue' => $isOverdue,
            ];
        }

        return $result;
    }
}
