<x-app-layout>
    @php
        $openManualModal =
            session('open_manual_modal')
            || ($errors->any() && old('tipo') !== null);
        $openCatModal = session('open_cat_modal') || ($errors->any() && old('cf_cat_nome') !== null);
    @endphp
    <div
        x-data="{ showManualModal: {{ $openManualModal ? 'true' : 'false' }}, showCatModal: {{ $openCatModal ? 'true' : 'false' }} }"
        class="min-h-[calc(100vh-3.5rem)] bg-[#f8f9fa] px-4 py-8 sm:px-6 lg:px-8"
    >
        <div class="mx-auto max-w-6xl">
            @if (session('status'))
                <div class="mb-4 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                    <p class="font-semibold">Verifique os campos.</p>
                    <ul class="mt-2 list-inside list-disc">
                        @foreach ($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                <div class="flex items-start gap-3">
                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-white text-slate-700 shadow-sm ring-1 ring-gray-200">
                        <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" />
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold tracking-tight text-gray-900">Fluxo de caixa</h1>
                        <p class="mt-1 text-sm text-gray-600">Entradas (vendas no caixa), saídas (despesas variáveis + estimativa de fixas mensais) e saldo acumulado no período.</p>
                    </div>
                </div>

                <div class="flex flex-col items-stretch gap-3 sm:flex-row sm:items-end">
                    <div class="sm:order-2">
                        <button type="button" class="btn-pdv btn-pdv-primary w-full px-4 py-2 text-sm sm:w-auto" @click="showManualModal = true">
                            + Lançamento manual
                        </button>
                    </div>

                    <form method="get" action="{{ route('financeiro.fluxo_caixa') }}" class="flex flex-wrap items-end gap-3 rounded-xl border border-gray-200 bg-white p-4 shadow-sm sm:order-1">
                    <div>
                        <label for="inicio" class="block text-xs font-bold uppercase tracking-wide text-gray-500">Início</label>
                        <input
                            id="inicio"
                            name="inicio"
                            type="date"
                            value="{{ $inicio }}"
                            class="mt-1 rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 shadow-sm focus:border-sky-500 focus:outline-none focus:ring-1 focus:ring-sky-500"
                        />
                    </div>
                    <div>
                        <label for="fim" class="block text-xs font-bold uppercase tracking-wide text-gray-500">Fim</label>
                        <input
                            id="fim"
                            name="fim"
                            type="date"
                            value="{{ $fim }}"
                            class="mt-1 rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 shadow-sm focus:border-sky-500 focus:outline-none focus:ring-1 focus:ring-sky-500"
                        />
                    </div>
                    <button type="submit" class="btn-pdv btn-pdv-primary px-4 py-2 text-sm">Atualizar</button>
                    </form>
                </div>
            </div>

            @php
                $t = $fluxo['totais'];
            @endphp

            <div class="mt-8 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                    <p class="text-xs font-bold uppercase tracking-wide text-gray-500">Entradas</p>
                    <p class="mt-2 text-2xl font-bold tabular-nums text-emerald-700">R$ {{ number_format($t['entradas'], 2, ',', '.') }}</p>
                    <p class="mt-1 text-xs text-gray-500">Vendas no caixa + entradas manuais</p>
                </div>
                <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                    <p class="text-xs font-bold uppercase tracking-wide text-gray-500">Saídas (variáveis)</p>
                    <p class="mt-2 text-2xl font-bold tabular-nums text-rose-700">R$ {{ number_format($t['saidas_variaveis'], 2, ',', '.') }}</p>
                    <p class="mt-1 text-xs text-gray-500">Despesas variáveis + saídas manuais (por data)</p>
                </div>
                <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                    <p class="text-xs font-bold uppercase tracking-wide text-gray-500">Saídas (fixas est.)</p>
                    <p class="mt-2 text-2xl font-bold tabular-nums text-orange-700">R$ {{ number_format($t['saidas_fixas'], 2, ',', '.') }}</p>
                    <p class="mt-1 text-xs text-gray-500">Mensais ativas, por vencimento</p>
                </div>
                <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                    <p class="text-xs font-bold uppercase tracking-wide text-gray-500">Saldo no período</p>
                    <p class="mt-2 text-2xl font-bold tabular-nums {{ $t['saldo'] >= 0 ? 'text-blue-800' : 'text-red-700' }}">
                        R$ {{ number_format($t['saldo'], 2, ',', '.') }}
                    </p>
                    <p class="mt-1 text-xs text-gray-500">Entradas − todas as saídas</p>
                </div>
            </div>

            <div class="mt-4 rounded-lg border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-700">
                <p class="font-semibold text-slate-900">Como este painel é montado</p>
                <ul class="mt-2 list-inside list-disc space-y-1 text-slate-600">
                    @foreach ($fluxo['notas'] as $nota)
                        <li>{{ $nota }}</li>
                    @endforeach
                </ul>
            </div>

            <div class="mt-8 grid grid-cols-1 gap-6 lg:grid-cols-2">
                <section class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm lg:col-span-2">
                    <div class="border-b border-gray-100 px-5 py-4">
                        <h2 class="text-base font-bold text-gray-900">Entradas × saídas (por dia)</h2>
                        <p class="mt-0.5 text-sm text-gray-500">Linha verde: vendas no caixa. Linha vermelha: saídas (variáveis + estimativa fixa no dia).</p>
                    </div>
                    <div class="p-4 sm:p-6">
                        <div class="relative h-[300px] w-full">
                            <canvas id="chartEvol"></canvas>
                        </div>
                    </div>
                </section>

                <section class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm lg:col-span-2">
                    <div class="border-b border-gray-100 px-5 py-4">
                        <h2 class="text-base font-bold text-gray-900">Saldo acumulado</h2>
                        <p class="mt-0.5 text-sm text-gray-500">Soma dia a dia: entradas − saídas.</p>
                    </div>
                    <div class="p-4 sm:p-6">
                        <div class="relative h-[260px] w-full">
                            <canvas id="chartAcum"></canvas>
                        </div>
                    </div>
                </section>

                <section class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
                    <div class="border-b border-gray-100 px-5 py-4">
                        <h2 class="text-base font-bold text-gray-900">Saídas variáveis por categoria</h2>
                        <p class="mt-0.5 text-sm text-gray-500">Total no período.</p>
                    </div>
                    <div class="p-4 sm:p-6">
                        <div class="relative mx-auto h-[280px] max-w-md">
                            <canvas id="chartCat"></canvas>
                        </div>
                        @if (empty($fluxo['categorias_saidas']))
                            <p class="mt-4 text-center text-sm text-gray-500">Sem despesas variáveis no período.</p>
                        @endif
                    </div>
                </section>

                <section class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
                    <div class="border-b border-gray-100 px-5 py-4">
                        <h2 class="text-base font-bold text-gray-900">Entradas por forma de pagamento</h2>
                        <p class="mt-0.5 text-sm text-gray-500">Vendas no caixa no período.</p>
                    </div>
                    <div class="p-4 sm:p-6">
                        <div class="relative mx-auto h-[280px] max-w-md">
                            <canvas id="chartForma"></canvas>
                        </div>
                        @if (empty($fluxo['formas_entrada']))
                            <p class="mt-4 text-center text-sm text-gray-500">Sem vendas no período.</p>
                        @endif
                    </div>
                </section>
            </div>

            <section class="mt-8 overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
                <div class="border-b border-gray-100 px-5 py-4">
                    <h2 class="text-base font-bold text-gray-900">Lançamentos manuais</h2>
                    <p class="mt-0.5 text-sm text-gray-500">Entradas/saídas lançadas manualmente (ajustes, retiradas, aportes, pagamentos fora do PDV).</p>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-left text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="whitespace-nowrap px-4 py-3 font-semibold text-gray-700">Data</th>
                                <th class="whitespace-nowrap px-4 py-3 font-semibold text-gray-700">Tipo</th>
                                <th class="whitespace-nowrap px-4 py-3 font-semibold text-gray-700">Descrição</th>
                                <th class="whitespace-nowrap px-4 py-3 font-semibold text-gray-700">Categoria</th>
                                <th class="whitespace-nowrap px-4 py-3 font-semibold text-gray-700">Origem</th>
                                <th class="whitespace-nowrap px-4 py-3 font-semibold text-gray-700">Valor</th>
                                <th class="whitespace-nowrap px-4 py-3 font-semibold text-gray-700"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            @forelse ($lancamentos as $e)
                                <tr class="hover:bg-gray-50/80">
                                    <td class="whitespace-nowrap px-4 py-3 text-gray-700">{{ $e->data_movimento?->format('d/m/Y') ?? '—' }}</td>
                                    <td class="whitespace-nowrap px-4 py-3">
                                        @if ($e->tipo === 'entrada')
                                            <span class="inline-flex rounded-full bg-emerald-100 px-2.5 py-0.5 text-xs font-medium text-emerald-800">Entrada</span>
                                        @else
                                            <span class="inline-flex rounded-full bg-rose-100 px-2.5 py-0.5 text-xs font-medium text-rose-800">Saída</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 font-medium text-gray-900">{{ $e->descricao }}</td>
                                    <td class="whitespace-nowrap px-4 py-3 text-gray-700">{{ $e->category?->nome ?? '—' }}</td>
                                    <td class="whitespace-nowrap px-4 py-3 text-gray-700">{{ $e->origem ?: '—' }}</td>
                                    <td class="whitespace-nowrap px-4 py-3 font-semibold {{ $e->tipo === 'entrada' ? 'text-emerald-700' : 'text-rose-700' }}">
                                        R$ {{ number_format((float) $e->valor, 2, ',', '.') }}
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-3 text-right">
                                        <form method="post" action="{{ route('financeiro.fluxo_caixa.lancamentos.destroy', $e) }}">
                                            @csrf
                                            @method('DELETE')
                                            <input type="hidden" name="inicio" value="{{ $inicio }}">
                                            <input type="hidden" name="fim" value="{{ $fim }}">
                                            <button type="submit" class="btn-pdv-ghost btn-pdv-ghost-red px-3 py-1.5 text-xs">Excluir</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-4 py-10 text-center text-sm text-gray-500">
                                        Nenhum lançamento manual no período.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
        </div>

        <div
            x-show="showManualModal"
            x-cloak
            class="fixed inset-0 z-50 flex items-end justify-center bg-black/50 p-6 backdrop-blur-[2px] sm:items-center sm:p-12"
            @click="showManualModal = false"
        >
        <div
            @click.stop
            class="relative z-10 w-full max-w-xl overflow-hidden rounded-xl bg-white shadow-xl ring-1 ring-black/10"
        >
            <form
                action="{{ route('financeiro.fluxo_caixa.lancamentos.store') }}"
                method="post"
                class="px-10 py-9 pb-14 sm:px-14 sm:py-12 sm:pb-16"
            >
                @csrf
                <input type="hidden" name="inicio" value="{{ $inicio }}">
                <input type="hidden" name="fim" value="{{ $fim }}">

                <div class="mx-auto max-w-md space-y-7">
                    <div class="space-y-1.5 pb-1">
                        <h3 class="text-lg font-bold tracking-tight text-gray-900">Lançamento manual</h3>
                        <p class="text-sm leading-relaxed text-gray-500">Registre uma entrada/saída que não veio do PDV automaticamente.</p>
                    </div>

                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <label for="man_tipo" class="block text-sm font-bold text-gray-900">Tipo</label>
                            <select
                                id="man_tipo"
                                name="tipo"
                                class="mt-1.5 w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm text-gray-900 shadow-sm focus:border-sky-500 focus:outline-none focus:ring-1 focus:ring-sky-500"
                                required
                            >
                                @php
                                    $tSel = old('tipo', 'saida');
                                @endphp
                                <option value="saida" @selected($tSel === 'saida')>Saída</option>
                                <option value="entrada" @selected($tSel === 'entrada')>Entrada</option>
                            </select>
                        </div>

                        <div>
                            <label for="man_data" class="block text-sm font-bold text-gray-900">Data</label>
                            <input
                                id="man_data"
                                name="data_movimento"
                                type="date"
                                value="{{ old('data_movimento', now()->format('Y-m-d')) }}"
                                class="mt-1.5 w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm text-gray-900 shadow-sm focus:border-sky-500 focus:outline-none focus:ring-1 focus:ring-sky-500"
                                required
                            />
                        </div>

                        <div>
                            <label for="man_valor" class="block text-sm font-bold text-gray-900">Valor</label>
                            <input
                                id="man_valor"
                                name="valor"
                                type="text"
                                value="{{ old('valor') }}"
                                placeholder="Ex: 80,00"
                                inputmode="decimal"
                                class="mt-1.5 w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm text-gray-900 shadow-sm focus:border-sky-500 focus:outline-none focus:ring-1 focus:ring-sky-500"
                                required
                            />
                        </div>

                        <div>
                            <label for="man_origem" class="block text-sm font-bold text-gray-900">Origem (opcional)</label>
                            <input
                                id="man_origem"
                                name="origem"
                                type="text"
                                value="{{ old('origem') }}"
                                placeholder="Ex: Caixa, Banco, Cartão"
                                class="mt-1.5 w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm text-gray-900 shadow-sm focus:border-sky-500 focus:outline-none focus:ring-1 focus:ring-sky-500"
                            />
                        </div>

                        <div class="sm:col-span-2">
                            <label for="man_desc" class="block text-sm font-bold text-gray-900">Descrição</label>
                            <input
                                id="man_desc"
                                name="descricao"
                                type="text"
                                value="{{ old('descricao') }}"
                                placeholder="Ex: Retirada, Aporte, Pagamento de taxa"
                                class="mt-1.5 w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm text-gray-900 shadow-sm focus:border-sky-500 focus:outline-none focus:ring-1 focus:ring-sky-500"
                                required
                            />
                        </div>

                        <div class="sm:col-span-2">
                            <div class="flex items-center justify-between gap-3">
                                <label for="man_cf_cat" class="block text-sm font-bold text-gray-900">Categoria (opcional)</label>
                                <button type="button" class="text-xs font-semibold text-blue-700 hover:text-blue-900" @click="showCatModal = true">
                                    + Nova categoria
                                </button>
                            </div>
                            <select
                                id="man_cf_cat"
                                name="cash_flow_category_id"
                                class="mt-1.5 w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm text-gray-900 shadow-sm focus:border-sky-500 focus:outline-none focus:ring-1 focus:ring-sky-500"
                            >
                                <option value="">— Selecione —</option>
                                @php
                                    $selCfCat = old('cash_flow_category_id', session('select_cash_flow_category_id'));
                                @endphp
                                @foreach ($categoriasFluxo as $c)
                                    <option value="{{ $c->id }}" @selected((string) $selCfCat === (string) $c->id)>{{ $c->nome }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="sm:col-span-2">
                            <label for="man_obs" class="block text-sm font-bold text-gray-900">Observações (opcional)</label>
                            <textarea
                                id="man_obs"
                                name="observacoes"
                                rows="3"
                                class="mt-1.5 w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm text-gray-900 shadow-sm focus:border-sky-500 focus:outline-none focus:ring-1 focus:ring-sky-500"
                            >{{ old('observacoes') }}</textarea>
                        </div>
                    </div>

                    <div class="flex flex-row flex-wrap items-center justify-end gap-3 border-t border-gray-100 pt-6">
                        <button type="button" class="btn-pdv-ghost btn-pdv-ghost-red px-6 py-2.5" @click="showManualModal = false">
                            Cancelar
                        </button>
                        <button type="submit" class="btn-pdv btn-pdv-primary px-7 py-2.5">
                            Salvar
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <div
            x-show="showCatModal"
            x-cloak
            class="absolute inset-0 z-[100] flex items-end justify-center bg-black/50 p-6 backdrop-blur-[2px] sm:items-center sm:p-12"
            @click="showCatModal = false"
        >
            <div
                @click.stop
                class="relative z-10 w-full max-w-xl overflow-hidden rounded-xl bg-white shadow-xl ring-1 ring-black/10"
            >
                <form
                    action="{{ route('financeiro.fluxo_caixa.categorias.store') }}"
                    method="post"
                    class="px-10 py-9 pb-14 sm:px-14 sm:py-12 sm:pb-16"
                >
                    @csrf
                    <input type="hidden" name="inicio" value="{{ $inicio }}">
                    <input type="hidden" name="fim" value="{{ $fim }}">

                    <div class="mx-auto max-w-md space-y-7">
                        <div class="space-y-1.5 pb-1">
                            <h3 class="text-lg font-bold tracking-tight text-gray-900">Nova categoria</h3>
                            <p class="text-sm leading-relaxed text-gray-500">Cria a categoria e já volta selecionada no lançamento.</p>
                        </div>
                        <div class="space-y-1.5">
                            <label for="cf_cat_nome" class="block text-sm font-bold text-gray-900">Nome</label>
                            <input
                                id="cf_cat_nome"
                                name="cf_cat_nome"
                                type="text"
                                value="{{ old('cf_cat_nome') }}"
                                placeholder="Ex: Retirada, Imposto, Ajuste"
                                class="w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm text-gray-900 shadow-sm focus:border-sky-500 focus:outline-none focus:ring-1 focus:ring-sky-500"
                                required
                            />
                        </div>
                        <div class="space-y-1.5">
                            <label for="cf_cat_cor" class="block text-sm font-bold text-gray-900">Cor (opcional)</label>
                            <input
                                id="cf_cat_cor"
                                name="cf_cat_cor"
                                type="text"
                                value="{{ old('cf_cat_cor') }}"
                                placeholder="Ex: #2563eb"
                                class="w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm text-gray-900 shadow-sm focus:border-sky-500 focus:outline-none focus:ring-1 focus:ring-sky-500"
                            />
                        </div>
                        <div class="flex flex-row flex-wrap items-center justify-end gap-3 border-t border-gray-100 pt-6">
                            <button type="button" class="btn-pdv-ghost btn-pdv-ghost-red px-6 py-2.5" @click="showCatModal = false">
                                Cancelar
                            </button>
                            <button type="submit" class="btn-pdv btn-pdv-primary px-7 py-2.5">
                                Salvar
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js" crossorigin="anonymous"></script>
    <script>
        (function () {
            const fluxo = @json($fluxo);
            const br = (n) =>
                new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(Number(n));

            const labels = fluxo.labels_short || [];
            const commonOpts = {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
            };

            const elEvol = document.getElementById('chartEvol');
            if (elEvol && typeof Chart !== 'undefined') {
                new Chart(elEvol, {
                    type: 'line',
                    data: {
                        labels,
                        datasets: [
                            {
                                label: 'Entradas',
                                data: fluxo.entradas,
                                borderColor: 'rgb(5, 150, 105)',
                                backgroundColor: 'rgba(5, 150, 105, 0.08)',
                                fill: true,
                                tension: 0.2,
                            },
                            {
                                label: 'Saídas',
                                data: fluxo.saidas_total,
                                borderColor: 'rgb(225, 29, 72)',
                                backgroundColor: 'rgba(225, 29, 72, 0.06)',
                                fill: true,
                                tension: 0.2,
                            },
                        ],
                    },
                    options: {
                        ...commonOpts,
                        plugins: {
                            legend: { position: 'bottom' },
                            tooltip: {
                                callbacks: {
                                    label: (ctx) => `${ctx.dataset.label}: ${br(ctx.parsed.y)}`,
                                },
                            },
                        },
                        scales: {
                            x: { ticks: { maxRotation: 45, minRotation: 0 } },
                            y: {
                                ticks: {
                                    callback: (v) =>
                                        new Intl.NumberFormat('pt-BR', {
                                            maximumFractionDigits: 0,
                                        }).format(v),
                                },
                            },
                        },
                    },
                });
            }

            const elAcum = document.getElementById('chartAcum');
            if (elAcum && typeof Chart !== 'undefined') {
                new Chart(elAcum, {
                    type: 'line',
                    data: {
                        labels,
                        datasets: [
                            {
                                label: 'Saldo acumulado',
                                data: fluxo.saldo_acumulado,
                                borderColor: 'rgb(37, 99, 235)',
                                backgroundColor: 'rgba(37, 99, 235, 0.08)',
                                fill: true,
                                tension: 0.2,
                            },
                        ],
                    },
                    options: {
                        ...commonOpts,
                        plugins: {
                            legend: { position: 'bottom' },
                            tooltip: {
                                callbacks: {
                                    label: (ctx) => `${ctx.dataset.label}: ${br(ctx.parsed.y)}`,
                                },
                            },
                        },
                        scales: {
                            x: { ticks: { maxRotation: 45, minRotation: 0 } },
                            y: {
                                ticks: {
                                    callback: (v) =>
                                        new Intl.NumberFormat('pt-BR', {
                                            maximumFractionDigits: 0,
                                        }).format(v),
                                },
                            },
                        },
                    },
                });
            }

            const palette = [
                '#2563eb',
                '#059669',
                '#d97706',
                '#dc2626',
                '#7c3aed',
                '#0d9488',
                '#db2777',
                '#4b5563',
            ];

            const elCat = document.getElementById('chartCat');
            if (elCat && fluxo.categorias_saidas && fluxo.categorias_saidas.length) {
                new Chart(elCat, {
                    type: 'doughnut',
                    data: {
                        labels: fluxo.categorias_saidas.map((c) => c.label),
                        datasets: [
                            {
                                data: fluxo.categorias_saidas.map((c) => c.valor),
                                backgroundColor: fluxo.categorias_saidas.map((_, i) => palette[i % palette.length]),
                            },
                        ],
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { position: 'right' },
                            tooltip: {
                                callbacks: {
                                    label: (ctx) => {
                                        const v = ctx.parsed;
                                        const total = ctx.dataset.data.reduce((a, b) => a + b, 0);
                                        const pct = total ? (v / total) * 100 : 0;
                                        return `${ctx.label}: ${br(v)} (${pct.toFixed(1)}%)`;
                                    },
                                },
                            },
                        },
                    },
                });
            }

            const elForma = document.getElementById('chartForma');
            if (elForma && fluxo.formas_entrada && fluxo.formas_entrada.length) {
                new Chart(elForma, {
                    type: 'doughnut',
                    data: {
                        labels: fluxo.formas_entrada.map((c) => c.label),
                        datasets: [
                            {
                                data: fluxo.formas_entrada.map((c) => c.valor),
                                backgroundColor: fluxo.formas_entrada.map((_, i) => palette[i % palette.length]),
                            },
                        ],
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { position: 'right' },
                            tooltip: {
                                callbacks: {
                                    label: (ctx) => {
                                        const v = ctx.parsed;
                                        const total = ctx.dataset.data.reduce((a, b) => a + b, 0);
                                        const pct = total ? (v / total) * 100 : 0;
                                        return `${ctx.label}: ${br(v)} (${pct.toFixed(1)}%)`;
                                    },
                                },
                            },
                        },
                    },
                });
            }
        })();
    </script>
</x-app-layout>
