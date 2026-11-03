<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

// Пуска само потребители с една от изброените роли.
// Пример: Route::middleware('role:registrar,admin')
class EnsureUserHasRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $userRole = $request->user()->role->value;

        if (! in_array($userRole, $roles)) {
            abort(403);
        }

        return $next($request);
    }
}
