<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserRole
{
    public function handle(Request $request, Closure $next, string $role): Response
    {
        $user = $request->user();

        if (! $user || ! $user->rol || $user->rol->nombre !== $role) {
            abort(403, 'No autorizado para acceder a este recurso.');
        }

        return $next($request);
    }
}
