<?php

namespace App\Http\Controllers;

use App\Models\CashSale;
use App\Models\Commission;
use App\Models\PdvSetting;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class RelatorioController extends Controller
{
    private const ABAS = ['resumo', 'vendas', 'produtos', 'vendedores', 'estoque'];

    public function index(Request $request): View
    {
        $defaultInicio = now()->startOfMonth()->startOfDay();
        $defaultFim = now()->endOfDay();

        $inicio = $this->parseBrDateStart($request->input('data_inicio'), $defaultInicio);
        $fim = $this->parseBrDateEnd($request->input('data_fim'), $defaultFim);

        if ($inicio->gt($fim)) {
            [$inicio, $fim] = [$fim->copy()->startOfDay(), $inicio->copy()->endOfDay()];
        }

        $aba = $request->string('aba')->toString();
        if (! in_array($aba, self::ABAS, true)) {
            $aba = 'resumo';
        }

        $dataInicioFmt = $inicio->format('d/m/Y');
        $dataFimFmt = $fim->format('d/m/Y');

        $totalVendas = (float) CashSale::query()
            ->whereBetween('created_at', [$inicio, $fim])
            ->sum('total');

        $qtdVendas = CashSale::query()
            ->whereBetween('created_at', [$inicio, $fim])
            ->count();

        $totalComissoes = (float) Commission::query()
            ->whereBetween('created_at', [$inicio, $fim])
            ->sum('valor');

        $ticketMedio = $qtdVendas > 0 ? round($totalVendas / $qtdVendas, 2) : 0.0;

        $listaVendas = CashSale::query()
            ->with('user')
            ->whereBetween('created_at', [$inicio, $fim])
            ->orderByDesc('created_at')
            ->limit(500)
            ->get();

        $produtosRanking = DB::table('cash_sale_items')
            ->join('cash_sales', 'cash_sale_items.cash_sale_id', '=', 'cash_sales.id')
            ->whereBetween('cash_sales.created_at', [$inicio, $fim])
            ->select(
                'cash_sale_items.product_id',
                DB::raw('SUM(cash_sale_items.quantidade) as qtd_total'),
                DB::raw('SUM(cash_sale_items.subtotal) as receita')
            )
            ->groupBy('cash_sale_items.product_id')
            ->orderByDesc('qtd_total')
            ->limit(50)
            ->get();

        $productIds = $produtosRanking->pluck('product_id')->filter()->all();
        $produtosPorId = Product::query()
            ->whereIn('id', $productIds)
            ->get()
            ->keyBy('id');

        $vendedoresRanking = DB::table('cash_sales')
            ->whereBetween('created_at', [$inicio, $fim])
            ->select(
                'user_id',
                DB::raw('COUNT(*) as n_vendas'),
                DB::raw('SUM(total) as total_rs')
            )
            ->groupBy('user_id')
            ->orderByDesc('total_rs')
            ->get();

        $userIds = $vendedoresRanking->pluck('user_id')->filter()->all();
        $usersPorId = User::query()
            ->whereIn('id', $userIds)
            ->get()
            ->keyBy('id');

        $estoqueMinPadrao = PdvSetting::current()->estoque_min;

        $estoqueBaixo = Product::query()
            ->with('category')
            ->where('status', 'ativo')
            ->where('estoque', '<=', (int) $request->input('estoque_min', $estoqueMinPadrao))
            ->orderBy('estoque')
            ->orderBy('nome')
            ->limit(100)
            ->get();

        $periodoLabel = $inicio->format('Y-m-d').' a '.$fim->format('Y-m-d');

        return view('paginas.relatorios', [
            'aba' => $aba,
            'dataInicioFmt' => $dataInicioFmt,
            'dataFimFmt' => $dataFimFmt,
            'periodoLabel' => $periodoLabel,
            'inicio' => $inicio,
            'fim' => $fim,
            'totalVendas' => $totalVendas,
            'qtdVendas' => $qtdVendas,
            'totalComissoes' => $totalComissoes,
            'ticketMedio' => $ticketMedio,
            'listaVendas' => $listaVendas,
            'produtosRanking' => $produtosRanking,
            'produtosPorId' => $produtosPorId,
            'vendedoresRanking' => $vendedoresRanking,
            'usersPorId' => $usersPorId,
            'estoqueBaixo' => $estoqueBaixo,
            'estoqueMinFiltro' => (int) $request->input('estoque_min', $estoqueMinPadrao),
            'formasPagamento' => PdvSetting::current()->formasPagamentoParaRelatorios(),
        ]);
    }

    private function parseBrDateStart(?string $raw, Carbon $default): Carbon
    {
        if ($raw === null || trim($raw) === '') {
            return $default->copy();
        }
        try {
            return Carbon::createFromFormat('d/m/Y', trim($raw))->startOfDay();
        } catch (\Throwable) {
            return $default->copy();
        }
    }

    private function parseBrDateEnd(?string $raw, Carbon $default): Carbon
    {
        if ($raw === null || trim($raw) === '') {
            return $default->copy();
        }
        try {
            return Carbon::createFromFormat('d/m/Y', trim($raw))->endOfDay();
        } catch (\Throwable) {
            return $default->copy();
        }
    }
}
