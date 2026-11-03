<?php

namespace Database\Seeders;

use App\Enums\Role;
use App\Models\Farm;
use App\Models\FarmVet;
use App\Models\Invitation;
use App\Models\Region;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class FarmSeeder extends Seeder
{
    public function run(): void
    {
        $shumen = Region::where('name', 'Шумен')->first();
        $registrar = User::where('email', 'registrar@stado.test')->first();

        $vetIvan = User::where('email', 'vet@stado.test')->first();
        $vetMaria = User::where('email', 'vet2@stado.test')->first();

        $ownerStoyan = User::where('email', 'owner@stado.test')->first();
        $ownerElena = User::where('email', 'owner2@stado.test')->first();
        $ownerNikolay = User::where('email', 'owner3@stado.test')->first();

        $madara = Farm::create([
            'region_id' => $shumen->id,
            'reg_number' => '9700-0011',
            'name' => 'Ферма Мадара',
            'settlement' => 'с. Мадара',
            'owner_id' => $ownerStoyan->id,
            'capacity' => 80,
        ]);

        $kaspichan = Farm::create([
            'region_id' => $shumen->id,
            'reg_number' => '9700-0024',
            'name' => 'Козеферма Каспичан',
            'settlement' => 'гр. Каспичан',
            'owner_id' => $ownerStoyan->id,
            'capacity' => 50,
        ]);

        $noviPazar = Farm::create([
            'region_id' => $shumen->id,
            'reg_number' => '9700-0037',
            'name' => 'Кравеферма Нови пазар',
            'settlement' => 'гр. Нови пазар',
            'owner_id' => $ownerElena->id,
            'capacity' => 40,
        ]);

        $hanKrum = Farm::create([
            'region_id' => $shumen->id,
            'reg_number' => '9700-0042',
            'name' => 'Ферма Хан Крум',
            'settlement' => 'с. Хан Крум',
            'owner_id' => $ownerNikolay->id,
            'capacity' => 70,
        ]);

        // Нова ферма – собственикът още не е приел поканата.
        $smyadovo = Farm::create([
            'region_id' => $shumen->id,
            'reg_number' => '9700-0058',
            'name' => 'Ферма Смядово',
            'settlement' => 'гр. Смядово',
            'owner_id' => null,
            'capacity' => 60,
        ]);

        Invitation::create([
            'farm_id' => $smyadovo->id,
            'email' => 'nov.sobstvenik@example.com',
            'role' => Role::Owner,
            'token' => Str::random(64),
            'expires_at' => now()->addDays(7),
        ]);

        // Назначения на ветеринари
        FarmVet::create([
            'farm_id' => $madara->id,
            'vet_id' => $vetIvan->id,
            'assigned_by' => $registrar->id,
            'valid_from' => today()->subYears(2),
        ]);

        FarmVet::create([
            'farm_id' => $noviPazar->id,
            'vet_id' => $vetMaria->id,
            'assigned_by' => $registrar->id,
            'valid_from' => today()->subYears(3),
        ]);

        FarmVet::create([
            'farm_id' => $hanKrum->id,
            'vet_id' => $vetMaria->id,
            'assigned_by' => $registrar->id,
            'valid_from' => today()->subYear(),
        ]);

        // В Каспичан ветеринарят е сменен: д-р Димитрова е до преди 5 месеца, след това д-р Петров.
        $changeDate = today()->subMonths(5);

        FarmVet::create([
            'farm_id' => $kaspichan->id,
            'vet_id' => $vetMaria->id,
            'assigned_by' => $registrar->id,
            'valid_from' => today()->subYears(2),
            'valid_to' => $changeDate->copy()->subDay(),
        ]);

        FarmVet::create([
            'farm_id' => $kaspichan->id,
            'vet_id' => $vetIvan->id,
            'assigned_by' => $registrar->id,
            'valid_from' => $changeDate,
        ]);
    }
}
