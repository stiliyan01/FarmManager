<?php

use App\Models\VetEvent;

// Правило 3: записът е закъснял, ако е въведен повече от 7 дни след мероприятието.
test('a record entered more than 7 days after the event is late', function () {
    $event = new VetEvent(['event_date' => today()->subDays(10)]);
    $event->created_at = now();

    expect($event->enteredAfterDays())->toBe(10);
    expect($event->isLate())->toBeTrue();
});

test('a record entered within 7 days is not late', function () {
    $event = new VetEvent(['event_date' => today()->subDays(7)]);
    $event->created_at = now();

    expect($event->isLate())->toBeFalse();
});

test('a record entered on the day of the event is not late', function () {
    $event = new VetEvent(['event_date' => today()]);
    $event->created_at = now();

    expect($event->enteredAfterDays())->toBe(0);
    expect($event->isLate())->toBeFalse();
});
