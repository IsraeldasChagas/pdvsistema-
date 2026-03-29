<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdministrador
{
    /**
     * @param  \Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user()?->canManageUsers()) {
            abort(403, 'Apenas administradores podem gerenciar usuários.');
        }

        return $next($request);
    }
}
