<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\SaasCharge;
use App\Models\SaasPlan;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class FinanceiroSaasController extends Controller
{
    public function dashboard(Request $request): View
    {
        return $this->renderDashboard($request);
    }

    public function cobrancas(Request $request): View
    {
        return $this->renderCobrancasPage($request);
    }

    public function financeiroEmpresas(): View
    {
        $companies = Company::query()->with('saasPlan:id,nome')->orderBy('nome')->get();
        $ids = $companies->pluck('id');

        $pendingByCompany = $ids->isEmpty()
            ? collect()
            : SaasCharge::query()
                ->with('saasPlan')
                ->whereIn('company_id', $ids)
                ->where('status', SaasCharge::STATUS_PENDENTE)
                ->orderBy('vencimento')
                ->orderBy('id')
                ->get()
                ->groupBy('company_id')
                ->map(fn (Collection $group) => $group->first());

        $latestByCompany = $ids->isEmpty()
            ? collect()
            : SaasCharge::query()
                ->with('saasPlan')
                ->whereIn('company_id', $ids)
                ->get()
                ->groupBy('company_id')
                ->map(fn (Collection $group) => $group->sortByDesc('id')->first());

        $rows = $companies->map(function (Company $company) use ($pendingByCompany, $latestByCompany) {
            /** @var SaasCharge|null $pending */
            $pending = $pendingByCompany->get($company->id);
            /** @var SaasCharge|null $latest */
            $latest = $latestByCompany->get($company->id);

            $plano = $company->saasPlan?->nome ?? $pending?->saasPlan?->nome ?? $latest?->saasPlan?->nome;

            $valorRef = $pending ?? $latest;
            $valorLabel = $valorRef !== null
                ? 'R$ '.number_format((float) $valorRef->valor, 2, ',', '.')
                : 'R$ —';

            $vencimentoLabel = $pending !== null
                ? 'Dia '.$pending->vencimento->day
                : 'Dia —';

            return (object) [
                'company' => $company,
                'plano' => $plano,
                'valor_label' => $valorLabel,
                'vencimento_label' => $vencimentoLabel,
            ];
        });

        return view('paginas.financeiro-saas-empresas', [
            'rows' => $rows,
        ]);
    }

    public function financeiroEmpresaShow(Company $empresa): View
    {
        $charges = SaasCharge::query()
            ->with('saasPlan')
            ->where('company_id', $empresa->id)
            ->orderByDesc('vencimento')
            ->orderByDesc('id')
            ->limit(50)
            ->get();

        $nextPending = SaasCharge::query()
            ->with('saasPlan')
            ->where('company_id', $empresa->id)
            ->where('status', SaasCharge::STATUS_PENDENTE)
            ->orderBy('vencimento')
            ->first();

        return view('paginas.financeiro-saas-empresa-show', [
            'empresa' => $empresa,
            'charges' => $charges,
            'nextPending' => $nextPending,
        ]);
    }

    private function renderDashboard(Request $request): View
    {
        $companies = Company::query()->orderBy('nome')->get(['id', 'nome', 'ativo', 'billing_blocked']);
        $planos = SaasPlan::query()->where('ativo', true)->orderBy('nome')->get();

        $chargesQuery = SaasCharge::query()->with(['company', 'saasPlan']);
        $this->applyChargeFilters($chargesQuery, $request, true);

        $charges = (clone $chargesQuery)
            ->orderByDesc('vencimento')
            ->orderByDesc('id')
            ->limit(100)
            ->get();

        $kpis = $this->computeKpis($request);

        return view('paginas.financeiro-saas-dashboard', [
            'companies' => $companies,
            'planos' => $planos,
            'charges' => $charges,
            'kpis' => $kpis,
        ]);
    }

    private function renderCobrancasPage(Request $request): View
    {
        $companies = Company::query()->orderBy('nome')->get(['id', 'nome', 'ativo', 'billing_blocked']);
        $planos = SaasPlan::query()->where('ativo', true)->orderBy('nome')->get();

        $chargesQuery = SaasCharge::query()->with(['company', 'saasPlan']);
        $this->applyChargeFilters($chargesQuery, $request, false);

        $charges = (clone $chargesQuery)
            ->orderByDesc('vencimento')
            ->orderByDesc('id')
            ->limit(100)
            ->get();

        return view('paginas.financeiro-saas-cobrancas', [
            'companies' => $companies,
            'planos' => $planos,
            'charges' => $charges,
        ]);
    }

    /**
     * @param  Builder<SaasCharge>  $query
     */
    private function applyChargeFilters(Builder $query, Request $request, bool $includeDateRange): void
    {
        if ($request->filled('empresa')) {
            $query->where('company_id', (int) $request->input('empresa'));
        }

        if ($request->filled('plano')) {
            $query->where('saas_plan_id', (int) $request->input('plano'));
        }

        if ($includeDateRange) {
            if ($request->filled('vencimento_de')) {
                $query->whereDate('vencimento', '>=', $request->input('vencimento_de'));
            }

            if ($request->filled('vencimento_ate')) {
                $query->whereDate('vencimento', '<=', $request->input('vencimento_ate'));
            }
        }

        $situacao = $request->input('situacao');
        if ($situacao === 'regular') {
            $query->where(function (Builder $q): void {
                $q->where('status', SaasCharge::STATUS_PAGO)
                    ->orWhere(function (Builder $q2): void {
                        $q2->where('status', SaasCharge::STATUS_PENDENTE)
                            ->whereDate('vencimento', '>=', today());
                    });
            });
        } elseif ($situacao === 'em_atraso') {
            $query->where('status', SaasCharge::STATUS_PENDENTE)
                ->whereDate('vencimento', '<', today());
        }

        $status = $request->input('status');
        if ($status === 'pago') {
            $query->where('status', SaasCharge::STATUS_PAGO);
        } elseif ($status === 'pendente') {
            $query->where('status', SaasCharge::STATUS_PENDENTE)
                ->whereDate('vencimento', '>=', today());
        } elseif ($status === 'vencido') {
            $query->where('status', SaasCharge::STATUS_PENDENTE)
                ->whereDate('vencimento', '<', today());
        }
    }

    /**
     * @return array{
     *     recebido_mes: float|int|string,
     *     pendente: float|int|string,
     *     vencido: float|int|string,
     *     empresas_ativas: int,
     *     bloqueadas: int,
     *     vence_hoje: int,
     *     em_atraso: int
     * }
     */
    private function computeKpis(Request $request): array
    {
        $base = SaasCharge::query();
        $this->applyChargeFilters($base, $request, true);

        $startMonth = now()->copy()->startOfMonth();
        $endMonth = now()->copy()->endOfMonth();

        $recebidoMes = (clone $base)
            ->where('status', SaasCharge::STATUS_PAGO)
            ->whereNotNull('pagamento')
            ->whereBetween('pagamento', [$startMonth->toDateString(), $endMonth->toDateString()])
            ->sum('valor');

        $pendente = (clone $base)
            ->where('status', SaasCharge::STATUS_PENDENTE)
            ->whereDate('vencimento', '>=', today())
            ->sum('valor');

        $vencido = (clone $base)
            ->where('status', SaasCharge::STATUS_PENDENTE)
            ->whereDate('vencimento', '<', today())
            ->sum('valor');

        $venceHoje = (clone $base)
            ->where('status', SaasCharge::STATUS_PENDENTE)
            ->whereDate('vencimento', today())
            ->count();

        $emAtraso = (clone $base)
            ->where('status', SaasCharge::STATUS_PENDENTE)
            ->whereDate('vencimento', '<', today())
            ->count();

        $companyQuery = Company::query();
        if ($request->filled('empresa')) {
            $companyQuery->where('id', (int) $request->input('empresa'));
        }

        $empresasAtivas = (clone $companyQuery)->where('ativo', true)->count();
        $bloqueadas = (clone $companyQuery)->where('billing_blocked', true)->count();

        return [
            'recebido_mes' => $recebidoMes,
            'pendente' => $pendente,
            'vencido' => $vencido,
            'empresas_ativas' => $empresasAtivas,
            'bloqueadas' => $bloqueadas,
            'vence_hoje' => $venceHoje,
            'em_atraso' => $emAtraso,
        ];
    }
}
