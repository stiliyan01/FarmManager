<?php

namespace App\Http\Controllers;

use App\Actions\InviteOwner;
use App\Enums\Role;
use App\Http\Requests\StoreInvitationRequest;
use App\Models\Farm;
use App\Models\Invitation;
use Illuminate\Http\Request;
use Inertia\Inertia;

// Поканите към собственици – от регистратора.
class InvitationController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $query = Invitation::with('farm')->orderByDesc('id');

        if ($user->role !== Role::Admin) {
            $query->whereHas('farm', function ($query) use ($user) {
                $query->where('region_id', $user->region_id);
            });
        }

        $invitations = $query->paginate(10)->through(function (Invitation $invitation) {
            $status = $invitation->status();

            return [
                'id' => $invitation->id,
                'email' => $invitation->email,
                'farm_id' => $invitation->farm_id,
                'farm' => $invitation->farm->name,
                'sent_at' => $invitation->created_at->format('d.m.Y'),
                'expires_at' => $invitation->expires_at->format('d.m.Y'),
                'status' => $status,
                'status_label' => $invitation->statusLabel(),
                // Линкът се показва, за да може да се прати и по друг начин (напр. по телефон).
                'url' => $status === 'pending' ? route('invitations.show', $invitation->token) : null,
            ];
        });

        return Inertia::render('invitations/index', [
            'invitations' => $invitations,
        ]);
    }

    public function store(StoreInvitationRequest $request, Farm $farm, InviteOwner $inviteOwner)
    {
        if ($farm->owner_id !== null) {
            return back()->withErrors(['email' => 'Фермата вече има собственик.']);
        }

        $email = $request->validated('email');
        $inviteOwner->handle($farm, $email);

        return back()->with('success', 'Поканата е изпратена до '.$email.'.');
    }
}
