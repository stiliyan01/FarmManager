<?php

use App\Models\Farm;
use App\Models\Inspection;
use App\Models\Region;
use App\Models\User;

test('inspector can create inspections only for farms in own region', function () {
    $shumen = Region::factory()->create();
    $varna = Region::factory()->create();
    $inspector = User::factory()->inspector($shumen)->create();

    $farmInShumen = Farm::factory()->create(['region_id' => $shumen->id]);
    $farmInVarna = Farm::factory()->create(['region_id' => $varna->id]);

    expect($inspector->can('create', [Inspection::class, $farmInShumen]))->toBeTrue();
    expect($inspector->can('create', [Inspection::class, $farmInVarna]))->toBeFalse();
});

test('registrar cannot create inspections', function () {
    $region = Region::factory()->create();
    $registrar = User::factory()->registrar($region)->create();
    $farm = Farm::factory()->create(['region_id' => $region->id]);

    expect($registrar->can('create', [Inspection::class, $farm]))->toBeFalse();
});

test('owner can see the inspections of own farm only', function () {
    $owner = User::factory()->create();
    $ownFarm = Farm::factory()->create(['owner_id' => $owner->id]);

    $ownInspection = Inspection::factory()->create(['farm_id' => $ownFarm->id]);
    $otherInspection = Inspection::factory()->create();

    expect($owner->can('view', $ownInspection))->toBeTrue();
    expect($owner->can('view', $otherInspection))->toBeFalse();
});
