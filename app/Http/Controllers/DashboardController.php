<?php

namespace App\Http\Controllers;

use App\Models\CashRegisterSession;
use App\Models\CashSale;
use App\Models\Commission;
use App\Models\Product;
use App\Support\CurrentCompany;
use Carbon\Carbon;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $companyId = CurrentCompany::id();
        if ($companyId === null) {
            abort(403, 'Nenhuma empresa no contexto.');
        }

        $today = Carbon::today();

        $minhasVendasTotal = (float) CashSale::query()
            ->whereDate('created_at', $today)
            ->where('user_id', auth()->id())
            ->sum('total');
        $minhasVendasCount = (int) CashSale::query()
            ->whereDate('created_at', $today)
            ->where('user_id', auth()->id())
            ->count();

        $totalDia = (float) CashSale::query()
            ->whereDate('created_at', $today)
            ->sum('total');
        $vendasDiaCount = (int) CashSale::query()
            ->whereDate('created_at', $today)
            ->count();

        $produtosCount = (int) Product::query()->count();
        $itensEstoque = (int) Product::query()->sum('estoque');

        $caixaAberta = CashRegisterSession::query()
            ->where('user_id', auth()->id())
            ->whereNull('closed_at')
            ->orderByDesc('opened_at')
            ->first();

        $caixaLabel = 'Fechado';
        $caixaValor = null;
        if ($caixaAberta !== null) {
            $caixaLabel = 'Aberto';
            $totalVendasSessao = round((float) $caixaAberta->cashSales()->sum('total'), 2);
            $caixaValor = round((float) $caixaAberta->valor_abertura + $totalVendasSessao, 2);
        }

        $comissoesPendentes = (float) Commission::query()
            ->where('user_id', auth()->id())
            ->where('status', Commission::STATUS_PENDENTE)
            ->sum('valor');

        return view('paginas.dashboard', [
            'minhasVendasTotal' => $minhasVendasTotal,
            'minhasVendasCount' => $minhasVendasCount,
            'totalDia' => $totalDia,
            'vendasDiaCount' => $vendasDiaCount,
            'produtosCount' => $produtosCount,
            'itensEstoque' => $itensEstoque,
            'caixaLabel' => $caixaLabel,
            'caixaValor' => $caixaValor,
            'comissoesPendentes' => $comissoesPendentes,
        ]);
    }
}
