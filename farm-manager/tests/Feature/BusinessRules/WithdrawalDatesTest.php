<?php

use App\Actions\CalculateWithdrawalDates;
use App\Models\Medication;
use Illuminate\Support\Carbon;

test('withdrawal dates are the event date plus the medication days', function () {
    $medication = new Medication(['withdrawal_milk_days' => 5, 'withdrawal_meat_days' => 21]);

    $dates = (new CalculateWithdrawalDates)->handle($medication, Carbon::parse('2026-03-10'));

    expect($dates['milk_withdrawal_until']->toDateString())->toBe('2026-03-15');
    expect($dates['meat_withdrawal_until']->toDateString())->toBe('2026-03-31');
});

test('there is no withdrawal date when the medication has no withdrawal period', function () {
    $vaccine = new Medication(['withdrawal_milk_days' => 0, 'withdrawal_meat_days' => 0]);

    $dates = (new CalculateWithdrawalDates)->handle($vaccine, Carbon::parse('2026-03-10'));

    expect($dates['milk_withdrawal_until'])->toBeNull();
    expect($dates['meat_withdrawal_until'])->toBeNull();
});

test('the event date itself is not changed', function () {
    $medication = new Medication(['withdrawal_milk_days' => 5, 'withdrawal_meat_days' => 21]);
    $eventDate = Carbon::parse('2026-03-10');

    (new CalculateWithdrawalDates)->handle($medication, $eventDate);

    expect($eventDate->toDateString())->toBe('2026-03-10');
});
