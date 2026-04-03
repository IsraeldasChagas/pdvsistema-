<?php

namespace App\Services;

use App\Models\Product;
use App\Models\StockMovement;
use App\Models\VendorStock;
use Illuminate\Support\Facades\DB;

class EstoqueMovimentoService
{
    public static function registrar(Product $product, string $tipo, int $quantidade, ?string $observacao = null): void
    {
        DB::transaction(function () use ($product, $tipo, $quantidade, $observacao) {
            if ($tipo === 'entrada') {
                $product->increment('estoque', $quantidade);
            } else {
                $product->decrement('estoque', $quantidade);
            }
            $product->refresh();

            StockMovement::query()->create([
                'product_id' => $product->id,
                'user_id' => auth()->id(),
                'tipo' => $tipo,
                'quantidade' => $quantidade,
                'saldo_apos' => $product->estoque,
                'observacao' => $observacao,
            ]);
        });
    }

    /**
     * Baixa o estoque da loja e credita no estoque do vendedor (vendor_stocks).
     */
    public static function registrarEntregaParaVendedor(
        Product $product,
        int $quantidade,
        int $destinatarioUserId,
        ?string $valorUnitarioRepasse = null,
    ): void {
        DB::transaction(function () use ($product, $quantidade, $destinatarioUserId, $valorUnitarioRepasse) {
            $product->decrement('estoque', $quantidade);
            $product->refresh();

            $vs = VendorStock::query()->firstOrCreate(
                [
                    'user_id' => $destinatarioUserId,
                    'product_id' => $product->id,
                ],
                ['quantidade' => 0],
            );
            $vs->increment('quantidade', $quantidade);

            StockMovement::query()->create([
                'product_id' => $product->id,
                'user_id' => auth()->id(),
                'destinatario_user_id' => $destinatarioUserId,
                'tipo' => 'entrega',
                'quantidade' => $quantidade,
                'saldo_apos' => $product->estoque,
                'observacao' => null,
                'valor_unitario_repasse' => $valorUnitarioRepasse,
            ]);
        });
    }
}
