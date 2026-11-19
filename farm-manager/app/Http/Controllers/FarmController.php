<?php

namespace App\Http\Controllers;

use App\Actions\CheckFarmAnimals;
use App\Actions\InviteOwner;
use App\Enums\Role;
use App\Http\Requests\StoreFarmRequest;
use App\Http\Requests\UpdateFarmRequest;
use App\Models\Animal;
use App\Models\Farm;
use App\Models\Inspection;
use App\Models\Region;
use App\Models\User;
use App\Models\VetEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class FarmController extends Controller
{
    // Списък с фермите: за регистратора и инспектора – в тяхната област, за админа – всички.
    public function index(Request $request)
    {
        $user = $request->user();
        $search = $request->input('search');
        $filter = $request->input('filter', 'all');

        $query = Farm::with('owner', 'activeVetAssignment.vet')->orderBy('name');

        if ($user->role !== Role::Admin) {
            $query->where('region_id', $user->region_id);
        }

        $farmsCount = $query->count();

        if ($search) {
            $query->where(function ($query) use ($search) {
                $query->where('name', 'like', '%'.$search.'%')
                    ->orWhere('reg_number', 'like', '%'.$search.'%')
                    ->orWhere('settlement', 'like', '%'.$search.'%');
            });
        }

        if ($filter === 'without_vet') {
            $query->whereDoesntHave('activeVetAssignment');
        }

        if ($filter === 'without_owner') {
            $query->whereNull('owner_id');
        }

        $farms = $query->paginate(10)->withQueryString()->through(function (Farm $farm) {
            $status = $this->farmStatus($farm);

            return [
                'id' => $farm->id,
                'reg_number' => $farm->reg_number,
                'name' => $farm->name,
                'settlement' => $farm->settlement,
                'owner' => $farm->owner?->name,
                'vet' => $farm->activeVetAssignment?->vet->name,
                'status' => $status,
                'status_label' => $this->farmStatusLabel($status),
            ];
        });

        return Inertia::render('farms/index', [
            'farms' => $farms,
            'farmsCount' => $farmsCount,
            'regionName' => $user->region?->name,
            'filters' => ['search' => $search, 'filter' => $filter],
            'canCreate' => $user->can('create', Farm::class),
            // Инспекторът само гледа, регистраторът и админът управляват.
            'canManage' => $user->role !== Role::Inspector,
        ]);
    }

    // Табло на фермата: обобщение, какво изисква внимание, последни записи, ветеринар и проверка.
    public function show(Request $request, Farm $farm, CheckFarmAnimals $checkFarmAnimals)
    {
        Gate::authorize('view', $farm);

        $user = $request->user();
        $check = $checkFarmAnimals->handle($farm);

        $countBySpecies = [];
        foreach ($check['animals'] as $animal) {
            $speciesName = $animal->species->name;
            $countBySpecies[$speciesName] = ($countBySpecies[$speciesName] ?? 0) + 1;
        }

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
            'myFarms' => $this->myFarms($user),
            'stats' => [
                'animals' => $check['animals']->count(),
                'by_species' => implode(' · ', $speciesParts),
                'quarantined' => count($check['quarantined']),
                'upcoming_vaccinations' => count($check['upcoming']),
                'overdue' => count($check['overdue']),
            ],
            'overdueAnimals' => $check['overdue'],
            'quarantinedAnimals' => $check['quarantined'],
            'recentEvents' => $this->recentEvents($farm),
            'currentVet' => $this->currentVet($farm),
            'lastInspection' => $this->lastInspection($farm),
            'canAddAnimal' => $user->can('create', [Animal::class, $farm]),
            'canAddVetEvent' => $user->can('create', [VetEvent::class, $farm]),
            'canInspect' => $user->can('create', [Inspection::class, $farm]),
            'canManage' => $user->can('manage', $farm),
        ]);
    }

    public function create(Request $request)
    {
        Gate::authorize('create', Farm::class);

        // Областта избира само администраторът. Регистраторът работи в своята.
        $regions = [];
        if ($request->user()->role === Role::Admin) {
            $regions = Region::orderBy('name')->get(['id', 'name']);
        }

        return Inertia::render('farms/create', [
            'regions' => $regions,
        ]);
    }

    public function store(StoreFarmRequest $request, InviteOwner $inviteOwner)
    {
        $user = $request->user();

        $regionId = $user->region_id;
        if ($user->role === Role::Admin) {
            $regionId = $request->validated('region_id');
        }

        $farm = Farm::create([
            ...$request->safe()->except('region_id', 'owner_email'),
            'region_id' => $regionId,
        ]);

        $ownerEmail = $request->validated('owner_email');
        if ($ownerEmail) {
            $inviteOwner->handle($farm, $ownerEmail);
        }

        return redirect()
            ->route('farms.edit', $farm)
            ->with('success', 'Обектът е регистриран. Остава да назначите обслужващ ветеринар.');
    }

    // Управление на обекта: данни, собственик (покана) и обслужващ ветеринар.
    public function edit(Farm $farm)
    {
        Gate::authorize('manage', $farm);

        $farm->load('region', 'owner', 'vetAssignments.vet', 'invitations');

        $assignments = [];
        foreach ($farm->vetAssignments->sortByDesc('valid_from') as $assignment) {
            $assignments[] = [
                'id' => $assignment->id,
                'vet' => $assignment->vet->name,
                'from' => $assignment->valid_from->format('d.m.Y'),
                'to' => $assignment->valid_to?->format('d.m.Y'),
            ];
        }

        $invitations = [];
        foreach ($farm->invitations->sortByDesc('id') as $invitation) {
            $invitations[] = [
                'id' => $invitation->id,
                'email' => $invitation->email,
                'expires_at' => $invitation->expires_at->format('d.m.Y'),
                'status' => $invitation->status(),
                'status_label' => $invitation->statusLabel(),
                'url' => route('invitations.show', $invitation->token),
            ];
        }

        // Ветеринарите за падащото меню – с броя ферми, които вече обслужват.
        $vets = [];
        foreach (User::where('role', Role::Vet)->withCount('assignedFarms')->orderBy('name')->get() as $vet) {
            $vets[] = [
                'id' => $vet->id,
                'name' => $vet->name.' · ферми: '.$vet->assigned_farms_count,
            ];
        }

        $currentAssignment = $farm->vetAssignments->whereNull('valid_to')->first();

        return Inertia::render('farms/edit', [
            'farm' => [
                'id' => $farm->id,
                'reg_number' => $farm->reg_number,
                'name' => $farm->name,
                'settlement' => $farm->settlement,
                'capacity' => $farm->capacity,
                'region' => $farm->region->name,
            ],
            'owner' => $farm->owner?->only('name', 'email', 'phone'),
            'invitations' => $invitations,
            'currentVet' => $currentAssignment ? [
                'name' => $currentAssignment->vet->name,
                'since' => $currentAssignment->valid_from->format('d.m.Y'),
            ] : null,
            'assignments' => $assignments,
            'vets' => $vets,
        ]);
    }

    public function update(UpdateFarmRequest $request, Farm $farm)
    {
        $farm->update($request->validated());

        return back()->with('success', 'Данните за обекта са запазени.');
    }

    // Статус в списъка с ферми. Първо се гледа ветеринарят – без него няма кой да ваксинира.
    private function farmStatus(Farm $farm): string
    {
        if ($farm->activeVetAssignment === null) {
            return 'without_vet';
        }

        if ($farm->owner_id === null) {
            return 'without_owner';
        }

        return 'active';
    }

    private function farmStatusLabel(string $status): string
    {
        return match ($status) {
            'without_vet' => 'без ветеринар',
            'without_owner' => 'чака собственик',
            'active' => 'активен',
        };
    }

    // Фермите, между които може да се превключва от таблото: на собственика – неговите, на ветеринаря – назначените.
    private function myFarms(User $user): array
    {
        $farms = [];

        if ($user->role === Role::Owner) {
            $farms = $user->ownedFarms()->orderBy('name')->get();
        }

        if ($user->role === Role::Vet) {
            $farms = $user->assignedFarms()->orderBy('name')->get();
        }

        $result = [];
        foreach ($farms as $farm) {
            $result[] = ['id' => $farm->id, 'name' => $farm->name];
        }

        return $result;
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

    private function lastInspection(Farm $farm): ?array
    {
        $inspection = $farm->inspections()->with('inspector')->orderByDesc('inspected_at')->first();

        if ($inspection === null) {
            return null;
        }

        return [
            'date' => $inspection->inspected_at->format('d.m.Y'),
            'result' => $inspection->result->value,
            'result_label' => $inspection->result->label(),
            'inspector' => $inspection->inspector->name,
        ];
    }
}
