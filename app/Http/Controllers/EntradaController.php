<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\StockMovement;
use App\Services\EstoqueMovimentoService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Js;
use Illuminate\View\View;

class EntradaController extends Controller
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

        $entradas = StockMovement::query()
            ->with(['product', 'user'])
            ->where('tipo', 'entrada')
            ->orderByDesc('created_at')
            ->limit(50)
            ->get();

        return view('paginas.entradas', [
            'categorias' => $categorias,
            'entradas' => $entradas,
            'produtosAlpineJson' => Js::from($produtosAlpine),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantidade' => 'required|integer|min:1',
            'motivo' => 'nullable|string|max:500',
        ]);

        $product = Product::query()->findOrFail($validated['product_id']);

        EstoqueMovimentoService::registrar(
            $product,
            'entrada',
            (int) $validated['quantidade'],
            $validated['motivo'] ?? null,
        );

        return redirect()
            ->route('modulos.entradas')
            ->with('success', 'Entrada registrada.');
    }
}
