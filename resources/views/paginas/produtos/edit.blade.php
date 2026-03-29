<x-app-layout>
    <div class="px-4 py-8 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-2xl">
            <h1 class="text-2xl font-bold text-gray-900">Editar produto</h1>
            <p class="mt-1 text-sm text-gray-500">{{ $product->nome }} ({{ $product->codigo }})</p>

            <form action="{{ route('produtos.update', $product) }}" method="post" class="mt-8 space-y-6 rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                @csrf
                @method('PUT')
                @include('paginas.produtos._form', ['produto' => $product, 'categorias' => $categorias])

                <div class="flex flex-wrap gap-3 border-t border-gray-100 pt-6">
                    <button type="submit" class="rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-blue-700">
                        Atualizar
                    </button>
                    <a href="{{ route('modulos.produtos') }}" class="rounded-lg border border-gray-300 bg-white px-5 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50">
                        Voltar
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
