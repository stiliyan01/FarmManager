<?php

namespace App\Actions;

use App\Enums\Role;
use App\Models\Farm;
use App\Models\Invitation;
use App\Notifications\FarmInvitation;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;

// Покана към собственика на фермата по имейл. Валидна е 7 дни.
// Собственикът я приема от линка в писмото и тогава става собственик на фермата.
class InviteOwner
{
    public function handle(Farm $farm, string $email): Invitation
    {
        $invitation = Invitation::create([
            'farm_id' => $farm->id,
            'email' => $email,
            'role' => Role::Owner,
            'token' => Str::random(64),
            'expires_at' => now()->addDays(7),
        ]);

        Notification::route('mail', $email)->notify(new FarmInvitation($invitation));

        return $invitation;
    }
}
