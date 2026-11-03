<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateAnimalStatusRequest;
use App\Models\Animal;

// Смяна на статус: продадено или умряло.
class AnimalStatusController extends Controller
{
    public function update(UpdateAnimalStatusRequest $request, Animal $animal)
    {
        if (! $animal->isActive()) {
            return back()->withErrors(['status' => 'Статусът може да се смени само на активно животно.']);
        }

        $animal->update([
            'status' => $request->validated('status'),
            'status_changed_at' => $request->validated('status_date'),
        ]);

        return back()->with('success', 'Статусът е сменен.');
    }
}
