<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePdvScreenAccess
{
    /**
     * @param  \Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user === null) {
            return $next($request);
        }

        if (! $user->is_active) {
            auth()->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')->withErrors([
                'email' => 'Sua conta está inativa. Procure o administrador.',
            ]);
        }

        if ($user->isSuperAdmin()) {
            return $next($request);
        }

        $screen = $this->resolveScreen($request);

        if ($screen === null) {
            return $next($request);
        }

        if (! $user->hasScreenAccess($screen)) {
            abort(403, 'Sem permissão para acessar esta tela.');
        }

        return $next($request);
    }

    private function resolveScreen(Request $request): ?string
    {
        foreach (config('pdv.route_screen_map', []) as $row) {
            foreach ($row['patterns'] as $pattern) {
                if ($request->routeIs($pattern)) {
                    return $row['screen'];
                }
            }
        }

        return null;
    }
}

