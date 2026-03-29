<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RestrictSuperAdminToSaaSPanel
{
    /**
     * Super administrador só navega em Empresas, Usuários, Financeiro SaaS e perfil.
     * Demais rotas redirecionam para a lista de empresas.
     *
     * @param  \Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user === null || ! $user->isSuperAdmin()) {
            return $next($request);
        }

        if ($request->routeIs([
            'empresas.*',
            'modulos.usuarios',
            'usuarios.*',
            'financeiro.saas',
            'financeiro.saas.*',
            'profile.*',
            'verification.*',
            'logout',
            'empresa.context',
        ])) {
            return $next($request);
        }

        return redirect()->route('empresas.index');
    }
}
