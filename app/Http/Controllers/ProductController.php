<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Support\CurrentCompany;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(): View
    {
        $produtos = Product::query()
            ->with('category')
            ->orderBy('nome')
            ->get();

        return view('paginas.produtos.index', compact('produtos'));
    }

    public function create(): View
    {
        $categorias = Category::query()->ativa()->orderBy('nome')->get();

        return view('paginas.produtos.create', compact('categorias'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatedStore($request);
        if (empty($data['codigo'])) {
            $data['codigo'] = $this->nextCodigo();
        }
        Product::create($data);

        return redirect()
            ->route('modulos.produtos')
            ->with('success', 'Produto cadastrado com sucesso.');
    }

    public function show(Product $product): View
    {
        $product->load('category');

        return view('paginas.produtos.show', compact('product'));
    }

    public function edit(Product $product): View
    {
        $categorias = Category::query()
            ->where(function ($q) use ($product) {
                $q->where('ativo', true);
                if ($product->category_id) {
                    $q->orWhere('id', $product->category_id);
                }
            })
            ->orderBy('nome')
            ->get();

        return view('paginas.produtos.edit', compact('product', 'categorias'));
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $product->update($this->validatedUpdate($request, $product));

        return redirect()
            ->route('modulos.produtos')
            ->with('success', 'Produto atualizado.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        $product->delete();

        return redirect()
            ->route('modulos.produtos')
            ->with('success', 'Produto excluído.');
    }

    private function validatedStore(Request $request): array
    {
        $cid = CurrentCompany::id();
        $validated = $request->validate([
            'codigo' => [
                'nullable',
                'string',
                'max:50',
                Rule::unique('products', 'codigo')->where(fn ($q) => $q->where('company_id', $cid)),
            ],
            'marca' => 'nullable|string|max:100',
            'nome' => 'required|string|max:255',
            'category_id' => [
                'nullable',
                Rule::exists('categories', 'id')->where(fn ($q) => $q->where('company_id', $cid)),
            ],
            'caracteristicas' => 'nullable|string|max:2000',
            'preco' => 'required|numeric|min:0',
            'estoque' => 'required|integer|min:0',
            'status' => 'required|in:ativo,inativo',
        ]);

        $validated['codigo'] = isset($validated['codigo']) && $validated['codigo'] !== ''
            ? trim($validated['codigo'])
            : null;

        return $validated;
    }

    private function validatedUpdate(Request $request, Product $product): array
    {
        $cid = CurrentCompany::id();
        $validated = $request->validate([
            'codigo' => [
                'required',
                'string',
                'max:50',
                Rule::unique('products', 'codigo')->where(fn ($q) => $q->where('company_id', $cid))->ignore($product->id),
            ],
            'marca' => 'nullable|string|max:100',
            'nome' => 'required|string|max:255',
            'category_id' => [
                'nullable',
                Rule::exists('categories', 'id')->where(fn ($q) => $q->where('company_id', $cid)),
            ],
            'caracteristicas' => 'nullable|string|max:2000',
            'preco' => 'required|numeric|min:0',
            'estoque' => 'required|integer|min:0',
            'status' => 'required|in:ativo,inativo',
        ]);

        $validated['codigo'] = trim($validated['codigo']);

        return $validated;
    }

    private function nextCodigo(): string
    {
        $max = (int) Product::query()->max('id');

        return 'PROD-'.str_pad((string) ($max + 1), 4, '0', STR_PAD_LEFT);
    }
}
