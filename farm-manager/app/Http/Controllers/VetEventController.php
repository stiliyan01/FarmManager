<?php

namespace App\Http\Controllers;

use App\Actions\CalculateWithdrawalDates;
use App\Actions\CreateVetEvents;
use App\Enums\AnimalStatus;
use App\Enums\VetEventType;
use App\Http\Requests\StoreVetEventRequest;
use App\Http\Requests\UpdateVetEventRequest;
use App\Models\Farm;
use App\Models\Medication;
use App\Models\VetEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class VetEventController extends Controller
{
    // Всички ветеринарни записи във фермата – виждат ги всички, които виждат фермата.
    public function index(Request $request, Farm $farm)
    {
        Gate::authorize('view', $farm);

        $user = $request->user();
        $search = $request->input('search');
        $type = $request->input('type', 'all');

        $query = $farm->vetEvents()
            ->with('animal', 'medication', 'vet', 'farm')
            ->orderByDesc('event_date')
            ->orderByDesc('id');

        if ($type !== 'all') {
            $query->where('type', $type);
        }

        if ($search) {
            $query->whereHas('animal', function ($query) use ($search) {
                $query->where('ear_tag', 'like', '%'.$search.'%');
            });
        }

        $events = $query->paginate(10)->withQueryString()->through(function (VetEvent $event) use ($user) {
            return [
                'id' => $event->id,
                'date' => $event->event_date->format('d.m.Y'),
                'animal_id' => $event->animal_id,
                'ear_tag' => $event->animal->ear_tag,
                'type' => $event->type->value,
                'type_label' => $event->type->label(),
                'medication' => $event->medication?->name,
                'dose' => $event->dose,
                'diagnosis' => $event->diagnosis,
                'vet' => $event->vet->name,
                'milk_until' => $event->milk_withdrawal_until?->format('d.m.Y'),
                'meat_until' => $event->meat_withdrawal_until?->format('d.m.Y'),
                'entered_late_days' => $event->isLate() ? $event->enteredAfterDays() : null,
                'can_edit' => $user->can('update', $event),
            ];
        });

        return Inertia::render('vet-events/index', [
            'farm' => $farm->only('id', 'name'),
            'events' => $events,
            'types' => VetEventType::options(),
            'filters' => ['search' => $search, 'type' => $type],
            'canCreate' => $user->can('create', [VetEvent::class, $farm]),
        ]);
    }

    public function create(Request $request, Farm $farm)
    {
        Gate::authorize('create', [VetEvent::class, $farm]);

        // Само активните животни – продадени и умрели не получават нови записи (правило 6).
        $animals = $farm->animals()
            ->where('status', AnimalStatus::Active)
            ->with('species')
            ->orderBy('ear_tag')
            ->get();

        $animalOptions = [];
        foreach ($animals as $animal) {
            $animalOptions[] = [
                'id' => $animal->id,
                'ear_tag' => $animal->ear_tag,
                'species' => $animal->species->name,
                'group_name' => $animal->group_name,
            ];
        }

        $myFarms = [];
        foreach ($request->user()->assignedFarms()->orderBy('name')->get() as $assignedFarm) {
            $myFarms[] = ['id' => $assignedFarm->id, 'name' => $assignedFarm->name];
        }

        return Inertia::render('vet-events/create', [
            'farm' => $farm->only('id', 'name'),
            'myFarms' => $myFarms,
            'animals' => $animalOptions,
            'medications' => $this->medications(),
            'types' => VetEventType::options(),
            // От профила на животно то идва избрано.
            'selectedAnimalId' => $request->integer('animal_id') ?: null,
        ]);
    }

    public function store(StoreVetEventRequest $request, Farm $farm, CreateVetEvents $createVetEvents)
    {
        $count = $createVetEvents->handle($farm, $request->user(), $request->validated());

        // Запис за едно животно – обратно в профила му. Масов запис – към списъка със записите.
        if ($count === 1) {
            return redirect()
                ->route('animals.show', $request->validated('animal_ids')[0])
                ->with('success', 'Записът е добавен.');
        }

        return redirect()
            ->route('farms.vet-events.index', $farm)
            ->with('success', 'Записът е добавен за '.$count.' животни.');
    }

    public function edit(VetEvent $vetEvent)
    {
        Gate::authorize('update', $vetEvent);

        $vetEvent->load('animal', 'farm');

        return Inertia::render('vet-events/edit', [
            'farm' => $vetEvent->farm->only('id', 'name'),
            'vetEvent' => [
                'id' => $vetEvent->id,
                'type' => $vetEvent->type->value,
                'medication_id' => $vetEvent->medication_id,
                'dose' => $vetEvent->dose,
                'event_date' => $vetEvent->event_date->toDateString(),
                'diagnosis' => $vetEvent->diagnosis,
                'notes' => $vetEvent->notes,
                'animal' => $vetEvent->animal->only('id', 'ear_tag'),
            ],
            'medications' => $this->medications(),
            'types' => VetEventType::options(),
        ]);
    }

    public function update(UpdateVetEventRequest $request, VetEvent $vetEvent, CalculateWithdrawalDates $calculateWithdrawalDates)
    {
        $data = $request->validated();

        // Препаратът или датата може да са сменени – карантината се смята наново.
        $medication = Medication::find($data['medication_id'] ?? null);
        $withdrawalDates = $calculateWithdrawalDates->handle($medication, Carbon::parse($data['event_date']));

        // Старите и новите стойности отиват в журнала (LogsActivity във VetEvent).
        $vetEvent->update([...$data, ...$withdrawalDates]);

        return redirect()
            ->route('animals.show', $vetEvent->animal_id)
            ->with('success', 'Записът е редактиран.');
    }

    public function destroy(VetEvent $vetEvent)
    {
        Gate::authorize('delete', $vetEvent);

        $vetEvent->delete();

        return redirect()
            ->route('animals.show', $vetEvent->animal_id)
            ->with('success', 'Записът е изтрит.');
    }

    // Препаратите с карантинните срокове – формата ги показва, щом се избере препарат.
    private function medications()
    {
        return Medication::orderBy('name')->get(['id', 'name', 'withdrawal_milk_days', 'withdrawal_meat_days']);
    }
}
