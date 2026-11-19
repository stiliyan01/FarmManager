<?php

use App\Models\Animal;
use App\Models\Farm;
use App\Models\FarmVet;
use App\Models\Inspection;
use App\Models\Region;
use App\Models\User;
use App\Models\VetEvent;
use Inertia\Testing\AssertableInertia as Assert;

beforeEach(function () {
    $this->shumen = Region::factory()->create(['name' => 'Шумен']);
    $this->inspector = User::factory()->inspector($this->shumen)->create();
});

test('control dashboard counts only the farms in the inspector region', function () {
    Farm::factory()->count(2)->create(['region_id' => $this->shumen->id]);
    Farm::factory()->create();

    $this->actingAs($this->inspector)
        ->get(route('control'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('control/index')
            ->where('region', 'Шумен')
            ->where('stats.farms', 2)
        );
});

test('late records and farms without a vet show up as signals', function () {
    $vet = User::factory()->vet()->create();
    $farm = Farm::factory()->create(['region_id' => $this->shumen->id]);
    FarmVet::factory()->create(['farm_id' => $farm->id, 'vet_id' => $vet->id]);
    $animal = Animal::factory()->create(['farm_id' => $farm->id]);

    // Преглед отпреди 20 дни, въведен едва днес – закъснял запис (правило 3).
    VetEvent::factory()->create(['animal_id' => $animal->id, 'vet_id' => $vet->id, 'event_date' => today()->subDays(20)]);

    Farm::factory()->create(['region_id' => $this->shumen->id]);

    $this->actingAs($this->inspector)
        ->get(route('control'))
        ->assertInertia(fn (Assert $page) => $page
            ->where('vets.0.name', $vet->name)
            ->where('vets.0.late', 1)
            ->where('signals', function ($signals) {
                $titles = $signals->pluck('title')->implode(' | ');

                return str_contains($titles, 'Записи, въведени след повече от 7 дни')
                    && str_contains($titles, 'Ферма без обслужващ ветеринар');
            })
        );
});

test('inspector records inspections only in own region', function () {
    $ownFarm = Farm::factory()->create(['region_id' => $this->shumen->id]);
    $otherFarm = Farm::factory()->create();

    $this->actingAs($this->inspector)
        ->post(route('inspections.store'), [
            'farm_id' => $ownFarm->id,
            'inspected_at' => today()->toDateString(),
            'result' => 'ok',
            'findings' => null,
        ])
        ->assertRedirect(route('inspections.index'));

    $this->actingAs($this->inspector)
        ->post(route('inspections.store'), [
            'farm_id' => $otherFarm->id,
            'inspected_at' => today()->toDateString(),
            'result' => 'ok',
            'findings' => null,
        ])
        ->assertForbidden();

    expect(Inspection::count())->toBe(1);
    expect(Inspection::first()->inspector_id)->toBe($this->inspector->id);
});

test('a violation must be described', function () {
    $farm = Farm::factory()->create(['region_id' => $this->shumen->id]);

    $this->actingAs($this->inspector)
        ->post(route('inspections.store'), [
            'farm_id' => $farm->id,
            'inspected_at' => today()->toDateString(),
            'result' => 'violation',
            'findings' => null,
        ])
        ->assertSessionHasErrors('findings');
});

test('inspector sees the inspections in own region only', function () {
    Inspection::factory()->create(['farm_id' => Farm::factory()->create(['region_id' => $this->shumen->id])->id]);
    Inspection::factory()->create();

    $this->actingAs($this->inspector)
        ->get(route('inspections.index'))
        ->assertInertia(fn (Assert $page) => $page->where('inspections.total', 1));
});

test('only the inspector opens the control dashboard', function () {
    $users = [
        User::factory()->registrar($this->shumen)->create(),
        User::factory()->vet()->create(),
        User::factory()->create(),
    ];

    foreach ($users as $user) {
        $this->actingAs($user)->get(route('control'))->assertForbidden();
    }
});
