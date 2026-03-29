<x-app-layout>
    <div class="px-4 py-8 sm:px-6 lg:px-8">
        @if (session('success'))
            <div class="mb-4 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
                {{ session('success') }}
            </div>
        @endif

        <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
            <h1 class="text-3xl font-bold tracking-tight text-gray-900">Produtos</h1>
            <div class="flex flex-wrap items-center gap-3">
                <a
                    href="{{ route('produtos.create') }}"
                    class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:ring-offset-2"
                >
                    + Novo Produto
                </a>
                <a
                    href="{{ route('modulos.categorias') }}"
                    class="inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 shadow-sm transition hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-400 focus:ring-offset-2"
                >
                    Categorias
                </a>
            </div>
        </div>

        <div class="mt-8 overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-left text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="whitespace-nowrap px-4 py-3 font-semibold text-gray-700">Código</th>
                            <th scope="col" class="whitespace-nowrap px-4 py-3 font-semibold text-gray-700">Marca</th>
                            <th scope="col" class="whitespace-nowrap px-4 py-3 font-semibold text-gray-700">Nome</th>
                            <th scope="col" class="whitespace-nowrap px-4 py-3 font-semibold text-gray-700">Categoria</th>
                            <th scope="col" class="min-w-[140px] px-4 py-3 font-semibold text-gray-700">Características</th>
                            <th scope="col" class="whitespace-nowrap px-4 py-3 font-semibold text-gray-700">Preço</th>
                            <th scope="col" class="whitespace-nowrap px-4 py-3 font-semibold text-gray-700">Estoque</th>
                            <th scope="col" class="whitespace-nowrap px-4 py-3 font-semibold text-gray-700">Status</th>
                            <th scope="col" class="whitespace-nowrap px-4 py-3 font-semibold text-gray-700">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @forelse ($produtos as $p)
                            <tr class="hover:bg-gray-50/80">
                                <td class="whitespace-nowrap px-4 py-3 text-gray-900">{{ $p->codigo }}</td>
                                <td class="whitespace-nowrap px-4 py-3 text-gray-700">{{ $p->marca ?: '—' }}</td>
                                <td class="whitespace-nowrap px-4 py-3 font-medium text-gray-900">{{ $p->nome }}</td>
                                <td class="whitespace-nowrap px-4 py-3 text-gray-700">{{ $p->category?->nome ?? '—' }}</td>
                                <td class="max-w-xs truncate px-4 py-3 text-gray-600" title="{{ $p->caracteristicas }}">{{ $p->caracteristicas ?: '—' }}</td>
                                <td class="whitespace-nowrap px-4 py-3 font-semibold text-gray-900">
                                    R$ {{ number_format((float) $p->preco, 2, ',', '.') }}
                                </td>
                                <td class="whitespace-nowrap px-4 py-3 text-gray-700">{{ $p->estoque }} UN</td>
                                <td class="whitespace-nowrap px-4 py-3">
                                    @if ($p->isAtivo())
                                        <span class="inline-flex rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800">Ativo</span>
                                    @else
                                        <span class="inline-flex rounded-full bg-gray-200 px-2.5 py-0.5 text-xs font-medium text-gray-700">Inativo</span>
                                    @endif
                                </td>
                                <td class="whitespace-nowrap px-4 py-3">
                                    <span class="flex flex-wrap items-center gap-x-1 text-xs sm:text-sm">
                                        <a href="{{ route('produtos.show', $p) }}" class="font-medium text-gray-500 hover:text-gray-700">Visualizar</a>
                                        <span class="text-gray-300" aria-hidden="true">|</span>
                                        <a href="{{ route('produtos.edit', $p) }}" class="font-medium text-blue-600 hover:text-blue-700">Editar</a>
                                        <span class="text-gray-300" aria-hidden="true">|</span>
                                        <form
                                            action="{{ route('produtos.destroy', $p) }}"
                                            method="post"
                                            class="inline"
                                            onsubmit="return confirm('Excluir este produto?');"
                                        >
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="font-medium text-red-600 hover:text-red-700">Excluir</button>
                                        </form>
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-4 py-12 text-center text-sm text-gray-500">
                                    Nenhum produto cadastrado.
                                    <a href="{{ route('produtos.create') }}" class="font-medium text-blue-600 hover:text-blue-500">Criar o primeiro</a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
