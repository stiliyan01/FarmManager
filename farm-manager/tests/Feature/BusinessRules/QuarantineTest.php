<?php

use App\Models\Animal;
use App\Models\VetEvent;

test('animal is under quarantine until the later of the milk and meat dates', function () {
    $animal = new Animal;
    $animal->setRelation('vetEvents', collect([
        new VetEvent(['milk_withdrawal_until' => today()->addDays(3), 'meat_withdrawal_until' => today()->addDays(20)]),
    ]));

    expect($animal->isUnderQuarantine())->toBeTrue();
    expect($animal->quarantineUntil()->toDateString())->toBe(today()->addDays(20)->toDateString());
});

test('quarantine still counts on its last day', function () {
    $animal = new Animal;
    $animal->setRelation('vetEvents', collect([
        new VetEvent(['milk_withdrawal_until' => today()]),
    ]));

    expect($animal->isUnderQuarantine())->toBeTrue();
});

test('animal is not under quarantine when all dates have passed', function () {
    $animal = new Animal;
    $animal->setRelation('vetEvents', collect([
        new VetEvent(['milk_withdrawal_until' => today()->subDay(), 'meat_withdrawal_until' => today()->subDays(5)]),
        new VetEvent(['milk_withdrawal_until' => null, 'meat_withdrawal_until' => null]),
    ]));

    expect($animal->isUnderQuarantine())->toBeFalse();
});
