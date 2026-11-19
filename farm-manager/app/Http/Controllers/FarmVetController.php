<?php

namespace App\Http\Controllers;

use App\Actions\AssignVet;
use App\Http\Requests\AssignVetRequest;
use App\Models\Farm;
use App\Models\User;
use Illuminate\Support\Carbon;

// Назначаване и смяна на обслужващия ветеринар (от регистратора).
class FarmVetController extends Controller
{
    public function store(AssignVetRequest $request, Farm $farm, AssignVet $assignVet)
    {
        $vet = User::find($request->validated('vet_id'));
        $validFrom = Carbon::parse($request->validated('valid_from'));

        $assignVet->handle($farm, $vet, $validFrom, $request->user());

        return back()->with('success', 'Обслужващ ветеринар на фермата вече е '.$vet->name.'.');
    }
}
