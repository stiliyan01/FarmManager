<?php

namespace Database\Seeders;

use App\Actions\CalculateWithdrawalDates;
use App\Enums\AnimalStatus;
use App\Enums\VetEventType;
use App\Models\Animal;
use App\Models\Farm;
use App\Models\FarmVet;
use App\Models\Medication;
use App\Models\User;
use App\Models\VaccinationScheme;
use App\Models\VetEvent;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class VetEventSeeder extends Seeder
{
    public function run(): void
    {
        $farms = Farm::with('activeVetAssignment.vet')->get();

        foreach ($farms as $farm) {
            // Ферма без ветеринар още няма записи.
            if ($farm->activeVetAssignment === null) {
                continue;
            }

            $vet = $farm->activeVetAssignment->vet;

            $this->vaccinateFarm($farm, $vet);
            $this->treatSomeAnimals($farm, $vet);
            $this->examineSomeAnimals($farm, $vet);
        }

        $this->addRecordsFromPreviousVets();
        $this->addLateRecords();
    }

    // Масова ваксинация по задължителните схеми – един общ batch_id за цялата група.
    private function vaccinateFarm(Farm $farm, User $vet): void
    {
        $schemes = VaccinationScheme::where('mandatory', true)->with('medication')->get();

        foreach ($schemes as $scheme) {
            $animals = $farm->animals()
                ->where('species_id', $scheme->species_id)
                ->where('status', AnimalStatus::Active)
                ->get();

            if ($animals->isEmpty()) {
                continue;
            }

            $eventDate = today()->subDays(fake()->numberBetween(30, 150));

            // В Хан Крум ваксинацията е пропусната – ще излезе като просрочена.
            if ($farm->name === 'Ферма Хан Крум') {
                $eventDate = today()->subDays(400);
            }

            $batchId = (string) Str::uuid();

            foreach ($animals as $animal) {
                // Малките, родени след ваксинацията, още не са ваксинирани.
                if ($animal->birth_date > $eventDate) {
                    continue;
                }

                $this->createEvent($animal, $vet, VetEventType::Vaccination, $eventDate, $scheme->medication, [
                    'dose' => '1 ml',
                    'batch_id' => $batchId,
                ]);
            }
        }
    }

    // Няколко лечения през последния месец – част от животните още са под карантина.
    private function treatSomeAnimals(Farm $farm, User $vet): void
    {
        $treatments = [
            ['medication' => 'Пен-Стреп', 'diagnosis' => 'Инфектирана рана', 'dose' => '10 ml'],
            ['medication' => 'Окситетрациклин 20% LA', 'diagnosis' => 'Пневмония', 'dose' => '5 ml'],
            ['medication' => 'Мелоксикам 20 mg/ml', 'diagnosis' => 'Куцота', 'dose' => '2 ml'],
            ['medication' => 'Енрофлоксацин 10%', 'diagnosis' => 'Диария', 'dose' => '4 ml'],
            ['medication' => 'Ивермектин 1%', 'diagnosis' => 'Външни паразити', 'dose' => '3 ml'],
        ];

        $animals = $farm->animals()
            ->where('status', AnimalStatus::Active)
            ->inRandomOrder()
            ->limit(6)
            ->get();

        foreach ($animals as $animal) {
            $treatment = fake()->randomElement($treatments);
            $medication = Medication::where('name', $treatment['medication'])->first();
            $eventDate = today()->subDays(fake()->numberBetween(1, 40));

            $this->createEvent($animal, $vet, VetEventType::Treatment, $eventDate, $medication, [
                'diagnosis' => $treatment['diagnosis'],
                'dose' => $treatment['dose'],
            ]);
        }
    }

    private function examineSomeAnimals(Farm $farm, User $vet): void
    {
        $animals = $farm->animals()
            ->where('status', AnimalStatus::Active)
            ->inRandomOrder()
            ->limit(5)
            ->get();

        foreach ($animals as $animal) {
            $this->createEvent($animal, $vet, VetEventType::Exam, today()->subDays(fake()->numberBetween(5, 90)), null, [
                'diagnosis' => 'Клинично здраво',
            ]);
        }

        $animal = $animals->first();
        $this->createEvent($animal, $vet, VetEventType::Test, today()->subDays(60), null, [
            'diagnosis' => 'Бруцелоза – отрицателен резултат',
        ]);
    }

    // Записи от стари ветеринари – остават видими за собственика и инспектора.
    private function addRecordsFromPreviousVets(): void
    {
        $endedAssignments = FarmVet::whereNotNull('valid_to')->with('vet')->get();

        foreach ($endedAssignments as $assignment) {
            $animals = Animal::where('farm_id', $assignment->farm_id)->limit(3)->get();

            foreach ($animals as $animal) {
                $this->createEvent($animal, $assignment->vet, VetEventType::Exam, $assignment->valid_to->copy()->subMonths(2), null, [
                    'diagnosis' => 'Клинично здраво',
                ]);
            }
        }
    }

    // Два записа, въведени със закъснение – сигнал за инспектора (повече от 7 дни след прегледа).
    private function addLateRecords(): void
    {
        $assignment = FarmVet::whereNull('valid_to')->with('vet')->first();
        $animals = Animal::where('farm_id', $assignment->farm_id)->inRandomOrder()->limit(2)->get();

        foreach ($animals as $animal) {
            $event = $this->createEvent($animal, $assignment->vet, VetEventType::Exam, today()->subDays(20), null, [
                'diagnosis' => 'Клинично здраво',
            ]);

            $event->created_at = today()->subDays(8);
            $event->save();
        }
    }

    private function createEvent(Animal $animal, User $vet, VetEventType $type, Carbon $eventDate, ?Medication $medication, array $extra): VetEvent
    {
        $data = [
            'farm_id' => $animal->farm_id,
            'animal_id' => $animal->id,
            'vet_id' => $vet->id,
            'type' => $type,
            'event_date' => $eventDate,
        ];

        if ($medication !== null) {
            $data['medication_id'] = $medication->id;

            $withdrawalDates = (new CalculateWithdrawalDates)->handle($medication, $eventDate);
            $data = array_merge($data, $withdrawalDates);
        }

        $event = new VetEvent(array_merge($data, $extra));

        // Ветеринарят въвежда записа в деня на процедурата.
        $event->created_at = $eventDate;
        $event->save();

        return $event;
    }
}
