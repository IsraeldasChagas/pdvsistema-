@php
    $brl = fn ($v) => number_format((float) $v, 2, ',', '.');
    $tabHref = fn (string $nome) => route('modulos.relatorios', [
        'data_inicio' => $dataInicioFmt,
        'data_fim' => $dataFimFmt,
        'estoque_min' => $estoqueMinFiltro,
        'aba' => $nome,
    ]);
    $tabClass = fn (string $nome) =>
        $aba === $nome
            ? 'flex flex-col items-start rounded-xl border-2 border-sky-300 bg-sky-50 p-4 text-left shadow-sm ring-1 ring-sky-100 transition hover:bg-sky-50/90'
            : 'flex flex-col items-start rounded-xl border border-gray-200 bg-white p-4 text-left shadow-sm transition hover:border-gray-300 hover:bg-gray-50/80';
@endphp

<x-app-layout>
    <div class="min-h-[calc(100vh-3.5rem)] bg-[#f8f9fa] px-4 py-8 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-7xl">
            <div class="flex gap-3">
                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-white text-gray-700 shadow-sm ring-1 ring-gray-200">
                    <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                    </svg>
                </div>
                <div>
                    <h1 class="text-3xl font-bold tracking-tight text-gray-900">Relatórios</h1>
                    <p class="mt-0.5 text-sm text-gray-500">Análise de vendas, produtos e desempenho</p>
                </div>
            </div>

            <form method="get" action="{{ route('modulos.relatorios') }}" class="mt-8 rounded-xl border border-gray-200 bg-white p-4 shadow-sm sm:p-5">
                <input type="hidden" name="aba" value="{{ $aba }}" />
                <div class="flex flex-col gap-4 lg:flex-row lg:flex-wrap lg:items-end">
                    <div class="min-w-[140px] flex-1">
                        <label for="data_inicio" class="block text-sm font-medium text-gray-700">Data início</label>
                        <div class="relative mt-1.5">
                            <input
                                id="data_inicio"
                                name="data_inicio"
                                type="text"
                                value="{{ $dataInicioFmt }}"
                                placeholder="dd/mm/aaaa"
                                autocomplete="off"
                                class="w-full rounded-lg border border-gray-300 bg-white py-2.5 pl-3 pr-10 text-sm text-gray-900 shadow-sm focus:border-violet-400 focus:outline-none focus:ring-1 focus:ring-violet-400"
                            />
                            <span class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5a2.25 2.25 0 002.25-2.25m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5a2.25 2.25 0 012.25 2.25v7.5" />
                                </svg>
                            </span>
                        </div>
                    </div>
                    <div class="min-w-[140px] flex-1">
                        <label for="data_fim" class="block text-sm font-medium text-gray-700">Data fim</label>
                        <div class="relative mt-1.5">
                            <input
                                id="data_fim"
                                name="data_fim"
                                type="text"
                                value="{{ $dataFimFmt }}"
                                placeholder="dd/mm/aaaa"
                                autocomplete="off"
                                class="w-full rounded-lg border border-gray-300 bg-white py-2.5 pl-3 pr-10 text-sm text-gray-900 shadow-sm focus:border-violet-400 focus:outline-none focus:ring-1 focus:ring-violet-400"
                            />
                            <span class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5a2.25 2.25 0 002.25-2.25m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5a2.25 2.25 0 012.25 2.25v7.5" />
                                </svg>
                            </span>
                        </div>
                    </div>
                    <div class="w-full min-w-[120px] max-w-[160px]">
                        <label for="estoque_min" class="block text-sm font-medium text-gray-700">Estoque baixo (≤ UN)</label>
                        <input
                            id="estoque_min"
                            name="estoque_min"
                            type="number"
                            min="0"
                            max="9999"
                            value="{{ $estoqueMinFiltro }}"
                            class="mt-1.5 w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm text-gray-900 shadow-sm focus:border-violet-400 focus:outline-none focus:ring-1 focus:ring-violet-400"
                        />
                    </div>
                    <button
                        type="submit"
                        class="rounded-lg bg-violet-600 px-6 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-violet-700 focus:outline-none focus:ring-2 focus:ring-violet-500 focus:ring-offset-2"
                    >
                        Aplicar
                    </button>
                </div>
            </form>

            <div class="mt-6 grid grid-cols-2 gap-3 lg:grid-cols-5">
                <a href="{{ $tabHref('resumo') }}" class="{{ $tabClass('resumo') }}">
                    <div class="flex h-10 w-10 items-end justify-center gap-0.5 pb-0.5" aria-hidden="true">
                        <span class="h-5 w-2 rounded-sm bg-blue-500"></span>
                        <span class="h-7 w-2 rounded-sm bg-emerald-500"></span>
                        <span class="h-4 w-2 rounded-sm bg-pink-500"></span>
                    </div>
                    <p class="mt-3 text-sm leading-snug text-gray-900">
                        <span class="font-bold">Resumo</span> <span class="font-normal text-gray-400">/</span> <span class="text-gray-600">Visão geral</span>
                    </p>
                </a>
                <a href="{{ $tabHref('vendas') }}" class="{{ $tabClass('vendas') }}">
                    <svg class="h-10 w-10 text-gray-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218a1.5 1.5 0 001.421-1.004l5.223-15.684a.75.75 0 00-1.183-.819l-2.682 8.09M7.5 14.25L5.106 5.106M7.5 14.25l-1.531 4.607M16.5 14.25l1.531-4.607M16.5 14.25h2.25M16.5 14.25H9.375" />
                    </svg>
                    <p class="mt-3 text-sm leading-snug text-gray-900">
                        <span class="font-bold">Vendas</span> <span class="font-normal text-gray-400">/</span> <span class="text-gray-600">Por período</span>
                    </p>
                </a>
                <a href="{{ $tabHref('produtos') }}" class="{{ $tabClass('produtos') }}">
                    <svg class="h-10 w-10 text-amber-800/80" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" />
                    </svg>
                    <p class="mt-3 text-sm leading-snug text-gray-900">
                        <span class="font-bold">Produtos</span> <span class="font-normal text-gray-400">/</span> <span class="text-gray-600">Mais vendidos</span>
                    </p>
                </a>
                <a href="{{ $tabHref('vendedores') }}" class="{{ $tabClass('vendedores') }}">
                    <svg class="h-10 w-10 text-gray-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                    </svg>
                    <p class="mt-3 text-sm leading-snug text-gray-900">
                        <span class="font-bold">Vendedores</span> <span class="font-normal text-gray-400">/</span> <span class="text-gray-600">Performance</span>
                    </p>
                </a>
                <a href="{{ $tabHref('estoque') }}" class="{{ $tabClass('estoque') }} col-span-2 lg:col-span-1">
                    <svg class="h-10 w-10 text-amber-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                    </svg>
                    <p class="mt-3 text-sm leading-snug text-gray-900">
                        <span class="font-bold">Estoque</span> <span class="font-normal text-gray-400">/</span> <span class="text-gray-600">Produtos baixos</span>
                    </p>
                </a>
            </div>

            @if ($aba === 'resumo')
                <div class="mt-8 overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
                    <div class="border-b border-gray-100 px-4 py-4 sm:px-6">
                        <h2 class="text-lg font-bold text-gray-900">Resumo Geral</h2>
                        <p class="mt-0.5 text-sm text-gray-500">
                            Período: {{ $inicio->format('d/m/Y') }} a {{ $fim->format('d/m/Y') }}
                        </p>
                    </div>
                    <div class="p-4 sm:p-6">
                        <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                            <div class="rounded-xl bg-violet-100/90 p-5 shadow-sm ring-1 ring-violet-200/50">
                                <p class="text-sm font-medium text-violet-900/80">Total em Vendas</p>
                                <p class="mt-2 text-3xl font-bold tracking-tight text-violet-950">R$ {{ $brl($totalVendas) }}</p>
                                <p class="mt-1 text-xs text-violet-800/70">No período selecionado</p>
                            </div>
                            <div class="rounded-xl bg-emerald-100/90 p-5 shadow-sm ring-1 ring-emerald-200/50">
                                <p class="text-sm font-medium text-emerald-900/80">Quantidade de Vendas</p>
                                <p class="mt-2 text-3xl font-bold tracking-tight text-emerald-950">{{ $qtdVendas }}</p>
                                <p class="mt-1 text-xs text-emerald-800/70">Transações realizadas</p>
                            </div>
                            <div class="rounded-xl bg-orange-100/90 p-5 shadow-sm ring-1 ring-orange-200/50">
                                <p class="text-sm font-medium text-orange-900/80">Comissões</p>
                                <p class="mt-2 text-3xl font-bold tracking-tight text-orange-950">R$ {{ $brl($totalComissoes) }}</p>
                                <p class="mt-1 text-xs text-orange-800/70">Total no período</p>
                            </div>
                        </div>
                        <p class="mt-6 text-sm text-gray-600">
                            Ticket médio:
                            <strong class="font-bold text-gray-900">R$ {{ $brl($ticketMedio) }}</strong>
                        </p>
                    </div>
                </div>
            @endif

            @if ($aba === 'vendas')
                <div class="mt-8 overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
                    <div class="border-b border-gray-100 px-4 py-4 sm:px-6">
                        <h2 class="text-lg font-bold text-gray-900">Vendas no período</h2>
                        <p class="mt-0.5 text-sm text-gray-500">{{ $inicio->format('d/m/Y') }} a {{ $fim->format('d/m/Y') }} · até 500 registros</p>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 text-left text-sm">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="px-4 py-3 font-semibold text-gray-700">Data</th>
                                    <th class="px-4 py-3 font-semibold text-gray-700">Vendedor</th>
                                    <th class="px-4 py-3 font-semibold text-gray-700">Total</th>
                                    <th class="px-4 py-3 font-semibold text-gray-700">Pagamento</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                                @forelse ($listaVendas as $v)
                                    <tr class="hover:bg-gray-50/80">
                                        <td class="whitespace-nowrap px-4 py-3 text-gray-700">{{ $v->created_at->format('d/m/Y H:i') }}</td>
                                        <td class="whitespace-nowrap px-4 py-3 text-gray-900">{{ $v->user?->name ?? '—' }}</td>
                                        <td class="whitespace-nowrap px-4 py-3 font-semibold text-gray-900">R$ {{ $brl($v->total) }}</td>
                                        <td class="whitespace-nowrap px-4 py-3 text-gray-600">
                                            {{ $formasPagamento[$v->forma_pagamento] ?? ($v->forma_pagamento ?? '—') }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-4 py-12 text-center text-gray-500">Nenhuma venda no período.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            @if ($aba === 'produtos')
                <div class="mt-8 overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
                    <div class="border-b border-gray-100 px-4 py-4 sm:px-6">
                        <h2 class="text-lg font-bold text-gray-900">Produtos mais vendidos</h2>
                        <p class="mt-0.5 text-sm text-gray-500">Por quantidade no período · até 50 itens</p>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 text-left text-sm">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="px-4 py-3 font-semibold text-gray-700">Produto</th>
                                    <th class="px-4 py-3 font-semibold text-gray-700">Categoria</th>
                                    <th class="px-4 py-3 font-semibold text-gray-700">Qtd vendida</th>
                                    <th class="px-4 py-3 font-semibold text-gray-700">Receita</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                                @forelse ($produtosRanking as $row)
                                    @php
                                        $prod = $produtosPorId->get($row->product_id);
                                    @endphp
                                    <tr class="hover:bg-gray-50/80">
                                        <td class="px-4 py-3 font-medium text-gray-900">{{ $prod?->nome ?? 'Produto #'.$row->product_id }}</td>
                                        <td class="px-4 py-3 text-gray-600">{{ $prod?->category?->nome ?? '—' }}</td>
                                        <td class="whitespace-nowrap px-4 py-3 text-gray-900">{{ (int) $row->qtd_total }} UN</td>
                                        <td class="whitespace-nowrap px-4 py-3 font-semibold text-gray-900">R$ {{ $brl($row->receita) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-4 py-12 text-center text-gray-500">Nenhuma venda com itens no período.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            @if ($aba === 'vendedores')
                <div class="mt-8 overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
                    <div class="border-b border-gray-100 px-4 py-4 sm:px-6">
                        <h2 class="text-lg font-bold text-gray-900">Performance por vendedor</h2>
                        <p class="mt-0.5 text-sm text-gray-500">Total faturado no PDV no período</p>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 text-left text-sm">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="px-4 py-3 font-semibold text-gray-700">Vendedor</th>
                                    <th class="px-4 py-3 font-semibold text-gray-700">Nº vendas</th>
                                    <th class="px-4 py-3 font-semibold text-gray-700">Total</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                                @forelse ($vendedoresRanking as $row)
                                    @php
                                        $u = $usersPorId->get($row->user_id);
                                    @endphp
                                    <tr class="hover:bg-gray-50/80">
                                        <td class="px-4 py-3 font-medium text-gray-900">{{ $u?->name ?? 'Usuário #'.$row->user_id }}</td>
                                        <td class="whitespace-nowrap px-4 py-3 text-gray-700">{{ (int) $row->n_vendas }}</td>
                                        <td class="whitespace-nowrap px-4 py-3 font-bold text-gray-900">R$ {{ $brl($row->total_rs) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="px-4 py-12 text-center text-gray-500">Nenhuma venda no período.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            @if ($aba === 'estoque')
                <div class="mt-8 overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
                    <div class="border-b border-gray-100 px-4 py-4 sm:px-6">
                        <h2 class="text-lg font-bold text-gray-900">Estoque baixo</h2>
                        <p class="mt-0.5 text-sm text-gray-500">
                            Produtos ativos com estoque ≤ {{ $estoqueMinFiltro }} UN (ajuste em “Estoque baixo” acima e clique em Aplicar)
                        </p>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 text-left text-sm">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="px-4 py-3 font-semibold text-gray-700">Código</th>
                                    <th class="px-4 py-3 font-semibold text-gray-700">Produto</th>
                                    <th class="px-4 py-3 font-semibold text-gray-700">Categoria</th>
                                    <th class="px-4 py-3 font-semibold text-gray-700">Estoque</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                                @forelse ($estoqueBaixo as $p)
                                    <tr class="hover:bg-gray-50/80">
                                        <td class="whitespace-nowrap px-4 py-3 text-gray-700">{{ $p->codigo }}</td>
                                        <td class="px-4 py-3 font-medium text-gray-900">{{ $p->nome }}</td>
                                        <td class="px-4 py-3 text-gray-600">{{ $p->category?->nome ?? '—' }}</td>
                                        <td class="whitespace-nowrap px-4 py-3 font-semibold text-orange-600">{{ $p->estoque }} UN</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-4 py-12 text-center text-gray-500">Nenhum produto abaixo do limite.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            <p class="mt-6 text-sm text-gray-500">
                Relatórios consideram o período selecionado. O limite de estoque baixo pode ser ajustado no campo “Estoque baixo (≤ UN)” e em
                <a href="{{ route('modulos.configuracoes') }}" class="font-medium text-violet-700 underline hover:text-violet-900">Configurações</a>
                (referência futura).
            </p>
        </div>
    </div>
</x-app-layout>
