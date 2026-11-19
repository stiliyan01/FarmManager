<?php

namespace App\Console\Commands;

use App\Actions\CheckFarmAnimals;
use App\Models\Farm;
use App\Notifications\VaccinationReminder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;

// Пуска се всеки ден от Scheduler-а (routes/console.php).
class SendVaccinationReminders extends Command
{
    protected $signature = 'reminders:vaccinations';

    protected $description = 'Напомняне до ветеринаря и собственика за просрочени и предстоящи ваксинации';

    public function handle(CheckFarmAnimals $checkFarmAnimals): void
    {
        $farms = Farm::with('owner', 'activeVetAssignment.vet')->orderBy('name')->get();
        $sentCount = 0;

        foreach ($farms as $farm) {
            $check = $checkFarmAnimals->handle($farm);
            $overdueCount = count($check['overdue']);
            $upcomingCount = count($check['upcoming']);

            if ($overdueCount === 0 && $upcomingCount === 0) {
                continue;
            }

            $recipients = [];
            if ($farm->activeVetAssignment !== null) {
                $recipients[] = $farm->activeVetAssignment->vet;
            }
            if ($farm->owner !== null) {
                $recipients[] = $farm->owner;
            }

            Notification::send($recipients, new VaccinationReminder($farm, $overdueCount, $upcomingCount));
            $sentCount += count($recipients);
        }

        $this->info('Изпратени напомняния: '.$sentCount);
    }
}
