<?php

use App\Models\Animal;
use App\Models\Farm;
use App\Models\FarmVet;
use App\Models\Medication;
use App\Models\Species;
use App\Models\User;
use App\Models\VaccinationScheme;
use App\Notifications\VaccinationReminder;
use Illuminate\Support\Facades\Notification;

test('vet and owner get a reminder when a vaccination is overdue', function () {
    Notification::fake();

    $sheep = Species::factory()->create(['name' => 'Овца']);
    $vaccine = Medication::factory()->create(['withdrawal_milk_days' => 0, 'withdrawal_meat_days' => 0]);
    VaccinationScheme::create([
        'species_id' => $sheep->id,
        'name' => 'Антракс',
        'medication_id' => $vaccine->id,
        'interval_days' => 365,
        'mandatory' => true,
    ]);

    $owner = User::factory()->create();
    $vet = User::factory()->vet()->create();
    $farm = Farm::factory()->create(['owner_id' => $owner->id]);
    FarmVet::factory()->create(['farm_id' => $farm->id, 'vet_id' => $vet->id]);

    // Никога неваксинирано – просрочено.
    Animal::factory()->create(['farm_id' => $farm->id, 'species_id' => $sheep->id]);

    // Ферма без животни – няма за какво да се напомня.
    $calmOwner = User::factory()->create();
    Farm::factory()->create(['owner_id' => $calmOwner->id]);

    $this->artisan('reminders:vaccinations')->assertSuccessful();

    Notification::assertSentTo([$owner, $vet], VaccinationReminder::class);
    Notification::assertNotSentTo($calmOwner, VaccinationReminder::class);
});

test('the reminders are scheduled every day', function () {
    $this->artisan('schedule:list')
        ->expectsOutputToContain('reminders:vaccinations')
        ->assertSuccessful();
});
