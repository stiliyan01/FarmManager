<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAnimalWeightRequest;
use App\Models\Animal;

class AnimalWeightController extends Controller
{
    public function store(StoreAnimalWeightRequest $request, Animal $animal)
    {
        $animal->weights()->create($request->validated());

        return back()->with('success', 'Теглото е записано.');
    }
}
