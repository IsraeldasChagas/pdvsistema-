<x-app-layout>
    <script>
        window.entradasPage = function (produtos) {
            return {
                produtos,
                search: '',
                categoriaId: null,
                modalOpen: false,
                selected: null,
                qty: 1,
                motivo: '',
                openModal(p) {
                    this.selected = p;
                    this.qty = 1;
                    this.motivo = '';
                    this.modalOpen = true;
                },
                closeModal() {
                    this.modalOpen = false;
                    this.selected = null;
                },
                matches(p) {
                    if (this.categoriaId !== null && p.category_id !== this.categoriaId) {
                        return false;
                    }
                    const q = this.search.trim().toLowerCase();
                    if (!q) {
                        return true;
                    }
                    return (
                        p.nome.toLowerCase().includes(q) ||
                        String(p.codigo).toLowerCase().includes(q)
                    );
                },
                get filtered() {
                    return this.produtos.filter((p) => this.matches(p));
                },
            };
        };
    </script>

    <div
        class="min-h-[calc(100vh-3.5rem)] bg-zinc-100 px-4 py-8 sm:px-6 lg:px-8"
        x-data="entradasPage({{ $produtosAlpineJson }})"
        @keydown.escape.window="closeModal()"
    >
        <div class="mx-auto max-w-7xl">
            @if (session('success'))
                <div class="mb-4 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                    <ul class="list-inside list-disc space-y-1">
                        @foreach ($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                <div class="flex gap-3">
                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-blue-600 text-white shadow-sm">
                        <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" />
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold tracking-tight text-gray-900 sm:text-3xl">Entradas de Estoque</h1>
                        <p class="mt-0.5 text-sm text-gray-500">Registre compras, reposições e devoluções de produtos</p>
                    </div>
                </div>
                <div class="flex flex-wrap gap-2">
                    <a
                        href="{{ route('modulos.estoque') }}"
                        class="inline-flex items-center gap-2 rounded-full border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 shadow-sm transition hover:bg-gray-50"
                    >
                        <svg class="h-4 w-4 text-gray-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" />
                        </svg>
                        Estoque
                    </a>
                    <a
                        href="{{ route('estoque.historico', ['tipo' => 'entrada']) }}"
                        class="inline-flex items-center gap-2 rounded-full border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 shadow-sm transition hover:bg-gray-50"
                    >
                        <svg class="h-4 w-4 text-gray-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 6.75h12M8.25 12h12m-12 4.5h12M3.75 6.75h.007v.008H3.75V6.75zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zM3.75 12h.007v.008H3.75V12zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm-.375 4.5h.007v.008H3.75v-.008zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                        </svg>
                        Histórico Completo
                    </a>
                </div>
            </div>

            <div class="mt-8 overflow-hidden rounded-xl border border-gray-200/80 bg-white shadow-md">
                <div class="flex flex-wrap items-center gap-2 bg-emerald-600 px-4 py-3 text-sm text-white">
                    <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" />
                    </svg>
                    <span>
                        Registrar / <strong class="font-bold">Nova Entrada</strong> / Clique no produto para adicionar quantidade ao estoque
                    </span>
                </div>
                <div class="p-4 sm:p-6">
                    <div class="relative">
                        <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                            </svg>
                        </span>
                        <input
                            type="search"
                            x-model="search"
                            placeholder="Buscar por nome ou código..."
                            class="w-full rounded-lg border border-gray-300 bg-white py-2.5 pl-10 pr-4 text-sm text-gray-900 placeholder:text-gray-400 shadow-sm focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500"
                        />
                    </div>

                    <div class="mt-4 flex flex-wrap gap-2">
                        <button
                            type="button"
                            @click="categoriaId = null"
                            :class="categoriaId === null ? 'rounded-full bg-emerald-600 px-4 py-1.5 text-sm font-semibold text-white shadow-sm' : 'rounded-full border border-gray-300 bg-white px-4 py-1.5 text-sm font-semibold text-gray-700 hover:bg-gray-50'"
                        >
                            Todos
                        </button>
                        @foreach ($categorias as $cat)
                            <button
                                type="button"
                                @click="categoriaId = {{ $cat->id }}"
                                :class="categoriaId === {{ $cat->id }} ? 'rounded-full bg-emerald-600 px-4 py-1.5 text-sm font-semibold text-white shadow-sm' : 'rounded-full border border-gray-300 bg-white px-4 py-1.5 text-sm font-semibold text-gray-700 hover:bg-gray-50'"
                            >
                                {{ $cat->nome }}
                            </button>
                        @endforeach
                    </div>

                    <template x-if="filtered.length === 0">
                        <p class="mt-8 text-center text-sm text-gray-500">Nenhum produto encontrado com os filtros atuais.</p>
                    </template>

                    <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5">
                        <template x-for="p in filtered" :key="p.id">
                            <div class="flex flex-col overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm transition hover:shadow-md">
                                <div class="flex aspect-square items-center justify-center bg-gray-100">
                                    <svg class="h-16 w-16 text-gray-300" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" />
                                    </svg>
                                </div>
                                <div class="flex flex-1 flex-col p-4">
                                    <p class="font-bold text-gray-900" x-text="p.nome"></p>
                                    <p class="mt-0.5 text-xs text-gray-500" x-text="p.categoria"></p>
                                    <p
                                        class="mt-2 text-sm font-bold"
                                        :class="p.estoque <= 5 ? 'text-orange-600' : 'text-gray-800'"
                                        x-text="'Est: ' + p.estoque + ' UN'"
                                    ></p>
                                    <button
                                        type="button"
                                        @click="openModal(p)"
                                        class="mt-3 w-full rounded-lg bg-emerald-600 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-700"
                                    >
                                        + Entrada
                                    </button>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            <div class="mt-8 overflow-hidden rounded-xl border border-gray-200/80 bg-white shadow-md">
                <div class="border-b border-gray-100 px-4 py-4 sm:px-6">
                    <h2 class="text-lg font-bold text-gray-900">Histórico de Entradas</h2>
                    <p class="mt-0.5 text-sm text-gray-500">Últimas 50 entradas registradas</p>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-left text-sm">
                        <thead class="border-b border-gray-200 bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 font-semibold text-gray-600 sm:px-6">Data</th>
                                <th class="px-4 py-3 font-semibold text-gray-600 sm:px-6">Produto</th>
                                <th class="px-4 py-3 font-semibold text-gray-600 sm:px-6">Quantidade</th>
                                <th class="px-4 py-3 font-semibold text-gray-600 sm:px-6">Motivo</th>
                                <th class="px-4 py-3 font-semibold text-gray-600 sm:px-6">Usuário</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($entradas as $e)
                                <tr class="border-b border-gray-100 hover:bg-gray-50/80">
                                    <td class="whitespace-nowrap px-4 py-3 text-gray-700 sm:px-6">{{ $e->created_at->format('d/m/Y H:i') }}</td>
                                    <td class="px-4 py-3 sm:px-6">
                                        <span class="font-medium text-gray-900">{{ $e->product?->nome ?? '—' }}</span>
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-3 font-medium text-gray-900 sm:px-6">{{ $e->quantidade }} UN</td>
                                    <td class="max-w-xs truncate px-4 py-3 text-gray-600 sm:px-6" title="{{ $e->observacao }}">{{ $e->observacao ?: '—' }}</td>
                                    <td class="whitespace-nowrap px-4 py-3 text-gray-600 sm:px-6">{{ $e->user?->name ?? '—' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-16 text-center sm:px-6">
                                        <div class="mx-auto flex max-w-sm flex-col items-center">
                                            <div class="flex h-14 w-14 items-center justify-center rounded-full bg-blue-50 text-blue-600">
                                                <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />
                                                </svg>
                                            </div>
                                            <p class="mt-4 text-sm font-medium text-gray-600">Nenhuma entrada registrada ainda</p>
                                            <p class="mt-1 text-sm text-gray-500">Use os botões acima para registrar a primeira entrada</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Modal Registrar Entrada --}}
        <div
            x-show="modalOpen"
            x-cloak
            class="fixed inset-0 z-50 flex items-center justify-center p-4"
        >
            <div class="absolute inset-0 bg-black/40" @click="closeModal()" aria-hidden="true"></div>
            <div
                class="relative z-10 w-full max-w-md overflow-hidden rounded-xl bg-white shadow-xl"
                role="dialog"
                aria-modal="true"
                aria-labelledby="modal-entrada-titulo"
                @click.stop
            >
                <div class="flex items-center justify-between border-b border-gray-100 px-5 py-4">
                    <h2 id="modal-entrada-titulo" class="text-lg font-bold text-gray-900">Registrar Entrada</h2>
                    <button
                        type="button"
                        class="rounded-lg p-1 text-gray-400 hover:bg-gray-100 hover:text-gray-600"
                        @click="closeModal()"
                        aria-label="Fechar"
                    >
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <form method="post" action="{{ route('entradas.store') }}" class="px-5 py-4">
                    @csrf
                    <input type="hidden" name="product_id" :value="selected ? selected.id : ''" />

                    <div class="rounded-lg border border-emerald-100 bg-emerald-50/80 px-4 py-3 text-sm">
                        <p class="font-semibold text-gray-900" x-text="selected ? selected.nome : ''"></p>
                        <p class="mt-1 text-gray-600" x-show="selected" x-text="selected ? 'Estoque atual: ' + selected.estoque + ' UN' : ''"></p>
                    </div>

                    <div class="mt-5">
                        <label for="quantidade_entrada" class="block text-sm font-medium text-gray-700">Quantidade <span class="text-red-500">*</span></label>
                        <input
                            type="number"
                            name="quantidade"
                            id="quantidade_entrada"
                            min="1"
                            required
                            x-model.number="qty"
                            class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500"
                        />
                    </div>
                    <div class="mt-4">
                        <label for="motivo_entrada" class="block text-sm font-medium text-gray-700">Motivo (opcional)</label>
                        <input
                            type="text"
                            name="motivo"
                            id="motivo_entrada"
                            placeholder="Ex: Compra, reposição, devolução"
                            x-model="motivo"
                            class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500"
                        />
                    </div>

                    <div class="mt-6 flex flex-wrap justify-end gap-3 border-t border-gray-100 pt-5">
                        <button
                            type="button"
                            @click="closeModal()"
                            class="rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50"
                        >
                            Cancelar
                        </button>
                        <button
                            type="submit"
                            class="rounded-lg bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-emerald-700"
                        >
                            Confirmar Entrada
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
