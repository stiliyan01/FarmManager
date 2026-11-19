<?php

namespace App\Http\Controllers;

use App\Models\Breed;
use App\Models\Species;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

// Породите се добавят и преименуват от страницата „Видове и породи“.
class BreedController extends Controller
{
    public function store(Request $request, Species $species)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100', Rule::unique('breeds', 'name')->where('species_id', $species->id)],
        ]);

        $species->breeds()->create($data);

        return back()->with('success', 'Породата „'.$data['name'].'“ е добавена.');
    }

    public function update(Request $request, Breed $breed)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100', Rule::unique('breeds', 'name')->where('species_id', $breed->species_id)->ignore($breed)],
        ]);

        $breed->update($data);

        return back()->with('success', 'Породата е преименувана.');
    }
}
