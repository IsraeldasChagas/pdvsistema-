<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Support\CurrentCompany;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(): View
    {
        $categorias = Category::query()->orderBy('nome')->get();

        return view('paginas.categorias.index', compact('categorias'));
    }

    public function create(): View
    {
        return view('paginas.categorias.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        Category::create($data);

        return redirect()
            ->route('modulos.categorias')
            ->with('success', 'Categoria cadastrada com sucesso.');
    }

    public function edit(Category $category): View
    {
        return view('paginas.categorias.edit', compact('category'));
    }

    public function update(Request $request, Category $category): RedirectResponse
    {
        $category->update($this->validated($request, $category->id));

        return redirect()
            ->route('modulos.categorias')
            ->with('success', 'Categoria atualizada.');
    }

    public function destroy(Category $category): RedirectResponse
    {
        $category->delete();

        return redirect()
            ->route('modulos.categorias')
            ->with('success', 'Categoria excluída. Produtos vinculados ficaram sem categoria.');
    }

    private function validated(Request $request, ?int $ignoreId = null): array
    {
        $cid = CurrentCompany::id();
        $nomeRule = Rule::unique('categories', 'nome')->where(fn ($q) => $q->where('company_id', $cid));
        if ($ignoreId !== null) {
            $nomeRule = $nomeRule->ignore($ignoreId);
        }

        $validated = $request->validate([
            'nome' => ['required', 'string', 'max:255', $nomeRule],
            'descricao' => 'nullable|string|max:2000',
            'ativo' => 'required|in:0,1',
        ]);

        $validated['ativo'] = (bool) (int) $validated['ativo'];

        return $validated;
    }
}
