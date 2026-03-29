<?php

namespace App\Http\Controllers;

use App\Models\CashRegisterSession;
use App\Models\CashSale;
use App\Models\CashSaleItem;
use App\Models\Category;
use App\Models\Commission;
use App\Models\PdvSetting;
use App\Models\Product;
use App\Services\EstoqueMovimentoService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Js;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class VendaController extends Controller
{
    public function index(): View
    {
        $produtos = Product::query()
            ->with('category')
            ->where('status', 'ativo')
            ->orderBy('nome')
            ->get();

        $produtosAlpine = $produtos->map(function (Product $p) {
            $preco = (float) $p->preco;

            return [
                'id' => $p->id,
                'nome' => $p->nome,
                'codigo' => $p->codigo,
                'category_id' => $p->category_id,
                'categoria' => $p->category?->nome ?? 'Sem categoria',
                'preco' => $preco,
                'preco_fmt' => number_format($preco, 2, ',', '.'),
                'estoque' => (int) $p->estoque,
            ];
        })->values()->all();

        $categorias = Category::query()
            ->ativa()
            ->orderBy('nome')
            ->get();

        $formasPagamento = PdvSetting::current()->formasPagamentoMap();

        return view('paginas.venda', [
            'categorias' => $categorias,
            'formasPagamento' => $formasPagamento,
            'produtosAlpineJson' => Js::from($produtosAlpine),
        ]);
    }

    public function finalizar(Request $request): JsonResponse
    {
        $session = CashRegisterSession::query()
            ->where('user_id', auth()->id())
            ->whereNull('closed_at')
            ->first();

        if ($session === null) {
            return response()->json(['message' => 'Caixa fechado. Abra o caixa para vender.'], 403);
        }

        $slugsFormas = array_keys(PdvSetting::current()->formasPagamentoMap());

        $validated = $request->validate([
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'integer', 'exists:products,id'],
            'items.*.quantidade' => ['required', 'integer', 'min:1'],
            'desconto' => ['nullable', 'numeric', 'min:0'],
            'forma_pagamento' => ['required', 'string', Rule::in($slugsFormas)],
        ]);

        $desconto = round((float) ($validated['desconto'] ?? 0), 2);

        $mergedQty = [];
        foreach ($validated['items'] as $row) {
            $pid = (int) $row['product_id'];
            $mergedQty[$pid] = ($mergedQty[$pid] ?? 0) + (int) $row['quantidade'];
        }

        try {
            $sale = DB::transaction(function () use ($mergedQty, $session, $desconto, $validated) {
                $lines = [];

                foreach ($mergedQty as $productId => $qty) {
                    $product = Product::query()
                        ->lockForUpdate()
                        ->findOrFail($productId);

                    if ($product->status !== 'ativo') {
                        throw ValidationException::withMessages([
                            'items' => 'O produto '.$product->nome.' não está ativo.',
                        ]);
                    }

                    if ($product->estoque < $qty) {
                        throw ValidationException::withMessages([
                            'items' => 'Estoque insuficiente para '.$product->nome.' (disponível: '.$product->estoque.' UN).',
                        ]);
                    }

                    $preco = (float) $product->preco;
                    $lines[] = [
                        'product' => $product,
                        'quantidade' => $qty,
                        'preco_unitario' => $preco,
                        'subtotal' => round($preco * $qty, 2),
                    ];
                }

                $subtotal = round(array_sum(array_column($lines, 'subtotal')), 2);
                if ($desconto > $subtotal) {
                    throw ValidationException::withMessages([
                        'desconto' => 'O desconto não pode ser maior que o subtotal (R$ '.number_format($subtotal, 2, ',', '.').').',
                    ]);
                }

                $total = round($subtotal - $desconto, 2);
                if ($total <= 0) {
                    throw ValidationException::withMessages([
                        'items' => 'O total da venda deve ser maior que zero.',
                    ]);
                }

                $sale = CashSale::query()->create([
                    'cash_register_session_id' => $session->id,
                    'user_id' => auth()->id(),
                    'subtotal' => $subtotal,
                    'desconto' => $desconto,
                    'total' => $total,
                    'forma_pagamento' => $validated['forma_pagamento'],
                ]);

                foreach ($lines as $line) {
                    CashSaleItem::query()->create([
                        'cash_sale_id' => $sale->id,
                        'product_id' => $line['product']->id,
                        'quantidade' => $line['quantidade'],
                        'preco_unitario' => $line['preco_unitario'],
                        'subtotal' => $line['subtotal'],
                    ]);

                    $line['product']->refresh();
                    EstoqueMovimentoService::registrar(
                        $line['product'],
                        'saida',
                        $line['quantidade'],
                        'Venda PDV #'.$sale->id,
                    );
                }

                $pct = (float) PdvSetting::current()->comissao_percentual;
                $comValor = $pct > 0 ? round((float) $sale->total * ($pct / 100), 2) : 0.0;
                if ($comValor > 0) {
                    Commission::query()->create([
                        'user_id' => auth()->id(),
                        'valor' => $comValor,
                        'percentual' => round($pct, 2),
                        'status' => Commission::STATUS_PENDENTE,
                        'origem' => 'venda',
                        'cash_sale_id' => $sale->id,
                        'created_by' => auth()->id(),
                    ]);
                }

                return $sale;
            });
        } catch (ValidationException $e) {
            return response()->json([
                'message' => collect($e->errors())->flatten()->first(),
                'errors' => $e->errors(),
            ], 422);
        }

        return response()->json([
            'ok' => true,
            'sale_id' => $sale->id,
            'total' => (float) $sale->total,
        ]);
    }
}
