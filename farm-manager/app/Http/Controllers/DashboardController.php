<?php

namespace App\Http\Controllers;

use App\Enums\Role;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        // Собственикът отива направо на таблото на (първата) си ферма.
        if ($user->role === Role::Owner) {
            $farm = $user->ownedFarms()->orderBy('name')->first();

            if ($farm !== null) {
                return redirect()->route('farms.show', $farm);
            }
        }

        // Таблата за останалите роли идват в следващите стъпки.
        return Inertia::render('dashboard');
    }
}
