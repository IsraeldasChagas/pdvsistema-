<?php

namespace App\Http\Controllers;

use App\Models\Commission;
use App\Models\User;
use App\Support\CurrentCompany;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CommissionController extends Controller
{
    public function index(Request $request): View
    {
        $base = Commission::query();
        if ($request->filled('vendedor')) {
            $base->where('user_id', $request->integer('vendedor'));
        }

        $aPagar = (clone $base)->where('status', Commission::STATUS_PENDENTE)->sum('valor');
        $jaPagas = (clone $base)->where('status', Commission::STATUS_PAGO)->sum('valor');
        $totalGeral = (clone $base)->sum('valor');

        $query = Commission::query()
            ->with(['user', 'cashSale'])
            ->orderByDesc('created_at');

        if ($request->filled('vendedor')) {
            $query->where('user_id', $request->integer('vendedor'));
        }

        $statusFiltro = $request->string('status')->toString() ?: 'pendentes';
        if ($statusFiltro === 'pendentes') {
            $query->where('status', Commission::STATUS_PENDENTE);
        } elseif ($statusFiltro === 'pagas') {
            $query->where('status', Commission::STATUS_PAGO);
        }

        $comissoes = $query->get();
        $vendedores = User::query()
            ->where('company_id', CurrentCompany::id())
            ->where('role', '!=', 'super_admin')
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('paginas.comissoes.index', [
            'comissoes' => $comissoes,
            'vendedores' => $vendedores,
            'aPagar' => (float) $aPagar,
            'jaPagas' => (float) $jaPagas,
            'totalGeral' => (float) $totalGeral,
            'statusFiltro' => $statusFiltro,
        ]);
    }

    public function create(): View
    {
        $vendedores = User::query()
            ->where('company_id', CurrentCompany::id())
            ->where('role', 'vendedor')
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('paginas.comissoes.create', compact('vendedores'));
    }

    public function store(Request $request): RedirectResponse
    {
        $cid = CurrentCompany::id();
        $request->validate([
            'user_id' => [
                'required',
                Rule::exists('users', 'id')->where(fn ($q) => $q->where('company_id', $cid)),
            ],
            'valor' => ['required', 'string', 'max:32'],
            'percentual' => ['nullable', 'string', 'max:16'],
        ], [
            'user_id.required' => 'Selecione o vendedor.',
        ]);

        $valor = $this->parseBrMoney($request->input('valor', '0'));
        if ($valor <= 0) {
            return back()->withInput()->withErrors(['valor' => 'Informe um valor maior que zero.']);
        }

        $percentual = null;
        if ($request->filled('percentual') && trim((string) $request->input('percentual')) !== '') {
            $percentual = $this->parseOptionalPercent($request->input('percentual'));
            if ($percentual === null) {
                return back()->withInput()->withErrors(['percentual' => 'Informe um percentual entre 0 e 100.']);
            }
        }

        Commission::query()->create([
            'user_id' => (int) $request->input('user_id'),
            'valor' => $valor,
            'percentual' => $percentual,
            'status' => Commission::STATUS_PENDENTE,
            'origem' => 'manual',
            'created_by' => auth()->id(),
        ]);

        return redirect()
            ->route('modulos.comissoes')
            ->with('success', 'Comissão cadastrada.');
    }

    public function marcarPago(Request $request, Commission $commission): RedirectResponse
    {
        if ($commission->status !== Commission::STATUS_PENDENTE) {
            return back()->withErrors(['comissao' => 'Esta comissão já está paga.']);
        }

        $commission->update([
            'status' => Commission::STATUS_PAGO,
            'paid_at' => now(),
        ]);

        $params = [];
        if ($request->filled('redirect_status')) {
            $params['status'] = $request->input('redirect_status');
        }
        if ($request->filled('redirect_vendedor')) {
            $params['vendedor'] = $request->input('redirect_vendedor');
        }

        return redirect()
            ->route('modulos.comissoes', $params)
            ->with('success', 'Comissão marcada como paga.');
    }

    public function marcarPagoLote(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['integer', 'exists:commissions,id'],
        ]);

        $updated = Commission::query()
            ->whereIn('id', $validated['ids'])
            ->where('status', Commission::STATUS_PENDENTE)
            ->update([
                'status' => Commission::STATUS_PAGO,
                'paid_at' => now(),
            ]);

        if ($updated === 0) {
            return redirect()
                ->route('modulos.comissoes', $request->only(['status', 'vendedor']))
                ->withErrors(['lote' => 'Nenhuma comissão pendente foi selecionada.']);
        }

        return redirect()
            ->route('modulos.comissoes', $request->only(['status', 'vendedor']))
            ->with('success', $updated === 1
                ? '1 comissão marcada como paga.'
                : $updated.' comissões marcadas como pagas.');
    }

    private function parseBrMoney(string $raw): float
    {
        $s = trim($raw);
        $s = preg_replace('/[^\d,.\-]/', '', $s) ?? '';
        if ($s === '' || $s === '-') {
            return 0.0;
        }
        if (str_contains($s, ',')) {
            $s = str_replace('.', '', $s);
            $s = str_replace(',', '.', $s);
        }

        return round((float) $s, 2);
    }

    private function parseOptionalPercent(?string $raw): ?float
    {
        if ($raw === null || trim($raw) === '') {
            return null;
        }
        $s = trim($raw);
        $s = preg_replace('/[^\d,.\-]/', '', $s) ?? '';
        if ($s === '' || $s === '-') {
            return null;
        }
        if (str_contains($s, ',')) {
            $s = str_replace('.', '', $s);
            $s = str_replace(',', '.', $s);
        }
        $n = round((float) $s, 2);
        if ($n < 0 || $n > 100) {
            return null;
        }

        return $n;
    }
}
