<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'admin' => \App\Http\Middleware\EnsureAdministrador::class,
            'caixa.aberto' => \App\Http\Middleware\EnsureCaixaAberto::class,
            'pdv.screen' => \App\Http\Middleware\EnsurePdvScreenAccess::class,
            'super.panel' => \App\Http\Middleware\RestrictSuperAdminToSaaSPanel::class,
            'saas' => \App\Http\Middleware\EnsureSuperAdminRole::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
