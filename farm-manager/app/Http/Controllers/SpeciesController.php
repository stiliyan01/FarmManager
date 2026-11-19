<?php

namespace App\Http\Controllers;

use App\Models\Species;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

// Номенклатура „Видове и породи“ – само за администратора.
class SpeciesController extends Controller
{
    public function index()
    {
        $species = Species::with(['breeds' => function ($query) {
            $query->orderBy('name');
        }])
            ->withCount('animals')
            ->orderBy('name')
            ->get();

        return Inertia::render('species/index', [
            'species' => $species,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:species,name'],
        ]);

        Species::create($data);

        return back()->with('success', 'Видът „'.$data['name'].'“ е добавен.');
    }

    public function update(Request $request, Species $species)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100', Rule::unique('species', 'name')->ignore($species)],
        ]);

        $species->update($data);

        return back()->with('success', 'Видът е преименуван.');
    }
}
