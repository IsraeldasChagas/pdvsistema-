<x-app-layout>
    <div class="px-4 py-8 sm:px-6 lg:px-8">
        @if (session('success'))
            <div class="mb-4 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
                {{ session('success') }}
            </div>
        @endif

        <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
            <h1 class="text-3xl font-bold tracking-tight text-gray-900">Categorias</h1>
            <div class="flex flex-wrap items-center gap-3">
                <a
                    href="{{ route('categorias.create') }}"
                    class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:ring-offset-2"
                >
                    + Nova Categoria
                </a>
                <a
                    href="{{ route('modulos.produtos') }}"
                    class="inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 shadow-sm transition hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-400 focus:ring-offset-2"
                >
                    Produtos
                </a>
            </div>
        </div>

        <div class="mt-8 overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-left text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-4 py-3 font-semibold text-gray-700">Nome</th>
                            <th scope="col" class="min-w-[200px] px-4 py-3 font-semibold text-gray-700">Descrição</th>
                            <th scope="col" class="whitespace-nowrap px-4 py-3 font-semibold text-gray-700">Status</th>
                            <th scope="col" class="whitespace-nowrap px-4 py-3 font-semibold text-gray-700">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @forelse ($categorias as $cat)
                            <tr class="hover:bg-gray-50/80">
                                <td class="whitespace-nowrap px-4 py-3 font-medium text-gray-900">{{ $cat->nome }}</td>
                                <td class="px-4 py-3 text-gray-700">{{ $cat->descricao ?: '—' }}</td>
                                <td class="whitespace-nowrap px-4 py-3">
                                    @if ($cat->ativo)
                                        <span class="inline-flex rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800">Ativo</span>
                                    @else
                                        <span class="inline-flex rounded-full bg-gray-200 px-2.5 py-0.5 text-xs font-medium text-gray-700">Inativo</span>
                                    @endif
                                </td>
                                <td class="whitespace-nowrap px-4 py-3">
                                    <span class="flex flex-wrap items-center gap-x-1 text-xs sm:text-sm">
                                        <a href="{{ route('categorias.edit', $cat) }}" class="font-medium text-blue-600 hover:text-blue-700">Editar</a>
                                        <span class="text-gray-300" aria-hidden="true">|</span>
                                        <form
                                            action="{{ route('categorias.destroy', $cat) }}"
                                            method="post"
                                            class="inline"
                                            onsubmit="return confirm('Excluir esta categoria?');"
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
                                <td colspan="4" class="px-4 py-12 text-center text-sm text-gray-500">
                                    Nenhuma categoria cadastrada.
                                    <a href="{{ route('categorias.create') }}" class="font-medium text-blue-600 hover:text-blue-500">Criar a primeira</a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
