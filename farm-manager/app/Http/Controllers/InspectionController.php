<?php

namespace App\Http\Controllers;

use App\Enums\InspectionResult;
use App\Enums\Role;
use App\Http\Requests\StoreInspectionRequest;
use App\Models\Farm;
use App\Models\Inspection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

// Проверките на инспекторите. Инспекторът вижда своята област, администраторът – всички.
class InspectionController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $result = $request->input('result', 'all');

        $query = Inspection::with('farm', 'inspector')
            ->orderByDesc('inspected_at')
            ->orderByDesc('id');

        if ($user->role !== Role::Admin) {
            $query->whereHas('farm', function ($query) use ($user) {
                $query->where('region_id', $user->region_id);
            });
        }

        if ($result !== 'all') {
            $query->where('result', $result);
        }

        $inspections = $query->paginate(10)->withQueryString()->through(function (Inspection $inspection) {
            return [
                'id' => $inspection->id,
                'date' => $inspection->inspected_at->format('d.m.Y'),
                'farm_id' => $inspection->farm_id,
                'farm' => $inspection->farm->name,
                'reg_number' => $inspection->farm->reg_number,
                'inspector' => $inspection->inspector->name,
                'result' => $inspection->result->value,
                'result_label' => $inspection->result->label(),
                'findings' => $inspection->findings,
            ];
        });

        return Inertia::render('inspections/index', [
            'inspections' => $inspections,
            'filters' => ['result' => $result],
            'canCreate' => $user->role === Role::Inspector,
        ]);
    }

    public function create(Request $request)
    {
        $farms = Farm::where('region_id', $request->user()->region_id)
            ->orderBy('name')
            ->get(['id', 'name', 'reg_number']);

        return Inertia::render('inspections/create', [
            'farms' => $farms,
            'results' => [
                ['value' => InspectionResult::Ok->value, 'label' => InspectionResult::Ok->label()],
                ['value' => InspectionResult::Violation->value, 'label' => InspectionResult::Violation->label()],
            ],
            // От таблото на фермата тя идва избрана.
            'selectedFarmId' => $request->integer('farm_id') ?: null,
        ]);
    }

    public function store(StoreInspectionRequest $request)
    {
        $farm = Farm::find($request->validated('farm_id'));
        Gate::authorize('create', [Inspection::class, $farm]);

        Inspection::create([
            ...$request->validated(),
            'inspector_id' => $request->user()->id,
        ]);

        return redirect()
            ->route('inspections.index')
            ->with('success', 'Проверката във „'.$farm->name.'“ е записана.');
    }
}
