<?php

namespace App\Notifications;

use App\Models\Invitation;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

// Писмото с поканата към собственика на нова ферма.
class FarmInvitation extends Notification
{
    public function __construct(private Invitation $invitation) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $farm = $this->invitation->farm;

        return (new MailMessage)
            ->subject('Покана за ферма „'.$farm->name.'“')
            ->line('ОДБХ регистрира животновъдния обект „'.$farm->name.'“ (рег. № '.$farm->reg_number.') и ви кани като негов собственик в системата „Стадо“.')
            ->action('Приеми поканата', route('invitations.show', $this->invitation->token))
            ->line('Поканата е валидна до '.$this->invitation->expires_at->format('d.m.Y').'.');
    }
}
