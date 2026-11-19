<?php

namespace Database\Seeders;

use App\Enums\VetEventType;
use App\Models\User;
use App\Models\VetEvent;
use Illuminate\Database\Seeder;
use Spatie\Activitylog\Facades\CauserResolver;

// Две корекции на записи от ветеринаря – за да не е празен журналът в демото.
class ActivityLogSeeder extends Seeder
{
    public function run(): void
    {
        $vet = User::where('email', 'vet@stado.test')->first();

        // Журналът записва кой е направил промяната.
        CauserResolver::setCauser($vet);

        $treatment = VetEvent::where('vet_id', $vet->id)->where('type', VetEventType::Treatment)->first();
        $treatment->update([
            'dose' => '6 ml',
            'notes' => 'Дозата е коригирана след претегляне на животното.',
        ]);

        $exam = VetEvent::where('vet_id', $vet->id)->where('type', VetEventType::Exam)->latest('id')->first();
        $exam->update([
            'diagnosis' => 'Клинично здраво, леко накуцване на задния десен крак',
        ]);

        CauserResolver::setCauser(null);
    }
}
