<?php

namespace App\Providers;

use App\Models\Company;
use App\Models\PdvSetting;
use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Event::listen(Login::class, function (Login $event): void {
            if ($event->user->isSuperAdmin()) {
                $cid = Company::query()->orderBy('id')->value('id');
                if ($cid !== null) {
                    session(['current_company_id' => (int) $cid]);
                }
            }
        });

        View::composer('layouts.app', function ($view): void {
            if (! auth()->check()) {
                return;
            }

            if (auth()->user()->isSuperAdmin()) {
                $view->with(
                    'empresasSwitcher',
                    Company::query()->where('ativo', true)->orderBy('nome')->get()
                );
            }

            $view->with('pdvSetting', PdvSetting::current());
        });
    }
}
