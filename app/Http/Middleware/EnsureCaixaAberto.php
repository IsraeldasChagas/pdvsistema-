<?php

namespace App\Http\Middleware;

use App\Models\CashRegisterSession;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureCaixaAberto
{
    public function handle(Request $request, Closure $next): Response
    {
        $aberto = CashRegisterSession::query()
            ->where('user_id', $request->user()->id)
            ->whereNull('closed_at')
            ->exists();

        if (! $aberto) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Abra o caixa para usar o Mini PDV.',
                ], 403);
            }

            return redirect()
                ->route('modulos.caixa')
                ->withErrors(['caixa' => 'Abra o caixa para usar o Mini PDV.']);
        }

        return $next($request);
    }
}
