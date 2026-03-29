<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSaasChargeRequest;
use App\Http\Requests\UpdateSaasChargeRequest;
use App\Models\Company;
use App\Models\SaasCharge;
use App\Models\SaasPlan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SaasChargeController extends Controller
{
    public function create(): View
    {
        $this->ensureSuperAdmin();

        return view('paginas.financeiro-saas-charge-form', [
            'charge' => new SaasCharge([
                'status' => SaasCharge::STATUS_PENDENTE,
                'valor' => 0,
            ]),
            'companies' => Company::query()->orderBy('nome')->get(),
            'planos' => SaasPlan::query()->where('ativo', true)->orderBy('nome')->get(),
        ]);
    }

    public function store(StoreSaasChargeRequest $request): RedirectResponse
    {
        $this->ensureSuperAdmin();

        SaasCharge::query()->create($request->validatedPayload());

        return redirect()
            ->route('financeiro.saas.cobrancas', $this->filterQuery($request))
            ->with('status', 'Cobrança registrada.');
    }

    public function edit(SaasCharge $charge): View
    {
        $this->ensureSuperAdmin();

        return view('paginas.financeiro-saas-charge-form', [
            'charge' => $charge,
            'companies' => Company::query()->orderBy('nome')->get(),
            'planos' => SaasPlan::query()->where('ativo', true)->orderBy('nome')->get(),
        ]);
    }

    public function update(UpdateSaasChargeRequest $request, SaasCharge $charge): RedirectResponse
    {
        $this->ensureSuperAdmin();

        $charge->update($request->validatedPayload());

        return redirect()
            ->route('financeiro.saas.cobrancas', $this->filterQuery($request))
            ->with('status', 'Cobrança atualizada.');
    }

    public function destroy(SaasCharge $charge): RedirectResponse
    {
        $this->ensureSuperAdmin();

        $charge->delete();

        return redirect()
            ->route('financeiro.saas.cobrancas', $this->filterQuery(request()))
            ->with('status', 'Cobrança excluída.');
    }

    /**
     * @return array<string, mixed>
     */
    private function filterQuery(Request $request): array
    {
        return array_filter(
            $request->only(['empresa', 'situacao', 'status', 'plano', 'vencimento_de', 'vencimento_ate']),
            static fn ($v) => $v !== null && $v !== ''
        );
    }

    private function ensureSuperAdmin(): void
    {
        abort_unless(auth()->user()?->isSuperAdmin(), 403);
    }
}
