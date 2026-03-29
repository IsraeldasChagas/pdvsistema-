<x-app-layout>
    <script>
        function parseBrMoneyPdv(str) {
            let s = String(str ?? '').trim().replace(/[^\d,.\-]/g, '');
            if (!s || s === '-') return 0;
            if (s.includes(',')) {
                s = s.replace(/\./g, '').replace(',', '.');
            }
            const n = parseFloat(s);
            return Number.isFinite(n) ? Math.round(n * 100) / 100 : 0;
        }

        window.pdvPage = function (produtos, finalizarUrl, csrfToken) {
            return {
                produtos,
                finalizarUrl,
                csrfToken,
                search: '',
                categoriaId: null,
                carrinho: [],
                descontoStr: '0,00',
                formaPagamento: 'dinheiro',
                finalizando: false,
                erroMsg: '',
                fmtRs(v) {
                    return Number(v).toFixed(2).replace('.', ',');
                },
                estoqueDisponivel(id) {
                    const p = this.produtos.find((x) => x.id === id);
                    return p ? p.estoque : 0;
                },
                qtdNoCarrinho(id) {
                    const it = this.carrinho.find((x) => x.id === id);
                    return it ? it.qtd : 0;
                },
                add(p) {
                    const pData = this.produtos.find((x) => x.id === p.id);
                    if (!pData || pData.estoque < 1) return;
                    const noCarrinho = this.qtdNoCarrinho(p.id);
                    if (noCarrinho + 1 > pData.estoque) return;
                    const i = this.carrinho.findIndex((x) => x.id === p.id);
                    if (i >= 0) {
                        this.carrinho[i].qtd++;
                    } else {
                        this.carrinho.push({
                            id: p.id,
                            nome: p.nome,
                            preco: p.preco,
                            preco_fmt: p.preco_fmt,
                            estoque: pData.estoque,
                            qtd: 1,
                        });
                    }
                },
                remove(idx) {
                    this.carrinho.splice(idx, 1);
                },
                inc(idx) {
                    const item = this.carrinho[idx];
                    const max = this.estoqueDisponivel(item.id);
                    if (item.qtd < max) item.qtd++;
                },
                dec(idx) {
                    const item = this.carrinho[idx];
                    if (item.qtd > 1) item.qtd--;
                    else this.remove(idx);
                },
                matches(p) {
                    if (this.categoriaId !== null && p.category_id !== this.categoriaId) return false;
                    const q = this.search.trim().toLowerCase();
                    if (!q) return true;
                    return p.nome.toLowerCase().includes(q) || String(p.codigo).toLowerCase().includes(q);
                },
                get filtered() {
                    return this.produtos.filter((p) => this.matches(p));
                },
                get totalItens() {
                    return this.carrinho.reduce((s, x) => s + x.qtd, 0);
                },
                get subtotal() {
                    return this.carrinho.reduce((s, x) => s + x.preco * x.qtd, 0);
                },
                get descontoNum() {
                    return parseBrMoneyPdv(this.descontoStr);
                },
                get totalLiquido() {
                    return Math.max(0, Math.round((this.subtotal - this.descontoNum) * 100) / 100);
                },
                get podeFinalizar() {
                    return this.carrinho.length > 0 && this.totalLiquido > 0 && !this.finalizando;
                },
                async finalizar() {
                    if (!this.podeFinalizar) return;
                    this.finalizando = true;
                    this.erroMsg = '';
                    try {
                        const res = await fetch(this.finalizarUrl, {
                            method: 'POST',
                            credentials: 'same-origin',
                            headers: {
                                'Content-Type': 'application/json',
                                Accept: 'application/json',
                                'X-CSRF-TOKEN': this.csrfToken,
                                'X-Requested-With': 'XMLHttpRequest',
                            },
                            body: JSON.stringify({
                                items: this.carrinho.map((i) => ({
                                    product_id: i.id,
                                    quantidade: i.qtd,
                                })),
                                desconto: this.descontoNum,
                                forma_pagamento: this.formaPagamento,
                            }),
                        });
                        const data = await res.json().catch(() => ({}));
                        if (res.ok && data.ok) {
                            window.location.reload();
                            return;
                        }
                        this.erroMsg = data.message || data.errors?.items?.[0] || 'Não foi possível finalizar a venda.';
                    } catch (e) {
                        this.erroMsg = 'Erro de conexão. Tente novamente.';
                    } finally {
                        this.finalizando = false;
                    }
                },
            };
        };
    </script>

    <div
        class="flex min-h-[calc(100vh-3.5rem)] flex-col bg-slate-100 lg:flex-row lg:items-stretch"
        x-data="pdvPage({{ $produtosAlpineJson }}, @js(route('venda.finalizar')), @js(csrf_token()))"
    >
        <div class="flex min-h-0 min-w-0 flex-1 flex-col overflow-y-auto p-4 sm:p-6">
            <h1 class="text-2xl font-bold tracking-tight text-gray-900">Mini PDV</h1>
            <p class="mt-0.5 text-sm text-gray-500">Clique nos produtos para adicionar ao carrinho</p>

            <div class="relative mt-6 max-w-2xl">
                <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                    </svg>
                </span>
                <input
                    type="search"
                    x-model="search"
                    placeholder="Buscar produto por nome ou código..."
                    class="w-full rounded-xl border border-gray-300 bg-white py-3 pl-10 pr-4 text-sm text-gray-900 shadow-sm placeholder:text-gray-400 focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500"
                />
            </div>

            <div class="mt-4 flex flex-wrap gap-2">
                <button
                    type="button"
                    @click="categoriaId = null"
                    :class="categoriaId === null ? 'rounded-full bg-amber-500 px-4 py-1.5 text-sm font-semibold text-white shadow-sm' : 'rounded-full border border-gray-300 bg-white px-4 py-1.5 text-sm font-semibold text-gray-700 hover:bg-gray-50'"
                >
                    Todos
                </button>
                @foreach ($categorias as $cat)
                    <button
                        type="button"
                        @click="categoriaId = {{ $cat->id }}"
                        :class="categoriaId === {{ $cat->id }} ? 'rounded-full bg-amber-500 px-4 py-1.5 text-sm font-semibold text-white shadow-sm' : 'rounded-full border border-gray-300 bg-white px-4 py-1.5 text-sm font-semibold text-gray-700 hover:bg-gray-50'"
                    >
                        {{ $cat->nome }}
                    </button>
                @endforeach
            </div>

            <template x-if="filtered.length === 0">
                <p class="mt-8 text-center text-sm text-gray-500">Nenhum produto encontrado.</p>
            </template>

            <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-4">
                <template x-for="p in filtered" :key="p.id">
                    <button
                        type="button"
                        @click="add(p)"
                        :disabled="p.estoque < 1"
                        :class="p.estoque < 1 ? 'cursor-not-allowed opacity-50' : ''"
                        class="flex flex-col overflow-hidden rounded-xl border border-gray-200 bg-white text-left shadow-sm transition hover:border-amber-300 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-amber-500 focus:ring-offset-2"
                    >
                        <div class="flex aspect-[4/3] items-center justify-center bg-gray-100">
                            <svg class="h-20 w-20 text-gray-300" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" />
                            </svg>
                        </div>
                        <div class="flex flex-1 flex-col p-4">
                            <p class="font-bold text-gray-900" x-text="p.nome"></p>
                            <p class="mt-0.5 text-xs text-gray-500" x-text="p.categoria"></p>
                            <p class="mt-3 text-xl font-bold text-amber-600" x-text="'R$ ' + p.preco_fmt"></p>
                            <p class="mt-1 text-sm font-medium text-orange-600" x-text="'Loja: ' + p.estoque + ' UN'"></p>
                        </div>
                    </button>
                </template>
            </div>
        </div>

        <aside class="flex min-h-[280px] w-full shrink-0 flex-col border-t border-slate-800 bg-[#1e293b] lg:min-h-0 lg:w-[22rem] lg:border-l lg:border-t-0 xl:w-96">
            <div class="flex items-center justify-between border-b border-slate-700/80 px-4 py-4 sm:px-5">
                <div class="flex items-center gap-2">
                    <svg class="h-6 w-6 text-slate-300" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218a1.5 1.5 0 001.421-1.004l5.223-15.684a.75.75 0 00-1.183-.819l-2.682 8.09M7.5 14.25L5.106 5.106M7.5 14.25l-1.531 4.607M16.5 14.25l1.531-4.607M16.5 14.25h2.25M16.5 14.25H9.375" />
                    </svg>
                    <h2 class="text-lg font-bold text-white">Carrinho</h2>
                </div>
                <span
                    class="flex h-8 min-w-[2rem] items-center justify-center rounded-full bg-amber-500 px-2 text-sm font-bold text-slate-900 ring-2 ring-amber-400/50"
                    x-text="totalItens"
                >0</span>
            </div>

            <div class="flex min-h-0 flex-1 flex-col">
                <template x-if="carrinho.length === 0">
                    <div class="flex flex-1 flex-col items-center justify-center px-4 py-12 text-center sm:px-5">
                        <svg class="mb-3 h-14 w-14 text-slate-600" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218a1.5 1.5 0 001.421-1.004l5.223-15.684a.75.75 0 00-1.183-.819l-2.682 8.09M7.5 14.25L5.106 5.106M7.5 14.25l-1.531 4.607M16.5 14.25l1.531-4.607M16.5 14.25h2.25M16.5 14.25H9.375" />
                        </svg>
                        <p class="text-sm text-slate-400">Clique nos produtos para adicionar</p>
                    </div>
                </template>
                <template x-if="carrinho.length > 0">
                    <div class="flex min-h-0 flex-1 flex-col overflow-y-auto">
                        <ul class="space-y-3 p-4 sm:p-5">
                            <template x-for="(item, idx) in carrinho" :key="item.id">
                                <li class="rounded-lg bg-slate-800/90 p-3 text-sm text-white">
                                    <p class="font-medium leading-snug" x-text="item.nome"></p>
                                    <p class="mt-1 text-xs text-slate-400">
                                        R$ <span x-text="item.preco_fmt"></span> × <span x-text="item.qtd"></span> = R$
                                        <span x-text="fmtRs(item.preco * item.qtd)"></span>
                                    </p>
                                    <div class="mt-3 flex items-center gap-2">
                                        <button
                                            type="button"
                                            @click="dec(idx)"
                                            class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg border border-slate-600 bg-slate-700 text-lg font-bold text-white hover:bg-slate-600"
                                        >
                                            −
                                        </button>
                                        <input
                                            type="text"
                                            readonly
                                            :value="item.qtd"
                                            class="h-9 w-12 rounded-lg border border-slate-600 bg-slate-900 text-center text-sm font-semibold text-white"
                                        />
                                        <button
                                            type="button"
                                            @click="inc(idx)"
                                            class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg border border-slate-600 bg-slate-700 text-lg font-bold text-white hover:bg-slate-600"
                                        >
                                            +
                                        </button>
                                        <button
                                            type="button"
                                            @click="remove(idx)"
                                            class="ml-auto flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-red-600/90 text-sm font-bold text-white hover:bg-red-500"
                                            aria-label="Remover"
                                        >
                                            ✕
                                        </button>
                                    </div>
                                </li>
                            </template>
                        </ul>

                        <div class="mt-auto space-y-4 border-t border-slate-700/80 p-4 sm:p-5">
                            <div>
                                <label for="desconto_pdv" class="block text-xs font-medium text-slate-400">Desconto (R$)</label>
                                <input
                                    id="desconto_pdv"
                                    type="text"
                                    x-model="descontoStr"
                                    inputmode="decimal"
                                    class="mt-1 block w-full rounded-lg border border-slate-600 bg-slate-900 px-3 py-2 text-sm font-medium text-white focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500"
                                />
                            </div>
                            <div>
                                <label for="forma_pagamento" class="block text-xs font-medium text-slate-400">Forma de pagamento</label>
                                <select
                                    id="forma_pagamento"
                                    x-model="formaPagamento"
                                    class="mt-1 block w-full rounded-lg border border-slate-600 bg-slate-900 px-3 py-2 text-sm font-medium text-white focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500"
                                >
                                    @foreach ($formasPagamento as $valor => $label)
                                        <option value="{{ $valor }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="flex items-end justify-between gap-4 pt-1 text-white">
                                <span class="text-base font-medium text-slate-300">Total</span>
                                <span class="text-2xl font-bold tracking-tight text-amber-300">R$ <span x-text="fmtRs(totalLiquido)"></span></span>
                            </div>
                            <p x-show="erroMsg" x-cloak class="text-sm text-red-400" x-text="erroMsg"></p>
                            <button
                                type="button"
                                @click="finalizar()"
                                :disabled="!podeFinalizar"
                                :class="podeFinalizar ? 'w-full rounded-xl bg-emerald-600 py-3.5 text-base font-bold text-white shadow-lg transition hover:bg-emerald-500' : 'w-full cursor-not-allowed rounded-xl bg-slate-600 py-3.5 text-base font-bold text-slate-400'"
                            >
                                <span x-show="!finalizando">Finalizar Venda</span>
                                <span x-show="finalizando" x-cloak>Processando…</span>
                            </button>
                        </div>
                    </div>
                </template>
            </div>
        </aside>
    </div>
</x-app-layout>
