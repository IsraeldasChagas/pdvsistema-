<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSaasPlanRequest;
use App\Http\Requests\UpdateSaasPlanRequest;
use App\Models\SaasPlan;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SaasPlanController extends Controller
{
    public function index(): View
    {
        $this->ensureSuperAdmin();

        $planos = SaasPlan::query()->orderBy('nome')->get();

        return view('paginas.financeiro-saas-planos', compact('planos'));
    }

    public function create(): View
    {
        $this->ensureSuperAdmin();

        return view('paginas.financeiro-saas-plano-form', [
            'plano' => new SaasPlan([
                'valor_mensal' => 0,
                'periodicidade' => 'mensal',
                'ativo' => true,
            ]),
        ]);
    }

    public function store(StoreSaasPlanRequest $request): RedirectResponse
    {
        SaasPlan::query()->create($request->planPayload());

        return redirect()
            ->route('financeiro.saas.planos')
            ->with('status', 'Plano criado com sucesso.');
    }

    public function edit(SaasPlan $plano): View
    {
        $this->ensureSuperAdmin();

        return view('paginas.financeiro-saas-plano-form', ['plano' => $plano]);
    }

    public function update(UpdateSaasPlanRequest $request, SaasPlan $plano): RedirectResponse
    {
        $plano->update($request->planPayload());

        return redirect()
            ->route('financeiro.saas.planos')
            ->with('status', 'Plano atualizado.');
    }

    public function destroy(SaasPlan $plano): RedirectResponse
    {
        $this->ensureSuperAdmin();

        $plano->delete();

        return redirect()
            ->route('financeiro.saas.planos')
            ->with('status', 'Plano removido.');
    }

    private function ensureSuperAdmin(): void
    {
        abort_unless(auth()->user()?->isSuperAdmin(), 403);
    }
}
