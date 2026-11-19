<?php

namespace App\Http\Controllers;

use App\Http\Requests\VaccinationSchemeRequest;
use App\Models\Medication;
use App\Models\Species;
use App\Models\VaccinationScheme;
use Inertia\Inertia;

// Ваксинационните схеми по вид – по тях се смятат предстоящите и просрочените ваксинации.
class VaccinationSchemeController extends Controller
{
    public function index()
    {
        $schemes = VaccinationScheme::with('species', 'medication')
            ->orderBy('species_id')
            ->orderBy('name')
            ->paginate(10)
            ->through(function (VaccinationScheme $scheme) {
                return [
                    'id' => $scheme->id,
                    'species' => $scheme->species->name,
                    'name' => $scheme->name,
                    'medication' => $scheme->medication->name,
                    'interval_days' => $scheme->interval_days,
                    'mandatory' => $scheme->mandatory,
                ];
            });

        return Inertia::render('vaccination-schemes/index', [
            'schemes' => $schemes,
        ]);
    }

    public function create()
    {
        return Inertia::render('vaccination-schemes/create', [
            'species' => Species::orderBy('name')->get(['id', 'name']),
            'medications' => Medication::orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function store(VaccinationSchemeRequest $request)
    {
        VaccinationScheme::create($request->validated());

        return redirect()->route('vaccination-schemes.index')->with('success', 'Схемата е добавена.');
    }

    public function edit(VaccinationScheme $scheme)
    {
        return Inertia::render('vaccination-schemes/edit', [
            'scheme' => $scheme->only('id', 'species_id', 'name', 'medication_id', 'interval_days', 'mandatory'),
            'species' => Species::orderBy('name')->get(['id', 'name']),
            'medications' => Medication::orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function update(VaccinationSchemeRequest $request, VaccinationScheme $scheme)
    {
        $scheme->update($request->validated());

        return redirect()->route('vaccination-schemes.index')->with('success', 'Схемата е запазена.');
    }

    public function destroy(VaccinationScheme $scheme)
    {
        $scheme->delete();

        return redirect()->route('vaccination-schemes.index')->with('success', 'Схемата е изтрита.');
    }
}
