<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    protected $rootView = 'app';

    // Данни, които получава всяка страница.
    public function share(Request $request): array
    {
        $user = $request->user();

        return [
            ...parent::share($request),
            'name' => config('app.name'),
            'auth' => [
                'user' => $user,
                'roleLabel' => $user?->role->label(),
            ],
            'flash' => [
                'success' => $request->session()->get('success'),
            ],
        ];
    }
}
