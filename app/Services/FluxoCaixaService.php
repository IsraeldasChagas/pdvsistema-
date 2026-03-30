<?php

namespace App\Services;

use App\Models\CashSale;
use App\Models\FixedExpense;
use App\Models\VariableExpense;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class FluxoCaixaService
{
    /**
     * @return array{
     *   labels: list<string>,
     *   labels_short: list<string>,
     *   entradas: list<float>,
     *   saidas_variaveis: list<float>,
     *   saidas_fixas_estimadas: list<float>,
     *   saidas_total: list<float>,
     *   saldo_acumulado: list<float>,
     *   totais: array{entradas: float, saidas_variaveis: float, saidas_fixas: float, saidas: float, saldo: float},
     *   categorias_saidas: list<array{label: string, valor: float}>,
     *   formas_entrada: list<array{label: string, valor: float}>,
     *   notas: list<string>
     * }
     */
    public function build(Carbon $inicio, Carbon $fim): array
    {
        $inicio = $inicio->copy()->startOfDay();
        $fim = $fim->copy()->endOfDay();

        $daily = [];
        for ($d = $inicio->copy(); $d->lte($fim); $d->addDay()) {
            $k = $d->format('Y-m-d');
            $daily[$k] = [
                'entradas' => 0.0,
                'saidas_variaveis' => 0.0,
                'saidas_fixas_estimadas' => 0.0,
            ];
        }

        $this->addCashSales($inicio, $fim, $daily);
        $this->addVariableExpenses($inicio, $fim, $daily);
        $this->addFixedMonthlyEstimates($inicio, $fim, $daily);

        $labels = [];
        $labelsShort = [];
        $entradas = [];
        $saidasVar = [];
        $saidasFix = [];
        $saidasTotal = [];
        $saldoAcum = [];
        $acum = 0.0;

        ksort($daily);

        foreach ($daily as $date => $row) {
            $labels[] = $date;
            $labelsShort[] = Carbon::parse($date)->format('d/m');
            $e = round($row['entradas'], 2);
            $sv = round($row['saidas_variaveis'], 2);
            $sf = round($row['saidas_fixas_estimadas'], 2);
            $st = round($sv + $sf, 2);
            $entradas[] = $e;
            $saidasVar[] = $sv;
            $saidasFix[] = $sf;
            $saidasTotal[] = $st;
            $acum += $e - $st;
            $saldoAcum[] = round($acum, 2);
        }

        $totEnt = round(array_sum($entradas), 2);
        $totSv = round(array_sum($saidasVar), 2);
        $totSf = round(array_sum($saidasFix), 2);
        $totSaidas = round($totSv + $totSf, 2);

        return [
            'labels' => $labels,
            'labels_short' => $labelsShort,
            'entradas' => $entradas,
            'saidas_variaveis' => $saidasVar,
            'saidas_fixas_estimadas' => $saidasFix,
            'saidas_total' => $saidasTotal,
            'saldo_acumulado' => $saldoAcum,
            'totais' => [
                'entradas' => $totEnt,
                'saidas_variaveis' => $totSv,
                'saidas_fixas' => $totSf,
                'saidas' => $totSaidas,
                'saldo' => round($totEnt - $totSaidas, 2),
            ],
            'categorias_saidas' => $this->categoriasVariaveis($inicio, $fim),
            'formas_entrada' => $this->formasEntrada($inicio, $fim),
            'notas' => $this->notas(),
        ];
    }

    /**
     * @param array<string, array{entradas: float, saidas_variaveis: float, saidas_fixas_estimadas: float}> $daily
     */
    private function addCashSales(Carbon $inicio, Carbon $fim, array &$daily): void
    {
        CashSale::query()
            ->whereBetween('created_at', [$inicio, $fim])
            ->get(['total', 'created_at'])
            ->each(function ($sale) use (&$daily): void {
                $k = $sale->created_at->format('Y-m-d');
                if (! isset($daily[$k])) {
                    return;
                }
                $daily[$k]['entradas'] += (float) $sale->total;
            });
    }

    /**
     * @param array<string, array{entradas: float, saidas_variaveis: float, saidas_fixas_estimadas: float}> $daily
     */
    private function addVariableExpenses(Carbon $inicio, Carbon $fim, array &$daily): void
    {
        VariableExpense::query()
            ->whereBetween('data_despesa', [$inicio->toDateString(), $fim->toDateString()])
            ->get(['valor', 'data_despesa'])
            ->each(function ($row) use (&$daily): void {
                $k = $row->data_despesa->format('Y-m-d');
                if (! isset($daily[$k])) {
                    return;
                }
                $daily[$k]['saidas_variaveis'] += (float) $row->valor;
            });
    }

    /**
     * Despesas fixas mensais ativas: 1 ocorrência por mês no dia de vencimento (estimativa).
     *
     * @param array<string, array{entradas: float, saidas_variaveis: float, saidas_fixas_estimadas: float}> $daily
     */
    private function addFixedMonthlyEstimates(Carbon $inicio, Carbon $fim, array &$daily): void
    {
        $fixas = FixedExpense::query()
            ->where('status', 'ativo')
            ->where('periodicidade', 'mensal')
            ->whereNotNull('dia_vencimento')
            ->get(['valor', 'dia_vencimento', 'data_inicio']);

        $startMonth = $inicio->copy()->startOfMonth();
        $endMonth = $fim->copy()->startOfMonth();

        foreach ($fixas as $fe) {
            $m = $startMonth->copy();
            while ($m->lte($endMonth)) {
                $lastDay = $m->daysInMonth;
                $day = min((int) $fe->dia_vencimento, $lastDay);
                $occ = $m->copy()->day($day)->startOfDay();

                if ($fe->data_inicio && $occ->lt($fe->data_inicio->startOfDay())) {
                    $m->addMonth();

                    continue;
                }

                if ($occ->between($inicio, $fim)) {
                    $k = $occ->format('Y-m-d');
                    if (isset($daily[$k])) {
                        $daily[$k]['saidas_fixas_estimadas'] += (float) $fe->valor;
                    }
                }
                $m->addMonth();
            }
        }
    }

    /**
     * @return list<array{label: string, valor: float}>
     */
    private function categoriasVariaveis(Carbon $inicio, Carbon $fim): array
    {
        $rows = VariableExpense::query()
            ->with('category')
            ->whereBetween('data_despesa', [$inicio->toDateString(), $fim->toDateString()])
            ->get();

        /** @var Collection<string, float> */
        $map = $rows->groupBy(function ($r) {
            return $r->category?->nome ?? 'Sem categoria';
        })->map(fn (Collection $g) => round((float) $g->sum('valor'), 2));

        return $map->sortDesc()->map(fn (float $v, string $label) => ['label' => $label, 'valor' => $v])->values()->all();
    }

    /**
     * @return list<array{label: string, valor: float}>
     */
    private function formasEntrada(Carbon $inicio, Carbon $fim): array
    {
        $rows = CashSale::query()
            ->whereBetween('created_at', [$inicio, $fim])
            ->get(['total', 'forma_pagamento']);

        /** @var Collection<string, float> */
        $map = $rows->groupBy(function ($r) {
            $f = $r->forma_pagamento;

            return $f !== null && trim((string) $f) !== '' ? trim((string) $f) : 'Não informado';
        })->map(fn (Collection $g) => round((float) $g->sum('total'), 2));

        return $map->sortDesc()->map(fn (float $v, string $label) => ['label' => $label, 'valor' => $v])->values()->all();
    }

    /**
     * @return list<string>
     */
    private function notas(): array
    {
        return [
            'Entradas: vendas registradas no caixa (PDV).',
            'Saídas variáveis: lançamentos pela data da despesa.',
            'Saídas fixas: estimativa mensal (apenas periodicidade “Mensal”, status Ativo), no dia de vencimento.',
            'Despesas fixas semanais/anuais/outras não entram neste gráfico.',
        ];
    }
}
