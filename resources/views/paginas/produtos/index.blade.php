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

        <div class="mt-8 space-y-4 lg:hidden">
            @forelse ($produtos as $p)
                <article class="overflow-hidden rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div class="sm:col-span-2">
                            <p class="text-sm font-bold text-gray-900">Nome</p>
                            <p class="mt-0.5 text-sm font-normal leading-snug text-gray-700">{{ $p->nome }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-gray-900">Código</p>
                            <p class="mt-0.5 text-sm font-normal text-gray-700">{{ $p->codigo }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-gray-900">Marca</p>
                            <p class="mt-0.5 text-sm font-normal text-gray-700">{{ $p->marca ?: '—' }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-gray-900">Categoria</p>
                            <p class="mt-0.5 text-sm font-normal text-gray-700">{{ $p->category?->nome ?? '—' }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-gray-900">Preço</p>
                            <p class="mt-0.5 text-sm font-normal text-gray-700">
                                R$ {{ number_format((float) $p->preco, 2, ',', '.') }}
                            </p>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-gray-900">Estoque</p>
                            <p class="mt-0.5 text-sm font-normal text-gray-700">{{ $p->estoque }} UN</p>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-gray-900">Status</p>
                            <p class="mt-0.5 font-normal">
                                @if ($p->isAtivo())
                                    <span class="inline-flex rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800">Ativo</span>
                                @else
                                    <span class="inline-flex rounded-full bg-gray-200 px-2.5 py-0.5 text-xs font-medium text-gray-700">Inativo</span>
                                @endif
                            </p>
                        </div>
                        <div class="sm:col-span-2">
                            <p class="text-sm font-bold text-gray-900">Características</p>
                            <p class="mt-0.5 whitespace-pre-wrap break-words text-sm font-normal text-gray-700">{{ $p->caracteristicas ?: '—' }}</p>
                        </div>
                    </div>
                    <div class="mt-4 flex flex-wrap items-center gap-x-3 gap-y-2 border-t border-gray-100 pt-4 text-sm">
                        <a href="{{ route('produtos.show', $p) }}" class="font-medium text-gray-600 hover:text-gray-900">Visualizar</a>
                        <a href="{{ route('produtos.edit', $p) }}" class="font-medium text-blue-600 hover:text-blue-700">Editar</a>
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
                    </div>
                </article>
            @empty
                <div class="rounded-lg border border-gray-200 bg-white px-4 py-12 text-center text-sm text-gray-500 shadow-sm">
                    Nenhum produto cadastrado.
                    <a href="{{ route('produtos.create') }}" class="font-medium text-blue-600 hover:text-blue-500">Criar o primeiro</a>
                </div>
            @endforelse
        </div>

        <div class="mt-8 hidden overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm lg:block">
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
