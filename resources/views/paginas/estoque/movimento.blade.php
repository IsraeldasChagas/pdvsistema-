<x-app-layout>
    <div class="px-4 py-8 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-lg">
            <h1 class="text-2xl font-bold text-gray-900">{{ $titulo }}</h1>
            <p class="mt-1 text-sm text-gray-500">
                {{ $product->codigo }} — {{ $product->nome }}
                @if ($product->category)
                    · {{ $product->category->nome }}
                @endif
            </p>
            <p class="mt-2 text-sm font-medium text-gray-700">Estoque atual: <span class="text-gray-900">{{ $product->estoque }} UN</span></p>

            <form
                action="{{ route('estoque.movimento.store', [$product, $tipo]) }}"
                method="post"
                class="mt-8 space-y-5 rounded-xl border border-gray-200 bg-white p-6 shadow-sm"
            >
                @csrf
                <div>
                    <label for="quantidade" class="block text-sm font-medium text-gray-700">Quantidade (UN) <span class="text-red-500">*</span></label>
                    <input
                        type="number"
                        name="quantidade"
                        id="quantidade"
                        min="1"
                        value="{{ old('quantidade', 1) }}"
                        required
                        class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                    />
                    @error('quantidade')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="observacao" class="block text-sm font-medium text-gray-700">Observação (opcional)</label>
                    <textarea
                        name="observacao"
                        id="observacao"
                        rows="2"
                        class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                    >{{ old('observacao') }}</textarea>
                    @error('observacao')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div class="flex flex-wrap gap-3 border-t border-gray-100 pt-6">
                    <button type="submit" class="rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-blue-700">
                        Confirmar
                    </button>
                    <a href="{{ route('modulos.estoque') }}" class="rounded-lg border border-gray-300 bg-white px-5 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50">
                        Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
