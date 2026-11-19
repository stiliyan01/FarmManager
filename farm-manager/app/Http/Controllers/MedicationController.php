<?php

namespace App\Http\Controllers;

use App\Enums\Role;
use App\Http\Requests\MedicationRequest;
use App\Models\Medication;
use Illuminate\Http\Request;
use Inertia\Inertia;

// Препаратите. Ветеринарят ги вижда за справка, администраторът ги поддържа.
class MedicationController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $query = Medication::orderBy('name');

        if ($search) {
            $query->where(function ($query) use ($search) {
                $query->where('name', 'like', '%'.$search.'%')
                    ->orWhere('active_substance', 'like', '%'.$search.'%');
            });
        }

        return Inertia::render('medications/index', [
            'medications' => $query->paginate(10)->withQueryString(),
            'filters' => ['search' => $search],
            'canManage' => $request->user()->role === Role::Admin,
        ]);
    }

    public function create()
    {
        return Inertia::render('medications/create');
    }

    public function store(MedicationRequest $request)
    {
        Medication::create($request->validated());

        return redirect()->route('medications.index')->with('success', 'Препаратът е добавен.');
    }

    public function edit(Medication $medication)
    {
        return Inertia::render('medications/edit', [
            'medication' => $medication->only('id', 'name', 'active_substance', 'withdrawal_milk_days', 'withdrawal_meat_days'),
        ]);
    }

    // Новите срокове важат за новите записи. Старите си пазят изчислените дати.
    public function update(MedicationRequest $request, Medication $medication)
    {
        $medication->update($request->validated());

        return redirect()->route('medications.index')->with('success', 'Препаратът е запазен.');
    }
}
