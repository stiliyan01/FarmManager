<?php

namespace App\Http\Controllers;

use App\Enums\Role;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\Breed;
use App\Models\Medication;
use App\Models\Region;
use App\Models\Species;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

// Потребители и роли – само за администратора.
class UserController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $role = $request->input('role', 'all');

        $query = User::with('region')
            ->withCount('ownedFarms', 'assignedFarms')
            ->orderBy('name');

        if ($role !== 'all') {
            $query->where('role', $role);
        }

        if ($search) {
            $query->where(function ($query) use ($search) {
                $query->where('name', 'like', '%'.$search.'%')
                    ->orWhere('email', 'like', '%'.$search.'%');
            });
        }

        $users = $query->paginate(10)->withQueryString()->through(function (User $user) {
            return [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role->value,
                'role_label' => $user->role->label(),
                'scope' => $this->scope($user),
                'last_login' => $user->last_login_at?->format('d.m.Y H:i'),
            ];
        });

        // Брой потребители по роли – за картите над таблицата.
        $roleCounts = [];
        foreach (Role::cases() as $roleCase) {
            $roleCounts[] = [
                'value' => $roleCase->value,
                'label' => $roleCase->pluralLabel(),
                'count' => User::where('role', $roleCase)->count(),
            ];
        }

        return Inertia::render('users/index', [
            'users' => $users,
            'usersCount' => User::count(),
            'roleCounts' => $roleCounts,
            'nomenclatures' => [
                'regions' => Region::count(),
                'species' => Species::count(),
                'breeds' => Breed::count(),
                'medications' => Medication::count(),
            ],
            'filters' => ['search' => $search, 'role' => $role],
        ]);
    }

    public function create()
    {
        return Inertia::render('users/create', [
            'roles' => Role::options(),
            'regions' => Region::orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function store(StoreUserRequest $request)
    {
        User::create($this->withRegionOnlyForOdbh($request->validated()));

        return redirect()->route('users.index')->with('success', 'Потребителят е създаден.');
    }

    public function edit(User $user)
    {
        return Inertia::render('users/edit', [
            'user' => $user->only('id', 'name', 'email', 'phone', 'role', 'region_id'),
            'roles' => Role::options(),
            'regions' => Region::orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        $data = $this->withRegionOnlyForOdbh($request->validated());

        // Празно поле за парола – паролата остава старата.
        if (empty($data['password'])) {
            unset($data['password']);
        }

        $user->update($data);

        return redirect()->route('users.index')->with('success', 'Промените по '.$user->name.' са запазени.');
    }

    // Област има само служителят на ОДБХ. За другите роли полето се изчиства.
    private function withRegionOnlyForOdbh(array $data): array
    {
        if ($data['role'] !== Role::Registrar->value && $data['role'] !== Role::Inspector->value) {
            $data['region_id'] = null;
        }

        return $data;
    }

    // Обхватът на ролята: фермите на собственика или ветеринаря, областта на служителя на ОДБХ.
    private function scope(User $user): string
    {
        if ($user->role === Role::Owner) {
            return $this->farmsText($user->owned_farms_count);
        }

        if ($user->role === Role::Vet) {
            return $this->farmsText($user->assigned_farms_count);
        }

        if ($user->role === Role::Admin) {
            return 'цялата система';
        }

        return 'ОДБХ '.$user->region->name;
    }

    private function farmsText(int $count): string
    {
        if ($count === 0) {
            return 'няма ферми';
        }

        if ($count === 1) {
            return '1 ферма';
        }

        return $count.' ферми';
    }
}
