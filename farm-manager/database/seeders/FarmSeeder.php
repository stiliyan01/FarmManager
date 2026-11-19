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

        $this->createSmallerFarms($shumen, $registrar, $vetIvan, $vetMaria);
    }

    // По-малките ферми в областта – за да е пълен списъкът на регистратора и инспектора.
    // Могила е без ветеринар, а Венец още чака собственика да приеме поканата.
    private function createSmallerFarms(Region $region, User $registrar, User $vetIvan, User $vetMaria): void
    {
        $farms = [
            ['reg_number' => '9700-0063', 'name' => 'Ферма Ивански', 'settlement' => 'с. Ивански', 'owner' => 'owner4@stado.test', 'invitation' => null, 'vet' => $vetIvan, 'capacity' => 30],
            ['reg_number' => '9700-0071', 'name' => 'Ферма Царев брод', 'settlement' => 'с. Царев брод', 'owner' => 'owner5@stado.test', 'invitation' => null, 'vet' => $vetMaria, 'capacity' => 20],
            ['reg_number' => '9700-0085', 'name' => 'Овцеферма Велики Преслав', 'settlement' => 'гр. Велики Преслав', 'owner' => 'owner6@stado.test', 'invitation' => null, 'vet' => $vetIvan, 'capacity' => 40],
            ['reg_number' => '9700-0092', 'name' => 'Ферма Могила', 'settlement' => 'с. Могила', 'owner' => 'owner7@stado.test', 'invitation' => null, 'vet' => null, 'capacity' => 25],
            ['reg_number' => '9700-0104', 'name' => 'Ферма Върбица', 'settlement' => 'гр. Върбица', 'owner' => 'owner8@stado.test', 'invitation' => null, 'vet' => $vetMaria, 'capacity' => 30],
            ['reg_number' => '9700-0117', 'name' => 'Ферма Венец', 'settlement' => 'с. Венец', 'owner' => null, 'invitation' => 'venets.ferma@example.com', 'vet' => $vetMaria, 'capacity' => 50],
            ['reg_number' => '9700-0123', 'name' => 'Кравеферма Хитрино', 'settlement' => 'с. Хитрино', 'owner' => 'owner9@stado.test', 'invitation' => null, 'vet' => $vetIvan, 'capacity' => 15],
        ];

        foreach ($farms as $data) {
            $ownerId = null;
            if ($data['owner'] !== null) {
                $ownerId = User::where('email', $data['owner'])->first()->id;
            }

            $farm = Farm::create([
                'region_id' => $region->id,
                'reg_number' => $data['reg_number'],
                'name' => $data['name'],
                'settlement' => $data['settlement'],
                'owner_id' => $ownerId,
                'capacity' => $data['capacity'],
            ]);

            if ($data['vet'] !== null) {
                FarmVet::create([
                    'farm_id' => $farm->id,
                    'vet_id' => $data['vet']->id,
                    'assigned_by' => $registrar->id,
                    'valid_from' => today()->subMonths(fake()->numberBetween(8, 36)),
                ]);
            }

            if ($data['invitation'] !== null) {
                Invitation::create([
                    'farm_id' => $farm->id,
                    'email' => $data['invitation'],
                    'role' => Role::Owner,
                    'token' => Str::random(64),
                    'expires_at' => now()->addDays(5),
                ]);
            }
        }
    }
}
