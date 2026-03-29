@php
    /** @var \App\Models\Category|null $category */
    $category = $category ?? null;
@endphp

<div class="space-y-4">
    <div>
        <label for="nome" class="block text-sm font-medium text-gray-700">Nome <span class="text-red-500">*</span></label>
        <input
            type="text"
            name="nome"
            id="nome"
            value="{{ old('nome', $category?->nome) }}"
            required
            class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
        />
        @error('nome')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>
    <div>
        <label for="descricao" class="block text-sm font-medium text-gray-700">Descrição</label>
        <textarea
            name="descricao"
            id="descricao"
            rows="4"
            class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
        >{{ old('descricao', $category?->descricao) }}</textarea>
        @error('descricao')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>
    <div>
        <label for="ativo" class="block text-sm font-medium text-gray-700">Status <span class="text-red-500">*</span></label>
        @php
            $ativoVal = old('ativo', $category ? ($category->ativo ? '1' : '0') : '1');
        @endphp
        <select
            name="ativo"
            id="ativo"
            required
            class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
        >
            <option value="1" @selected($ativoVal === '1' || $ativoVal === 1 || $ativoVal === true)>Ativo</option>
            <option value="0" @selected($ativoVal === '0' || $ativoVal === 0 || $ativoVal === false)>Inativo</option>
        </select>
        @error('ativo')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>
</div>
