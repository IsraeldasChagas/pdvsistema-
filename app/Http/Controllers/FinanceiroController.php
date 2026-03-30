<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFixedExpenseRequest;
use App\Http\Requests\StoreCashFlowEntryRequest;
use App\Http\Requests\StoreVariableExpenseRequest;
use App\Models\CashFlowEntry;
use App\Models\FixedExpense;
use App\Models\FixedExpenseCategory;
use App\Models\VariableExpense;
use App\Models\VariableExpenseCategory;
use App\Services\FluxoCaixaService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class FinanceiroController extends Controller
{
    public function despesasFixas(): View
    {
        $rows = FixedExpense::query()
            ->with('category')
            ->orderByDesc('created_at')
            ->limit(200)
            ->get();

        $categorias = FixedExpenseCategory::query()->orderBy('nome')->get(['id', 'nome']);

        return view('paginas.financeiro.despesas-fixas', [
            'rows' => $rows,
            'categorias' => $categorias,
        ]);
    }

    public function fluxoCaixa(Request $request): View
    {
        $fim = $request->filled('fim')
            ? Carbon::parse((string) $request->input('fim'))->endOfDay()
            : Carbon::now()->endOfDay();
        $inicio = $request->filled('inicio')
            ? Carbon::parse((string) $request->input('inicio'))->startOfDay()
            : Carbon::now()->copy()->startOfMonth()->startOfDay();

        if ($inicio->gt($fim)) {
            $inicio = $fim->copy()->startOfMonth()->startOfDay();
        }

        $maxDays = 370;
        if ($inicio->diffInDays($fim) > $maxDays) {
            $inicio = $fim->copy()->subDays($maxDays)->startOfDay();
        }

        $fluxo = (new FluxoCaixaService)->build($inicio, $fim);

        $lancamentos = CashFlowEntry::query()
            ->with('user')
            ->whereBetween('data_movimento', [$inicio->toDateString(), $fim->toDateString()])
            ->orderByDesc('data_movimento')
            ->orderByDesc('created_at')
            ->limit(200)
            ->get();

        return view('paginas.financeiro.fluxo-caixa', [
            'fluxo' => $fluxo,
            'inicio' => $inicio->toDateString(),
            'fim' => $fim->toDateString(),
            'lancamentos' => $lancamentos,
        ]);
    }

    public function storeLancamentoManual(StoreCashFlowEntryRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $valor = $this->parseBrMoney($data['valor'] ?? '0');
        if ($valor <= 0) {
            return back()->withInput()->withErrors(['valor' => 'Informe um valor maior que zero.']);
        }

        CashFlowEntry::query()->create([
            'user_id' => auth()->id(),
            'tipo' => $data['tipo'],
            'data_movimento' => $data['data_movimento'],
            'valor' => $valor,
            'categoria' => $data['categoria'] ?? null,
            'origem' => $data['origem'] ?? null,
            'descricao' => $data['descricao'],
            'observacoes' => $data['observacoes'] ?? null,
        ]);

        return redirect()
            ->route('financeiro.fluxo_caixa', $request->only(['inicio', 'fim']))
            ->with('status', 'Lançamento manual criado.');
    }

    public function destroyLancamentoManual(CashFlowEntry $entry, Request $request): RedirectResponse
    {
        $entry->delete();

        return redirect()
            ->route('financeiro.fluxo_caixa', $request->only(['inicio', 'fim']))
            ->with('status', 'Lançamento removido.');
    }

    public function despesasVariaveis(): View
    {
        $rows = VariableExpense::query()
            ->with('category')
            ->orderByDesc('data_despesa')
            ->orderByDesc('created_at')
            ->limit(200)
            ->get();

        $categorias = VariableExpenseCategory::query()->orderBy('nome')->get(['id', 'nome']);

        return view('paginas.financeiro.despesas-variaveis', [
            'rows' => $rows,
            'categorias' => $categorias,
        ]);
    }

    public function storeCategoriaDespesasVariaveis(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'nome' => ['required', 'string', 'max:80'],
            'cor' => ['nullable', 'string', 'max:16'],
        ], [
            'nome.required' => 'Informe o nome da categoria.',
        ]);

        $cat = VariableExpenseCategory::query()->create([
            'nome' => $data['nome'],
            'cor' => $data['cor'] ?? null,
        ]);

        return redirect()
            ->route('financeiro.despesas_variaveis')
            ->with('status', 'Categoria criada.')
            ->with('select_variable_expense_category_id', $cat->id);
    }

    public function storeDespesasVariaveis(StoreVariableExpenseRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $valor = $this->parseBrMoney($data['valor'] ?? '0');
        if ($valor <= 0) {
            return back()->withInput()->withErrors(['valor' => 'Informe um valor maior que zero.']);
        }

        $anexoPath = null;
        if ($request->hasFile('anexo')) {
            $file = $request->file('anexo');
            if ($file && $file->isValid()) {
                $ext = strtolower($file->getClientOriginalExtension() ?: $file->guessExtension() ?: 'bin');
                $ext = preg_replace('/[^a-z0-9]/', '', $ext) ?: 'bin';
                $name = 'variable-expenses/'.Str::uuid()->toString().'.'.$ext;
                $stored = Storage::disk('pdv_public')->putFileAs('', $file, $name);
                $anexoPath = $stored ?: null;
            }
        }

        VariableExpense::query()->create([
            'descricao' => $data['descricao'],
            'variable_expense_category_id' => $data['variable_expense_category_id'] ?? null,
            'valor' => $valor,
            'data_despesa' => $data['data_despesa'],
            'forma_pagamento' => $data['forma_pagamento'] ?? null,
            'fornecedor_nome' => $data['fornecedor_nome'] ?? null,
            'fornecedor_doc' => $data['fornecedor_doc'] ?? null,
            'centro_custo' => $data['centro_custo'] ?? null,
            'conta' => $data['conta'] ?? null,
            'observacoes' => $data['observacoes'] ?? null,
            'anexo_path' => $anexoPath,
        ]);

        return redirect()
            ->route('financeiro.despesas_variaveis')
            ->with('status', 'Despesa variável cadastrada.');
    }

    public function categoriasDespesasFixas(): View
    {
        $rows = FixedExpenseCategory::query()->orderBy('nome')->get();

        return view('paginas.financeiro.categorias-despesas-fixas', [
            'rows' => $rows,
        ]);
    }

    public function storeCategoriaDespesasFixas(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'nome' => ['required', 'string', 'max:80'],
            'cor' => ['nullable', 'string', 'max:16'],
        ], [
            'nome.required' => 'Informe o nome da categoria.',
        ]);

        $cat = FixedExpenseCategory::query()->create([
            'nome' => $data['nome'],
            'cor' => $data['cor'] ?? null,
        ]);

        return redirect()
            ->route('financeiro.despesas_fixas')
            ->with('status', 'Categoria criada.')
            ->with('select_fixed_expense_category_id', $cat->id);
    }

    public function storeDespesasFixas(StoreFixedExpenseRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $valor = $this->parseBrMoney($data['valor'] ?? '0');
        if ($valor <= 0) {
            return back()->withInput()->withErrors(['valor' => 'Informe um valor maior que zero.']);
        }

        if (($data['periodicidade'] ?? '') === 'a_cada_x_dias' && empty($data['intervalo'])) {
            return back()->withInput()->withErrors(['intervalo' => 'Informe o intervalo (dias) para “a cada X dias”.']);
        }

        if (($data['periodicidade'] ?? '') === 'mensal' && empty($data['dia_vencimento'])) {
            return back()->withInput()->withErrors(['dia_vencimento' => 'Informe o dia do vencimento para despesas mensais.']);
        }

        $anexoPath = null;
        if ($request->hasFile('anexo')) {
            $file = $request->file('anexo');
            if ($file && $file->isValid()) {
                $ext = strtolower($file->getClientOriginalExtension() ?: $file->guessExtension() ?: 'bin');
                $ext = preg_replace('/[^a-z0-9]/', '', $ext) ?: 'bin';
                $name = 'fixed-expenses/'.Str::uuid()->toString().'.'.$ext;
                $stored = Storage::disk('pdv_public')->putFileAs('', $file, $name);
                $anexoPath = $stored ?: null;
            }
        }

        FixedExpense::query()->create([
            'descricao' => $data['descricao'],
            'fixed_expense_category_id' => $data['fixed_expense_category_id'] ?? null,
            'valor' => $valor,
            'periodicidade' => $data['periodicidade'],
            'intervalo' => $data['intervalo'] ?? null,
            'dia_vencimento' => $data['dia_vencimento'] ?? null,
            'data_inicio' => $data['data_inicio'] ?? null,
            'status' => $data['status'],
            'forma_pagamento' => $data['forma_pagamento'] ?? null,
            'fornecedor_nome' => $data['fornecedor_nome'] ?? null,
            'fornecedor_doc' => $data['fornecedor_doc'] ?? null,
            'centro_custo' => $data['centro_custo'] ?? null,
            'conta' => $data['conta'] ?? null,
            'alerta_dias' => $data['alerta_dias'] ?? null,
            'observacoes' => $data['observacoes'] ?? null,
            'anexo_path' => $anexoPath,
        ]);

        return redirect()
            ->route('financeiro.despesas_fixas')
            ->with('status', 'Despesa fixa cadastrada.');
    }

    private function parseBrMoney(string $raw): float
    {
        $s = trim((string) $raw);
        $s = str_replace(['R$', 'r$', ' '], '', $s);
        if (str_contains($s, ',')) {
            $s = str_replace('.', '', $s);
            $s = str_replace(',', '.', $s);
        }

        $n = (float) preg_replace('/[^0-9.\\-]/', '', $s);

        return round($n, 2);
    }
}
