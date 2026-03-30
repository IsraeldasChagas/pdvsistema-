<x-app-layout>
    <div class="min-h-[calc(100vh-3.5rem)] bg-[#f8f9fa] px-4 py-8 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-6xl">
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

                <form method="get" action="{{ route('financeiro.fluxo_caixa') }}" class="flex flex-wrap items-end gap-3 rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
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

            @php
                $t = $fluxo['totais'];
            @endphp

            <div class="mt-8 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                    <p class="text-xs font-bold uppercase tracking-wide text-gray-500">Entradas</p>
                    <p class="mt-2 text-2xl font-bold tabular-nums text-emerald-700">R$ {{ number_format($t['entradas'], 2, ',', '.') }}</p>
                    <p class="mt-1 text-xs text-gray-500">Vendas registradas no caixa</p>
                </div>
                <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                    <p class="text-xs font-bold uppercase tracking-wide text-gray-500">Saídas (variáveis)</p>
                    <p class="mt-2 text-2xl font-bold tabular-nums text-rose-700">R$ {{ number_format($t['saidas_variaveis'], 2, ',', '.') }}</p>
                    <p class="mt-1 text-xs text-gray-500">Por data da despesa</p>
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
