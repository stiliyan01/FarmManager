<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAnimalWeightRequest;
use App\Models\Animal;

class AnimalWeightController extends Controller
{
    public function store(StoreAnimalWeightRequest $request, Animal $animal)
    {
        if (! $animal->isActive()) {
            return back()->withErrors(['weight_kg' => 'Тегло се записва само на активно животно.']);
        }

        $animal->weights()->create($request->validated());

        return back()->with('success', 'Теглото е записано.');
    }
}
