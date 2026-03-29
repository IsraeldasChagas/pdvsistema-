@php
    /** @var \App\Models\Product|null $produto */
    $produto = $produto ?? null;
@endphp

<div class="space-y-4">
    <div>
        <label for="codigo" class="block text-sm font-medium text-gray-700">Código</label>
        <input
            type="text"
            name="codigo"
            id="codigo"
            value="{{ old('codigo', $produto?->codigo) }}"
            class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
            placeholder="Deixe vazio para gerar automaticamente (ex.: PROD-0001)"
        />
        @error('codigo')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>
    <div>
        <label for="marca" class="block text-sm font-medium text-gray-700">Marca</label>
        <input
            type="text"
            name="marca"
            id="marca"
            value="{{ old('marca', $produto?->marca) }}"
            class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
        />
        @error('marca')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>
    <div>
        <label for="nome" class="block text-sm font-medium text-gray-700">Nome <span class="text-red-500">*</span></label>
        <input
            type="text"
            name="nome"
            id="nome"
            value="{{ old('nome', $produto?->nome) }}"
            required
            class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
        />
        @error('nome')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>
    <div>
        <label for="category_id" class="block text-sm font-medium text-gray-700">Categoria</label>
        <select
            name="category_id"
            id="category_id"
            class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
        >
            <option value="">— Selecione —</option>
            @foreach ($categorias as $cat)
                <option value="{{ $cat->id }}" @selected(old('category_id', $produto?->category_id) == $cat->id)>{{ $cat->nome }}</option>
            @endforeach
        </select>
        @error('category_id')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>
    <div>
        <label for="caracteristicas" class="block text-sm font-medium text-gray-700">Características</label>
        <textarea
            name="caracteristicas"
            id="caracteristicas"
            rows="3"
            class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
        >{{ old('caracteristicas', $produto?->caracteristicas) }}</textarea>
        @error('caracteristicas')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        <div>
            <label for="preco" class="block text-sm font-medium text-gray-700">Preço (R$) <span class="text-red-500">*</span></label>
            <input
                type="number"
                name="preco"
                id="preco"
                step="0.01"
                min="0"
                value="{{ old('preco', $produto?->preco) }}"
                required
                class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
            />
            @error('preco')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
        <div>
            <label for="estoque" class="block text-sm font-medium text-gray-700">Estoque (UN) <span class="text-red-500">*</span></label>
            <input
                type="number"
                name="estoque"
                id="estoque"
                min="0"
                value="{{ old('estoque', $produto?->estoque ?? 0) }}"
                required
                class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
            />
            @error('estoque')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
    </div>
    <div>
        <label for="status" class="block text-sm font-medium text-gray-700">Status <span class="text-red-500">*</span></label>
        <select
            name="status"
            id="status"
            required
            class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
        >
            @foreach (['ativo' => 'Ativo', 'inativo' => 'Inativo'] as $val => $label)
                <option value="{{ $val }}" @selected(old('status', $produto?->status ?? 'ativo') === $val)>{{ $label }}</option>
            @endforeach
        </select>
        @error('status')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>
</div>
