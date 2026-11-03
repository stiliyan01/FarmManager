<?php

namespace App\Actions;

use App\Models\Medication;
use Illuminate\Support\Carbon;

// Карантинен срок: до коя дата млякото и месото от животното не бива да се ползват.
class CalculateWithdrawalDates
{
    public function handle(Medication $medication, Carbon $eventDate): array
    {
        $milkUntil = null;
        $meatUntil = null;

        if ($medication->withdrawal_milk_days > 0) {
            $milkUntil = $eventDate->copy()->addDays($medication->withdrawal_milk_days);
        }

        if ($medication->withdrawal_meat_days > 0) {
            $meatUntil = $eventDate->copy()->addDays($medication->withdrawal_meat_days);
        }

        return [
            'milk_withdrawal_until' => $milkUntil,
            'meat_withdrawal_until' => $meatUntil,
        ];
    }
}
