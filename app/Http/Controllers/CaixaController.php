<?php

namespace App\Http\Controllers;

use App\Models\CashRegisterSession;
use App\Models\CashSale;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CaixaController extends Controller
{
    public function index(): View
    {
        $userId = auth()->id();

        $aberta = CashRegisterSession::query()
            ->where('user_id', $userId)
            ->whereNull('closed_at')
            ->orderByDesc('opened_at')
            ->first();

        $totalVendas = 0.0;
        if ($aberta !== null) {
            $totalVendas = round((float) $aberta->cashSales()->sum('total'), 2);
        }

        $saldoEsperado = $aberta !== null
            ? round((float) $aberta->valor_abertura + $totalVendas, 2)
            : 0.0;

        $historico = CashRegisterSession::query()
            ->where('user_id', $userId)
            ->whereNotNull('closed_at')
            ->with('cashSales')
            ->orderByDesc('closed_at')
            ->limit(50)
            ->get();

        return view('paginas.caixa', [
            'aberta' => $aberta,
            'totalVendas' => $totalVendas,
            'saldoEsperado' => $saldoEsperado,
            'historico' => $historico,
        ]);
    }

    public function abrir(Request $request): RedirectResponse
    {
        if ($this->sessaoAberta()) {
            return back()->withErrors(['valor_abertura' => 'Já existe um caixa aberto. Feche-o antes de abrir outro.']);
        }

        $request->validate([
            'valor_abertura' => 'required|string|max:32',
        ], [
            'valor_abertura.required' => 'Informe o valor de abertura.',
        ]);

        $valor = $this->parseBrMoney($request->input('valor_abertura', '0'));
        if ($valor < 0) {
            return back()->withInput()->withErrors(['valor_abertura' => 'O valor não pode ser negativo.']);
        }

        CashRegisterSession::query()->create([
            'user_id' => auth()->id(),
            'opened_at' => now(),
            'valor_abertura' => $valor,
        ]);

        return redirect()
            ->route('modulos.caixa')
            ->with('success', 'Caixa aberto com sucesso.');
    }

    public function fechar(Request $request): RedirectResponse
    {
        $session = CashRegisterSession::query()
            ->where('user_id', auth()->id())
            ->whereNull('closed_at')
            ->first();

        if ($session === null) {
            return redirect()
                ->route('modulos.caixa')
                ->withErrors(['fechar' => 'Não há caixa aberto para fechar.']);
        }

        $request->validate([
            'valor_fechamento' => 'required|string|max:32',
            'observacao' => 'nullable|string|max:1000',
        ], [
            'valor_fechamento.required' => 'Informe o valor no caixa no fechamento.',
        ]);

        $fechamento = $this->parseBrMoney($request->input('valor_fechamento', '0'));
        if ($fechamento < 0) {
            return back()->withInput()->withErrors(['valor_fechamento' => 'O valor não pode ser negativo.']);
        }

        $totalVendas = round((float) $session->cashSales()->sum('total'), 2);

        $session->update([
            'closed_at' => now(),
            'valor_fechamento' => $fechamento,
            'observacao_fechamento' => $request->input('observacao'),
            'total_vendas_no_fechamento' => $totalVendas,
        ]);

        return redirect()
            ->route('modulos.caixa')
            ->with('success', 'Caixa fechado com sucesso.');
    }

    /**
     * Registra o total de uma venda no caixa aberto (ex.: Mini PDV após finalizar).
     */
    public function registrarVenda(Request $request): JsonResponse|RedirectResponse
    {
        $session = CashRegisterSession::query()
            ->where('user_id', auth()->id())
            ->whereNull('closed_at')
            ->first();

        if ($session === null) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Abra o caixa antes de registrar vendas.'], 422);
            }

            return back()->withErrors(['venda' => 'Abra o caixa antes de registrar vendas.']);
        }

        $validated = $request->validate([
            'total' => ['required', 'numeric', 'min:0.01', 'max:999999.99'],
        ]);

        $t = (float) $validated['total'];
        CashSale::query()->create([
            'cash_register_session_id' => $session->id,
            'user_id' => auth()->id(),
            'subtotal' => $t,
            'desconto' => 0,
            'total' => $t,
            'forma_pagamento' => 'manual',
        ]);

        if ($request->expectsJson()) {
            return response()->json(['ok' => true]);
        }

        return back()->with('success', 'Venda lançada no caixa.');
    }

    private function sessaoAberta(): bool
    {
        return CashRegisterSession::query()
            ->where('user_id', auth()->id())
            ->whereNull('closed_at')
            ->exists();
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
}
