<?php

namespace App\Http\Controllers;

use App\Actions\CalculateVaccinationDueDates;
use App\Enums\AnimalStatus;
use App\Http\Requests\StoreAnimalRequest;
use App\Models\Animal;
use App\Models\Farm;
use App\Models\Species;
use App\Models\VaccinationScheme;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class AnimalController extends Controller
{
    public function index(Request $request, Farm $farm)
    {
        Gate::authorize('view', $farm);

        $animals = $farm->animals()
            ->with('species', 'breed', 'vetEvents')
            ->orderBy('ear_tag')
            ->get();

        $rows = [];
        foreach ($animals as $animal) {
            $rows[] = [
                'id' => $animal->id,
                'ear_tag' => $animal->ear_tag,
                'species' => $animal->species->name,
                'breed' => $animal->breed?->name,
                'sex_label' => $animal->sexLabel(),
                'age' => $animal->ageText(),
                'group_name' => $animal->group_name,
                'status' => $animal->status->value,
                'status_label' => $animal->status->label(),
                'quarantine_until' => $animal->quarantineUntil()?->format('d.m.Y'),
            ];
        }

        return Inertia::render('animals/index', [
            'farm' => $farm->only('id', 'name', 'reg_number'),
            'animals' => $rows,
            'canAddAnimal' => $request->user()->can('create', [Animal::class, $farm]),
        ]);
    }

    public function create(Request $request, Farm $farm)
    {
        Gate::authorize('create', [Animal::class, $farm]);

        // Възможни майки – активните женски животни във фермата.
        $mothers = $farm->animals()
            ->where('sex', 'female')
            ->where('status', AnimalStatus::Active)
            ->orderBy('ear_tag')
            ->get(['id', 'ear_tag', 'species_id']);

        return Inertia::render('animals/create', [
            'farm' => $farm->only('id', 'name'),
            'species' => Species::with('breeds')->orderBy('name')->get(),
            'mothers' => $mothers,
            // При „Регистрирай раждане“ от профила майката идва избрана.
            'motherId' => $request->integer('mother_id') ?: null,
        ]);
    }

    public function store(StoreAnimalRequest $request, Farm $farm)
    {
        $animal = Animal::create([
            ...$request->validated(),
            'farm_id' => $farm->id,
            'status' => AnimalStatus::Active,
        ]);

        return redirect()
            ->route('animals.show', $animal)
            ->with('success', 'Животното е добавено.');
    }

    public function show(Request $request, Animal $animal, CalculateVaccinationDueDates $calculateVaccinationDueDates)
    {
        Gate::authorize('view', $animal);

        $animal->load('farm', 'species', 'breed', 'mother', 'offspring', 'weights', 'vetEvents.medication', 'vetEvents.vet');

        $events = [];
        foreach ($animal->vetEvents->sortByDesc('event_date') as $event) {
            $events[] = [
                'id' => $event->id,
                'date' => $event->event_date->format('d.m.Y'),
                'type' => $event->type->value,
                'type_label' => $event->type->label(),
                'medication' => $event->medication?->name,
                'dose' => $event->dose,
                'diagnosis' => $event->diagnosis,
                'notes' => $event->notes,
                'vet' => $event->vet->name,
            ];
        }

        $weights = [];
        foreach ($animal->weights->sortByDesc('measured_at') as $weight) {
            $weights[] = [
                'id' => $weight->id,
                'date' => $weight->measured_at->format('d.m.Y'),
                'weight_kg' => $weight->weight_kg,
            ];
        }

        $offspring = [];
        foreach ($animal->offspring as $child) {
            $offspring[] = [
                'id' => $child->id,
                'ear_tag' => $child->ear_tag,
                'sex_label' => $child->sexLabel(),
                'birth_date' => $child->birth_date?->format('d.m.Y'),
            ];
        }

        // Предстоящи ваксинации има смисъл да се показват само за активни животни.
        $vaccinations = [];
        if ($animal->isActive()) {
            $mandatorySchemes = VaccinationScheme::where('mandatory', true)->get();

            foreach ($calculateVaccinationDueDates->handle($animal, $mandatorySchemes) as $vaccination) {
                $vaccinations[] = [
                    'scheme' => $vaccination['scheme'],
                    'last_date' => $vaccination['last_date']?->format('d.m.Y'),
                    'due_date' => $vaccination['due_date']?->format('d.m.Y'),
                    'is_overdue' => $vaccination['is_overdue'],
                ];
            }
        }

        return Inertia::render('animals/show', [
            'farm' => $animal->farm->only('id', 'name'),
            'animal' => [
                'id' => $animal->id,
                'ear_tag' => $animal->ear_tag,
                'status' => $animal->status->value,
                'status_label' => $animal->status->label(),
                'status_changed_at' => $animal->status_changed_at?->format('d.m.Y'),
                'is_active' => $animal->isActive(),
                'species' => $animal->species->name,
                'breed' => $animal->breed?->name,
                'sex' => $animal->sex,
                'sex_label' => $animal->sexLabel(),
                'age' => $animal->ageText(),
                'birth_date' => $animal->birth_date?->format('d.m.Y'),
                'group_name' => $animal->group_name,
                'notes' => $animal->notes,
                'mother' => $animal->mother?->only('id', 'ear_tag'),
            ],
            'quarantine' => $this->quarantineInfo($animal),
            'events' => $events,
            'vaccinations' => $vaccinations,
            'weights' => $weights,
            'offspring' => $offspring,
            'canEdit' => $request->user()->can('update', $animal),
        ]);
    }

    // Данни за карантинната лента в профила. null, ако животното не е под карантина.
    private function quarantineInfo(Animal $animal): ?array
    {
        if (! $animal->isUnderQuarantine()) {
            return null;
        }

        return [
            'milk' => $this->withdrawalInfo($animal->milkWithdrawalUntil()),
            'meat' => $this->withdrawalInfo($animal->meatWithdrawalUntil()),
        ];
    }

    // Дата и оставащи дни. null, ако срокът го няма или вече е минал.
    private function withdrawalInfo(?Carbon $until): ?array
    {
        if ($until === null || $until->lt(today())) {
            return null;
        }

        return [
            'until' => $until->format('d.m.Y'),
            'days_left' => (int) today()->diffInDays($until),
        ];
    }
}
