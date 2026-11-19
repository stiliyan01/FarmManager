<?php

namespace App\Notifications;

use App\Models\Farm;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

// Ежедневното напомняне до ветеринаря и собственика за ваксинациите във фермата.
class VaccinationReminder extends Notification
{
    public function __construct(
        private Farm $farm,
        private int $overdueCount,
        private int $upcomingCount,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $message = (new MailMessage)
            ->subject('Ваксинации във ферма „'.$this->farm->name.'“')
            ->greeting('Здравейте, '.$notifiable->name.'!');

        if ($this->overdueCount > 0) {
            $message->line('Животни с просрочена задължителна ваксинация: '.$this->overdueCount.'.');
        }

        if ($this->upcomingCount > 0) {
            $message->line('Ваксинации, които предстоят в следващите 14 дни: '.$this->upcomingCount.'.');
        }

        return $message->action('Към фермата', route('farms.show', $this->farm));
    }
}
