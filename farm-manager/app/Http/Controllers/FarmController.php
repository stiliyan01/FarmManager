<?php

namespace App\Http\Controllers;

use App\Actions\CalculateVaccinationDueDates;
use App\Enums\AnimalStatus;
use App\Models\Animal;
use App\Models\Farm;
use App\Models\VaccinationScheme;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class FarmController extends Controller
{
    // Табло на фермата: обобщение, какво изисква внимание, последни записи и ветеринар.
    public function show(Request $request, Farm $farm, CalculateVaccinationDueDates $calculateVaccinationDueDates)
    {
        Gate::authorize('view', $farm);

        $animals = $farm->animals()
            ->where('status', AnimalStatus::Active)
            ->with('species', 'vetEvents')
            ->orderBy('ear_tag')
            ->get();

        $mandatorySchemes = VaccinationScheme::where('mandatory', true)->get();

        $countBySpecies = [];
        $quarantinedAnimals = [];
        $overdueAnimals = [];
        $upcomingVaccinations = 0;

        foreach ($animals as $animal) {
            $speciesName = $animal->species->name;
            $countBySpecies[$speciesName] = ($countBySpecies[$speciesName] ?? 0) + 1;

            $quarantineUntil = $animal->quarantineUntil();
            if ($quarantineUntil !== null) {
                $quarantinedAnimals[] = [
                    'id' => $animal->id,
                    'ear_tag' => $animal->ear_tag,
                    'until' => $quarantineUntil->format('d.m.Y'),
                    'days_left' => (int) today()->diffInDays($quarantineUntil),
                ];
            }

            $overdueSchemes = [];
            foreach ($calculateVaccinationDueDates->handle($animal, $mandatorySchemes) as $vaccination) {
                if ($vaccination['is_overdue']) {
                    $overdueSchemes[] = $vaccination['scheme'];
                } elseif ($vaccination['due_date']->lte(today()->addDays(14))) {
                    $upcomingVaccinations++;
                }
            }

            if (count($overdueSchemes) > 0) {
                $overdueAnimals[] = [
                    'id' => $animal->id,
                    'ear_tag' => $animal->ear_tag,
                    'schemes' => implode(', ', $overdueSchemes),
                ];
            }
        }

        // Първо животните, чиято карантина изтича най-скоро.
        usort($quarantinedAnimals, fn ($a, $b) => $a['days_left'] <=> $b['days_left']);

        $speciesParts = [];
        foreach ($countBySpecies as $speciesName => $count) {
            $speciesParts[] = $speciesName.' – '.$count;
        }

        return Inertia::render('farms/show', [
            'farm' => [
                'id' => $farm->id,
                'name' => $farm->name,
                'reg_number' => $farm->reg_number,
                'settlement' => $farm->settlement,
                'region' => $farm->region->name,
            ],
            'ownerFarms' => $request->user()->ownedFarms()->orderBy('name')->get(['id', 'name']),
            'stats' => [
                'animals' => $animals->count(),
                'by_species' => implode(' · ', $speciesParts),
                'quarantined' => count($quarantinedAnimals),
                'upcoming_vaccinations' => $upcomingVaccinations,
                'overdue' => count($overdueAnimals),
            ],
            'overdueAnimals' => $overdueAnimals,
            'quarantinedAnimals' => $quarantinedAnimals,
            'recentEvents' => $this->recentEvents($farm),
            'currentVet' => $this->currentVet($farm),
            'canAddAnimal' => $request->user()->can('create', [Animal::class, $farm]),
        ]);
    }

    // Последните записи. Масовият запис (общ batch_id) се показва на един ред с броя животни.
    private function recentEvents(Farm $farm): array
    {
        $events = $farm->vetEvents()
            ->with('animal', 'medication', 'vet')
            ->orderByDesc('event_date')
            ->orderByDesc('id')
            ->limit(300)
            ->get();

        $rows = [];

        foreach ($events as $event) {
            $key = $event->batch_id ?? 'event-'.$event->id;

            if (isset($rows[$key])) {
                $rows[$key]['animal_count']++;

                continue;
            }

            $rows[$key] = [
                'key' => $key,
                'date' => $event->event_date->format('d.m.Y'),
                'animal_id' => $event->animal_id,
                'ear_tag' => $event->animal->ear_tag,
                'animal_count' => 1,
                'type' => $event->type->value,
                'type_label' => $event->type->label(),
                'medication' => $event->medication?->name,
                'vet' => $event->vet->name,
            ];
        }

        return array_slice(array_values($rows), 0, 6);
    }

    private function currentVet(Farm $farm): ?array
    {
        $assignment = $farm->activeVetAssignment()->with('vet')->first();

        if ($assignment === null) {
            return null;
        }

        return [
            'name' => $assignment->vet->name,
            'phone' => $assignment->vet->phone,
            'since' => $assignment->valid_from->format('d.m.Y'),
        ];
    }
}
