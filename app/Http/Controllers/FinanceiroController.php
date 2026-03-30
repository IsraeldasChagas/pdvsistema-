<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFixedExpenseRequest;
use App\Models\FixedExpense;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class FinanceiroController extends Controller
{
    public function despesasFixas(): View
    {
        $rows = FixedExpense::query()->orderByDesc('created_at')->limit(200)->get();

        return view('paginas.financeiro.despesas-fixas', [
            'rows' => $rows,
        ]);
    }

    public function despesasVariaveis(): View
    {
        return view('paginas.financeiro.despesas-variaveis');
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
            'categoria' => $data['categoria'] ?? null,
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
