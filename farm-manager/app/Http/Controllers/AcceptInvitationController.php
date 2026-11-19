<?php

namespace App\Http\Controllers;

use App\Enums\Role;
use App\Models\Invitation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;
use Inertia\Inertia;

// Собственикът отваря линка от писмото и приема поканата.
// Ако още няма акаунт, го създава тук. Ако има – първо влиза в него.
class AcceptInvitationController extends Controller
{
    public function show(Request $request, string $token)
    {
        $invitation = Invitation::where('token', $token)->with('farm')->firstOrFail();
        $user = $request->user();
        $hasAccount = User::where('email', $invitation->email)->exists();

        // register – нов акаунт, login – трябва да влезе, accept – само потвърждава.
        $step = 'accept';

        if ($user === null && $hasAccount) {
            $step = 'login';
            // След входа го връщаме обратно на поканата.
            redirect()->setIntendedUrl($request->url());
        }

        if ($user === null && ! $hasAccount) {
            $step = 'register';
        }

        return Inertia::render('invitations/accept', [
            'invitation' => [
                'token' => $invitation->token,
                'email' => $invitation->email,
                'farm' => $invitation->farm->name,
                'reg_number' => $invitation->farm->reg_number,
                'settlement' => $invitation->farm->settlement,
            ],
            'step' => $step,
            'problem' => $this->problem($invitation, $user),
        ]);
    }

    public function store(Request $request, string $token)
    {
        $invitation = Invitation::where('token', $token)->with('farm')->firstOrFail();
        $user = $request->user();

        $problem = $this->problem($invitation, $user);
        if ($problem !== null) {
            return back()->withErrors(['invitation' => $problem]);
        }

        if ($user === null) {
            // Който вече има акаунт, трябва първо да влезе в него.
            if (User::where('email', $invitation->email)->exists()) {
                return redirect()->route('login');
            }

            $data = $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'password' => ['required', 'confirmed', Password::defaults()],
            ]);

            $user = User::create([
                'name' => $data['name'],
                'email' => $invitation->email,
                'password' => $data['password'],
                'role' => Role::Owner,
            ]);

            Auth::login($user);
        }

        $invitation->farm->update(['owner_id' => $user->id]);
        $invitation->update(['accepted_at' => now()]);

        return redirect()
            ->route('farms.show', $invitation->farm)
            ->with('success', 'Поканата е приета. Вече сте собственик на „'.$invitation->farm->name.'“.');
    }

    // Защо поканата не може да бъде приета. null – всичко е наред.
    private function problem(Invitation $invitation, ?User $user): ?string
    {
        if ($invitation->accepted_at !== null) {
            return 'Тази покана вече е приета.';
        }

        if ($invitation->expires_at->isPast()) {
            return 'Поканата е изтекла. Свържете се с ОДБХ, за да получите нова.';
        }

        if ($invitation->farm->owner_id !== null) {
            return 'Фермата вече има собственик.';
        }

        if ($user !== null && $user->email !== $invitation->email) {
            return 'Поканата е за '.$invitation->email.', а сте влезли като '.$user->email.'. Излезте и отворете линка отново.';
        }

        return null;
    }
}
