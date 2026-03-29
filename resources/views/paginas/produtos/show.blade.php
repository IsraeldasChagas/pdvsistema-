<x-app-layout>
    <div class="px-4 py-8 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-2xl">
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">{{ $product->nome }}</h1>
                    <p class="mt-1 text-sm text-gray-500">{{ $product->codigo }}</p>
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('produtos.edit', $product) }}" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">Editar</a>
                    <a href="{{ route('modulos.produtos') }}" class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">Lista</a>
                </div>
            </div>

            <dl class="mt-8 space-y-4 rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                <div>
                    <dt class="text-xs font-medium uppercase text-gray-500">Marca</dt>
                    <dd class="mt-1 text-gray-900">{{ $product->marca ?: '—' }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-medium uppercase text-gray-500">Categoria</dt>
                    <dd class="mt-1 text-gray-900">{{ $product->category?->nome ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-medium uppercase text-gray-500">Características</dt>
                    <dd class="mt-1 text-gray-900">{{ $product->caracteristicas ?: '—' }}</dd>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <dt class="text-xs font-medium uppercase text-gray-500">Preço</dt>
                        <dd class="mt-1 text-lg font-semibold text-gray-900">R$ {{ number_format((float) $product->preco, 2, ',', '.') }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium uppercase text-gray-500">Estoque</dt>
                        <dd class="mt-1 text-lg font-semibold text-gray-900">{{ $product->estoque }} UN</dd>
                    </div>
                </div>
                <div>
                    <dt class="text-xs font-medium uppercase text-gray-500">Status</dt>
                    <dd class="mt-1">
                        @if ($product->isAtivo())
                            <span class="inline-flex rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800">Ativo</span>
                        @else
                            <span class="inline-flex rounded-full bg-gray-200 px-2.5 py-0.5 text-xs font-medium text-gray-700">Inativo</span>
                        @endif
                    </dd>
                </div>
            </dl>
        </div>
    </div>
</x-app-layout>
