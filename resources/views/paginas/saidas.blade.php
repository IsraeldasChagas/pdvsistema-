<x-app-layout>
    <script>
        window.saidasPage = function (produtos) {
            return {
                produtos,
                search: '',
                categoriaId: null,
                modalOpen: false,
                selected: null,
                qty: 1,
                motivo: '',
                openModal(p) {
                    if (p.estoque < 1) {
                        return;
                    }
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
                get canSubmit() {
                    if (!this.selected || this.selected.estoque < 1) {
                        return false;
                    }
                    const q = Number(this.qty);
                    if (!Number.isFinite(q)) {
                        return false;
                    }
                    return q >= 1 && q <= this.selected.estoque;
                },
            };
        };
    </script>

    <div
        class="min-h-[calc(100vh-3.5rem)] bg-zinc-100 px-4 py-8 sm:px-6 lg:px-8"
        x-data="saidasPage({{ $produtosAlpineJson }})"
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
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5" />
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold tracking-tight text-blue-950 sm:text-3xl">Saídas de Estoque</h1>
                        <p class="mt-0.5 text-sm text-gray-500">Registre vendas avulsas, perdas, danos e baixas de produtos</p>
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
                        href="{{ route('modulos.entregas') }}"
                        class="inline-flex items-center gap-2 rounded-full border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 shadow-sm transition hover:bg-gray-50"
                    >
                        <svg class="h-4 w-4 text-gray-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 00-3.108-9.184M14.25 18.75h1.5a1.5 1.5 0 001.5-1.5V9.75a1.5 1.5 0 00-1.5-1.5h-1.5m-6 0H6a1.5 1.5 0 00-1.5 1.5v6.75a1.5 1.5 0 001.5 1.5h1.5m6-9.75V9.75m0 0L9 3.75m5.25 5.25L9 9.75" />
                        </svg>
                        Entregas
                    </a>
                    <a
                        href="{{ route('estoque.historico', ['tipo' => 'saida']) }}"
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
                <div class="flex flex-wrap items-center justify-between gap-3 rounded-t-xl bg-red-600 px-4 py-3 text-sm text-white">
                    <div class="flex min-w-0 flex-1 items-start gap-2">
                        <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" />
                        </svg>
                        <div class="min-w-0">
                            <p>
                                Registrar / <strong class="font-bold">Nova Saída</strong>
                            </p>
                            <p class="mt-0.5 text-white/90">Clique no produto para dar baixa no estoque</p>
                        </div>
                    </div>
                    <svg class="h-8 w-8 shrink-0 text-white/95" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 11.25v8.25a1.5 1.5 0 01-1.5 1.5H5.25a1.5 1.5 0 01-1.5-1.5v-8.25M12 4.875A2.625 2.625 0 109.375 7.5H12m0-2.625V7.5m0-2.625A2.625 2.625 0 1114.625 7.5H12m-8.25 3.75h16.5a1.125 1.125 0 00.375-1.05V6a1.125 1.125 0 00-.375-1.05L12 .75 3.375 4.95A1.125 1.125 0 003 6v4.95a1.125 1.125 0 00.375 1.05z" />
                    </svg>
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
                            class="w-full rounded-lg border border-gray-300 bg-white py-2.5 pl-10 pr-4 text-sm text-gray-900 placeholder:text-gray-400 shadow-sm focus:border-red-500 focus:outline-none focus:ring-1 focus:ring-red-500"
                        />
                    </div>

                    <div class="mt-4 flex flex-wrap gap-2">
                        <button
                            type="button"
                            @click="categoriaId = null"
                            :class="categoriaId === null ? 'rounded-full bg-red-600 px-4 py-1.5 text-sm font-semibold text-white shadow-sm' : 'rounded-full border border-gray-300 bg-white px-4 py-1.5 text-sm font-semibold text-gray-700 hover:bg-gray-50'"
                        >
                            Todos
                        </button>
                        @foreach ($categorias as $cat)
                            <button
                                type="button"
                                @click="categoriaId = {{ $cat->id }}"
                                :class="categoriaId === {{ $cat->id }} ? 'rounded-full bg-red-600 px-4 py-1.5 text-sm font-semibold text-white shadow-sm' : 'rounded-full border border-gray-300 bg-white px-4 py-1.5 text-sm font-semibold text-gray-700 hover:bg-gray-50'"
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
                                <div class="flex aspect-square items-center justify-center bg-rose-50">
                                    <svg class="h-16 w-16 text-rose-200" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" />
                                    </svg>
                                </div>
                                <div class="flex flex-1 flex-col p-4">
                                    <p class="font-bold text-gray-900" x-text="p.nome"></p>
                                    <p class="mt-0.5 text-xs text-gray-500" x-text="p.categoria"></p>
                                    <p
                                        class="mt-2 text-sm font-bold"
                                        :class="p.estoque <= 5 ? 'text-orange-600' : 'text-gray-900'"
                                        x-text="'Est: ' + p.estoque + ' UN'"
                                    ></p>
                                    <button
                                        type="button"
                                        @click="openModal(p)"
                                        :disabled="p.estoque < 1"
                                        :class="p.estoque < 1 ? 'mt-3 w-full cursor-not-allowed rounded-lg bg-gray-300 py-2 text-sm font-semibold text-gray-500' : 'mt-3 w-full rounded-lg bg-red-600 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-red-700'"
                                    >
                                        - Saída
                                    </button>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            <div class="mt-8 overflow-hidden rounded-xl border border-gray-200/80 bg-white shadow-md">
                <div class="border-b border-gray-100 px-4 py-4 sm:px-6">
                    <h2 class="text-lg font-bold text-gray-900">Histórico de Saídas</h2>
                    <p class="mt-0.5 text-sm text-gray-500">Últimas 50 saídas registradas</p>
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
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($saidas as $s)
                                <tr class="bg-white hover:bg-gray-50/80">
                                    <td class="whitespace-nowrap px-4 py-3 text-gray-700 sm:px-6">{{ $s->created_at->format('d/m/Y H:i') }}</td>
                                    <td class="px-4 py-3 font-medium text-gray-900 sm:px-6">{{ $s->product?->nome ?? '—' }}</td>
                                    <td class="whitespace-nowrap px-4 py-3 font-semibold text-red-600 sm:px-6">-{{ $s->quantidade }} UN</td>
                                    <td class="max-w-xs truncate px-4 py-3 text-gray-600 sm:px-6" title="{{ $s->observacao }}">{{ $s->observacao ?: '—' }}</td>
                                    <td class="whitespace-nowrap px-4 py-3 text-gray-700 sm:px-6">{{ $s->user?->name ?? '—' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-16 text-center sm:px-6">
                                        <div class="mx-auto flex max-w-sm flex-col items-center">
                                            <div class="flex h-14 w-14 items-center justify-center rounded-full bg-red-50 text-red-600">
                                                <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5" />
                                                </svg>
                                            </div>
                                            <p class="mt-4 text-sm font-medium text-gray-600">Nenhuma saída registrada ainda</p>
                                            <p class="mt-1 text-sm text-gray-500">Use os botões acima para registrar a primeira saída</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Modal Registrar Saída --}}
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
                aria-labelledby="modal-saida-titulo"
                @click.stop
            >
                <div class="flex items-center justify-between border-b border-gray-100 px-5 py-4">
                    <h2 id="modal-saida-titulo" class="text-lg font-bold text-gray-900">Registrar Saída</h2>
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
                <form method="post" action="{{ route('saidas.store') }}" class="px-5 py-4">
                    @csrf
                    <input type="hidden" name="product_id" :value="selected ? selected.id : ''" />

                    <div class="rounded-lg border border-rose-200 bg-rose-50 px-4 py-3 text-sm">
                        <p class="font-semibold text-gray-900" x-text="selected ? selected.nome : ''"></p>
                        <p class="mt-1 text-gray-700" x-show="selected" x-text="selected ? 'Disponível: ' + selected.estoque + ' UN' : ''"></p>
                    </div>

                    <div class="mt-5">
                        <label for="quantidade_saida" class="block text-sm font-medium text-gray-700">Quantidade <span class="text-red-500">*</span></label>
                        <input
                            type="number"
                            name="quantidade"
                            id="quantidade_saida"
                            min="1"
                            required
                            x-model.number="qty"
                            :max="selected && selected.estoque > 0 ? selected.estoque : 1"
                            class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500"
                        />
                        <p class="mt-1 text-xs text-gray-500" x-show="selected" x-text="selected ? 'Máximo: ' + selected.estoque + ' UN' : ''"></p>
                    </div>
                    <div class="mt-4">
                        <label for="motivo_saida" class="block text-sm font-medium text-gray-700">Motivo (opcional)</label>
                        <input
                            type="text"
                            name="motivo"
                            id="motivo_saida"
                            placeholder="Ex: Venda avulsa, perda, dano"
                            x-model="motivo"
                            class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500"
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
                            :disabled="!canSubmit"
                            :class="canSubmit ? 'rounded-lg bg-red-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-red-700' : 'cursor-not-allowed rounded-lg bg-red-300 px-4 py-2.5 text-sm font-semibold text-white'"
                        >
                            Confirmar Saída
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
