<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\User;
use App\Services\EstoqueMovimentoService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Js;
use Illuminate\View\View;

class EntregaController extends Controller
{
    public function index(): View
    {
        $produtos = Product::query()
            ->with('category')
            ->where('status', 'ativo')
            ->orderBy('nome')
            ->get();

        $produtosAlpine = $produtos->map(fn (Product $p) => [
            'id' => $p->id,
            'nome' => $p->nome,
            'codigo' => $p->codigo,
            'category_id' => $p->category_id,
            'categoria' => $p->category?->nome ?? 'Sem categoria',
            'estoque' => (int) $p->estoque,
        ])->values()->all();

        $categorias = Category::query()
            ->ativa()
            ->orderBy('nome')
            ->get();

        $vendedores = User::query()
            ->orderBy('name')
            ->get(['id', 'name']);

        $entregas = StockMovement::query()
            ->with(['product', 'user', 'destinatario'])
            ->where('tipo', 'entrega')
            ->orderByDesc('created_at')
            ->limit(50)
            ->get();

        return view('paginas.entregas', [
            'categorias' => $categorias,
            'vendedores' => $vendedores,
            'entregas' => $entregas,
            'produtosAlpineJson' => Js::from($produtosAlpine),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->merge([
            'valor_unitario_repasse' => str_replace(',', '.', $request->string('valor_unitario_repasse')->toString()),
        ]);

        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'vendedor_id' => 'required|exists:users,id',
            'quantidade' => 'required|integer|min:1',
            'valor_unitario_repasse' => ['required', 'numeric', 'min:0.01', 'max:999999999999.99'],
            'return_to' => 'nullable|in:estoque',
        ]);

        $product = Product::query()->findOrFail($validated['product_id']);
        $q = (int) $validated['quantidade'];

        if ($product->estoque < $q) {
            return back()
                ->withInput()
                ->withErrors(['quantidade' => 'Estoque insuficiente na loja (disponível: '.$product->estoque.' UN).']);
        }

        $valorUnit = number_format((float) $validated['valor_unitario_repasse'], 2, '.', '');

        EstoqueMovimentoService::registrarEntregaParaVendedor(
            $product,
            $q,
            (int) $validated['vendedor_id'],
            $valorUnit,
        );

        $route = ($validated['return_to'] ?? null) === 'estoque'
            ? 'modulos.estoque'
            : 'modulos.entregas';

        return redirect()
            ->route($route)
            ->with('success', 'Entrega registrada.');
    }
}
