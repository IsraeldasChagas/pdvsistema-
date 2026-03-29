<x-app-layout>
    <script>
        window.estoqueEntregaModal = function (semVendedores) {
            return {
                semVendedores: !!semVendedores,
                modalOpen: false,
                selected: null,
                qty: 1,
                vendedorId: '',
                openModal(p) {
                    if (!p || p.estoque < 1 || this.semVendedores) {
                        return;
                    }
                    this.selected = p;
                    this.qty = 1;
                    this.vendedorId = '';
                    this.modalOpen = true;
                },
                closeModal() {
                    this.modalOpen = false;
                    this.selected = null;
                    this.vendedorId = '';
                },
                get canSubmit() {
                    if (!this.selected || this.selected.estoque < 1 || !this.vendedorId) {
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
        class="px-4 py-8 sm:px-6 lg:px-8"
        x-data="estoqueEntregaModal(@js($semVendedores))"
        @keydown.escape.window="closeModal()"
    >
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

        @if ($semVendedores)
            <div class="mb-4 rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900">
                Cadastre pelo menos um usuário (vendedor) no sistema para registrar entregas.
            </div>
        @endif

        <h1 class="text-3xl font-bold tracking-tight text-gray-900">Estoque</h1>

        <div class="mt-4 flex flex-wrap gap-3">
            <a
                href="{{ route('estoque.historico') }}"
                class="inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 shadow-sm transition hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-400 focus:ring-offset-2"
            >
                Histórico
            </a>
            <a
                href="{{ route('modulos.produtos') }}"
                class="inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 shadow-sm transition hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-400 focus:ring-offset-2"
            >
                Produtos
            </a>
        </div>

        <div class="mt-8 overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-left text-sm">
                    <thead class="bg-slate-100">
                        <tr>
                            <th scope="col" class="whitespace-nowrap px-4 py-3 font-semibold text-gray-700">Código</th>
                            <th scope="col" class="whitespace-nowrap px-4 py-3 font-semibold text-gray-700">Marca</th>
                            <th scope="col" class="whitespace-nowrap px-4 py-3 font-semibold text-gray-700">Nome</th>
                            <th scope="col" class="whitespace-nowrap px-4 py-3 font-semibold text-gray-700">Categoria</th>
                            <th scope="col" class="min-w-[160px] px-4 py-3 font-semibold text-gray-700">Onde está</th>
                            <th scope="col" class="min-w-[280px] px-4 py-3 font-semibold text-gray-700">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @forelse ($produtos as $p)
                            <tr class="hover:bg-gray-50/80">
                                <td class="whitespace-nowrap px-4 py-3 text-gray-900">{{ $p->codigo }}</td>
                                <td class="whitespace-nowrap px-4 py-3 text-gray-700">{{ $p->marca ?: '—' }}</td>
                                <td class="whitespace-nowrap px-4 py-3 font-medium text-gray-900">{{ $p->nome }}</td>
                                <td class="whitespace-nowrap px-4 py-3 text-gray-700">{{ $p->category?->nome ?? '—' }}</td>
                                <td class="px-4 py-3 text-gray-700">
                                    <div class="space-y-0.5 leading-snug">
                                        <div>Loja: {{ $p->estoque }}</div>
                                        <div class="font-medium text-gray-900">Total: {{ $p->estoque }} UN</div>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex flex-wrap items-center gap-x-2 gap-y-1 text-xs sm:text-sm">
                                        <a href="{{ route('estoque.movimento.form', [$p, 'entrada']) }}" class="font-semibold text-green-600 hover:text-green-700">+ Entrada</a>
                                        <span class="text-gray-300">|</span>
                                        <a href="{{ route('estoque.movimento.form', [$p, 'saida']) }}" class="font-semibold text-red-600 hover:text-red-700">- Saída</a>
                                        <span class="text-gray-300">|</span>
                                        <button
                                            type="button"
                                            @click="openModal(@js(['id' => $p->id, 'nome' => $p->nome, 'estoque' => (int) $p->estoque]))"
                                            @disabled($semVendedores || $p->estoque < 1)
                                            class="font-semibold disabled:cursor-not-allowed disabled:text-gray-400 enabled:text-purple-600 enabled:hover:text-purple-700"
                                        >
                                            Entregar
                                        </button>
                                        <span class="text-gray-300">|</span>
                                        <a href="{{ route('estoque.historico', ['produto' => $p->id]) }}" class="font-semibold text-gray-500 hover:text-gray-700">Histórico</a>
                                        <span class="inline-flex shrink-0 text-lg leading-none" title="Entrega">🚚</span>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-12 text-center text-sm text-gray-500">
                                    Nenhum produto cadastrado.
                                    <a href="{{ route('produtos.create') }}" class="font-medium text-blue-600 hover:text-blue-500">Cadastrar produto</a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Modal Entregar para Vendedor --}}
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
                aria-labelledby="modal-estoque-entrega-titulo"
                @click.stop
            >
                <div class="flex items-center justify-between border-b border-gray-100 px-5 py-4">
                    <h2 id="modal-estoque-entrega-titulo" class="text-lg font-bold text-gray-900">Entregar para Vendedor</h2>
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
                <form method="post" action="{{ route('entregas.store') }}" class="px-5 py-4">
                    @csrf
                    <input type="hidden" name="return_to" value="estoque" />
                    <input type="hidden" name="product_id" :value="selected ? selected.id : ''" />

                    <div class="rounded-lg border border-violet-200 bg-violet-50 px-4 py-3 text-sm">
                        <p class="font-semibold text-gray-900" x-text="selected ? selected.nome : ''"></p>
                        <p class="mt-1 text-gray-700" x-show="selected" x-text="selected ? 'Disponível na loja: ' + selected.estoque + ' UN' : ''"></p>
                    </div>

                    <div class="mt-5">
                        <label for="estoque_entrega_vendedor_id" class="block text-sm font-medium text-gray-700">Vendedor de rua <span class="text-red-500">*</span></label>
                        <select
                            name="vendedor_id"
                            id="estoque_entrega_vendedor_id"
                            x-model="vendedorId"
                            required
                            class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-[#8b5cf6] focus:ring-[#8b5cf6]"
                        >
                            <option value="">-- Selecione --</option>
                            @foreach ($vendedores as $v)
                                <option value="{{ $v->id }}">{{ $v->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mt-4">
                        <label for="estoque_entrega_quantidade" class="block text-sm font-medium text-gray-700">Quantidade <span class="text-red-500">*</span></label>
                        <input
                            type="number"
                            name="quantidade"
                            id="estoque_entrega_quantidade"
                            min="1"
                            required
                            x-model.number="qty"
                            :max="selected && selected.estoque > 0 ? selected.estoque : 1"
                            class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-[#8b5cf6] focus:ring-[#8b5cf6]"
                        />
                        <p class="mt-1 text-xs text-gray-500" x-show="selected" x-text="selected ? 'Máximo na loja: ' + selected.estoque + ' UN' : ''"></p>
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
                            :class="canSubmit ? 'rounded-lg bg-[#8b5cf6] px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-violet-700' : 'cursor-not-allowed rounded-lg bg-violet-300 px-4 py-2.5 text-sm font-semibold text-white'"
                        >
                            Confirmar Entrega
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
