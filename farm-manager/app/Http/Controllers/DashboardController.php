<?php

namespace App\Http\Controllers;

use App\Enums\Role;
use Illuminate\Http\Request;
use Inertia\Inertia;

// След вход всеки отива на своето табло.
class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->role === Role::Vet) {
            return redirect()->route('vet.dashboard');
        }

        if ($user->role === Role::Registrar) {
            return redirect()->route('farms.index');
        }

        if ($user->role === Role::Inspector) {
            return redirect()->route('control');
        }

        if ($user->role === Role::Admin) {
            return redirect()->route('users.index');
        }

        // Собственикът отива направо на таблото на (първата) си ферма.
        $farm = $user->ownedFarms()->orderBy('name')->first();

        if ($farm !== null) {
            return redirect()->route('farms.show', $farm);
        }

        // Собственик, който още няма ферма.
        return Inertia::render('dashboard');
    }
}
