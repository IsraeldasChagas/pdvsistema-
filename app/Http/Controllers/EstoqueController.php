<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockMovement;
use App\Models\User;
use App\Services\EstoqueMovimentoService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EstoqueController extends Controller
{
    private const TIPOS = ['entrada', 'saida', 'entrega'];

    public function index(): View
    {
        $produtos = Product::query()
            ->with('category')
            ->orderBy('nome')
            ->get();

        $vendedores = User::query()
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('paginas.estoque.index', [
            'produtos' => $produtos,
            'vendedores' => $vendedores,
            'semVendedores' => $vendedores->isEmpty(),
        ]);
    }

    public function historico(Request $request): View
    {
        $query = StockMovement::query()
            ->with(['product', 'user', 'destinatario'])
            ->orderByDesc('created_at');

        if ($request->filled('produto')) {
            $query->where('product_id', $request->integer('produto'));
        }

        if ($request->filled('tipo')) {
            $t = $request->string('tipo')->toString();
            if (in_array($t, self::TIPOS, true)) {
                $query->where('tipo', $t);
            }
        }

        $movimentos = $query->limit(200)->get();
        $produtos = Product::orderBy('nome')->get(['id', 'nome', 'codigo']);

        return view('paginas.estoque.historico', compact('movimentos', 'produtos'));
    }

    public function movimentoForm(Product $product, string $tipo): View|RedirectResponse
    {
        $this->assertTipo($tipo);

        if ($tipo === 'entrega') {
            return redirect()
                ->route('modulos.estoque')
                ->with('success', 'Use a ação Entregar na listagem para registrar a entrega ao vendedor de rua.');
        }

        $titulos = [
            'entrada' => 'Entrada de estoque',
            'saida' => 'Saída de estoque',
            'entrega' => 'Entrega (baixa no estoque)',
        ];

        return view('paginas.estoque.movimento', [
            'product' => $product->load('category'),
            'tipo' => $tipo,
            'titulo' => $titulos[$tipo],
        ]);
    }

    public function movimentoStore(Request $request, Product $product, string $tipo): RedirectResponse
    {
        $this->assertTipo($tipo);

        if ($tipo === 'entrega') {
            return redirect()
                ->route('modulos.estoque')
                ->with('success', 'Use a ação Entregar na listagem para registrar a entrega ao vendedor de rua.');
        }

        $validated = $request->validate([
            'quantidade' => 'required|integer|min:1',
            'observacao' => 'nullable|string|max:500',
        ]);

        $q = (int) $validated['quantidade'];

        if (in_array($tipo, ['saida', 'entrega'], true) && $product->estoque < $q) {
            return back()
                ->withInput()
                ->withErrors(['quantidade' => 'Estoque insuficiente (disponível: '.$product->estoque.' UN).']);
        }

        EstoqueMovimentoService::registrar(
            $product,
            $tipo,
            $q,
            $validated['observacao'] ?? null,
        );

        $msg = match ($tipo) {
            'entrada' => 'Entrada registrada.',
            'saida' => 'Saída registrada.',
            default => 'Entrega registrada.',
        };

        return redirect()
            ->route('modulos.estoque')
            ->with('success', $msg);
    }

    private function assertTipo(string $tipo): void
    {
        abort_unless(in_array($tipo, self::TIPOS, true), 404);
    }
}
