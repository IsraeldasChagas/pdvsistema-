@php
    $brl = fn ($v) => number_format((float) $v, 2, ',', '.');
@endphp

<x-app-layout>
    <div class="min-h-[calc(100vh-3.5rem)] bg-slate-50 px-4 py-8 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-7xl">
            @if (session('success'))
                <div class="mb-6 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm font-medium text-green-800">
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->has('lote'))
                <div class="mb-6 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                    {{ $errors->first('lote') }}
                </div>
            @endif

            @if ($errors->has('comissao'))
                <div class="mb-6 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                    {{ $errors->first('comissao') }}
                </div>
            @endif

            <form id="form-pagar-lote" action="{{ route('comissoes.pagar-lote') }}" method="post" class="hidden">
                @csrf
                <div id="lote-hidden-fields"></div>
            </form>

            <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                <div class="flex gap-3">
                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-lg bg-violet-100 text-violet-600 shadow-sm">
                        <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" />
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold tracking-tight text-gray-900">Comissões</h1>
                        <p class="mt-0.5 text-sm text-gray-500">Controle de comissões por vendedor</p>
                    </div>
                </div>
                <a
                    href="{{ route('comissoes.create') }}"
                    class="inline-flex shrink-0 items-center justify-center rounded-lg bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2"
                >
                    + Nova Comissão
                </a>
            </div>

            <div class="mt-8 grid grid-cols-1 gap-4 sm:grid-cols-3">
                <div class="rounded-xl bg-orange-500 p-6 text-white shadow-md">
                    <p class="text-sm font-medium text-white/90">A pagar</p>
                    <p class="mt-2 text-3xl font-bold tracking-tight">R$ {{ $brl($aPagar) }}</p>
                </div>
                <div class="rounded-xl bg-green-600 p-6 text-white shadow-md">
                    <p class="text-sm font-medium text-white/90">Já pagas</p>
                    <p class="mt-2 text-3xl font-bold tracking-tight">R$ {{ $brl($jaPagas) }}</p>
                </div>
                <div class="rounded-xl bg-purple-600 p-6 text-white shadow-md">
                    <p class="text-sm font-medium text-white/90">Total geral</p>
                    <p class="mt-2 text-3xl font-bold tracking-tight">R$ {{ $brl($totalGeral) }}</p>
                </div>
            </div>

            <form method="get" action="{{ route('modulos.comissoes') }}" class="mt-8 rounded-xl border border-gray-200 bg-white p-4 shadow-sm sm:p-5">
                <div class="flex flex-col gap-4 lg:flex-row lg:flex-wrap lg:items-end">
                    <div class="min-w-[160px] flex-1">
                        <label for="filtro_status" class="block text-sm font-medium text-gray-700">Status</label>
                        <select
                            id="filtro_status"
                            name="status"
                            class="mt-1.5 w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 shadow-sm focus:border-slate-500 focus:outline-none focus:ring-1 focus:ring-slate-500"
                        >
                            <option value="pendentes" @selected($statusFiltro === 'pendentes')>Pendentes</option>
                            <option value="pagas" @selected($statusFiltro === 'pagas')>Pagas</option>
                            <option value="todas" @selected($statusFiltro === 'todas')>Todas</option>
                        </select>
                    </div>
                    <div class="min-w-[160px] flex-1">
                        <label for="filtro_vendedor" class="block text-sm font-medium text-gray-700">Vendedor</label>
                        <select
                            id="filtro_vendedor"
                            name="vendedor"
                            class="mt-1.5 w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 shadow-sm focus:border-slate-500 focus:outline-none focus:ring-1 focus:ring-slate-500"
                        >
                            <option value="">Todos</option>
                            @foreach ($vendedores as $v)
                                <option value="{{ $v->id }}" @selected(request('vendedor') == $v->id)>{{ $v->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button
                        type="submit"
                        class="rounded-lg bg-slate-800 px-6 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-slate-900 focus:outline-none focus:ring-2 focus:ring-slate-600 focus:ring-offset-2"
                    >
                        Filtrar
                    </button>
                </div>
            </form>

            <div class="mt-4">
                <button
                    type="button"
                    class="inline-flex w-full items-center justify-center rounded-lg bg-orange-500 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-orange-600 focus:outline-none focus:ring-2 focus:ring-orange-400 focus:ring-offset-2 sm:w-auto"
                    onclick="window.submitComissoesLote && window.submitComissoesLote()"
                >
                    Marcar selecionadas como pagas
                </button>
            </div>

            <div class="mt-6 overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-left text-sm">
                        <thead class="bg-gray-100">
                            <tr>
                                <th scope="col" class="w-10 px-3 py-3 sm:px-4">
                                    <span class="sr-only">Selecionar</span>
                                </th>
                                <th scope="col" class="whitespace-nowrap px-3 py-3 font-semibold text-gray-700 sm:px-4">#</th>
                                <th scope="col" class="whitespace-nowrap px-3 py-3 font-semibold text-gray-700 sm:px-4">Vendedor</th>
                                <th scope="col" class="whitespace-nowrap px-3 py-3 font-semibold text-gray-700 sm:px-4">Venda</th>
                                <th scope="col" class="whitespace-nowrap px-3 py-3 font-semibold text-gray-700 sm:px-4">Valor</th>
                                <th scope="col" class="whitespace-nowrap px-3 py-3 font-semibold text-gray-700 sm:px-4">%</th>
                                <th scope="col" class="whitespace-nowrap px-3 py-3 font-semibold text-gray-700 sm:px-4">Data ref.</th>
                                <th scope="col" class="whitespace-nowrap px-3 py-3 font-semibold text-gray-700 sm:px-4">Status</th>
                                <th scope="col" class="whitespace-nowrap px-3 py-3 font-semibold text-gray-700 sm:px-4">Ação</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            @forelse ($comissoes as $c)
                                <tr class="hover:bg-gray-50/80">
                                    <td class="whitespace-nowrap px-3 py-3 sm:px-4">
                                        @if ($c->status === \App\Models\Commission::STATUS_PENDENTE)
                                            <input type="checkbox" class="comissao-check h-4 w-4 rounded border-gray-300 text-slate-800 focus:ring-slate-500" value="{{ $c->id }}" aria-label="Selecionar comissão {{ $c->id }}" />
                                        @endif
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-3 text-gray-700 sm:px-4">{{ $comissoes->count() - $loop->index }}</td>
                                    <td class="whitespace-nowrap px-3 py-3 text-gray-900 sm:px-4">{{ $c->user->name }}</td>
                                    <td class="whitespace-nowrap px-3 py-3 sm:px-4">
                                        @if ($c->cash_sale_id)
                                            <span class="font-medium text-green-600">#{{ $c->cash_sale_id }}</span>
                                        @else
                                            <span class="text-gray-500">Manual</span>
                                        @endif
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-3 font-bold text-gray-900 sm:px-4">R$ {{ $brl($c->valor) }}</td>
                                    <td class="whitespace-nowrap px-3 py-3 text-gray-700 sm:px-4">
                                        {{ $c->percentual !== null ? number_format((float) $c->percentual, 1, ',', '.').'%' : '—' }}
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-3 text-gray-700 sm:px-4">{{ $c->created_at->format('d/m/Y') }}</td>
                                    <td class="whitespace-nowrap px-3 py-3 sm:px-4">
                                        @if ($c->status === \App\Models\Commission::STATUS_PENDENTE)
                                            <span class="inline-flex rounded-full bg-orange-100 px-2.5 py-0.5 text-xs font-semibold text-orange-800">
                                                Pendente
                                            </span>
                                        @else
                                            <span class="inline-flex rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-semibold text-green-800">
                                                Pago
                                            </span>
                                        @endif
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-3 sm:px-4">
                                        @if ($c->status === \App\Models\Commission::STATUS_PENDENTE)
                                            <form method="post" action="{{ route('comissoes.pagar', $c) }}" class="inline">
                                                @csrf
                                                @if (request()->filled('status'))
                                                    <input type="hidden" name="redirect_status" value="{{ request('status') }}" />
                                                @endif
                                                @if (request()->filled('vendedor'))
                                                    <input type="hidden" name="redirect_vendedor" value="{{ request('vendedor') }}" />
                                                @endif
                                                <button type="submit" class="font-semibold text-green-600 hover:text-green-700 hover:underline">
                                                    Marcar pago
                                                </button>
                                            </form>
                                        @else
                                            <span class="text-gray-400">—</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="px-4 py-12 text-center text-sm text-gray-500">Nenhuma comissão encontrada.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <p class="mt-6 text-sm text-gray-500">
                Comissões são geradas automaticamente (5%) a cada venda no PDV. Você também pode adicionar comissões manuais.
            </p>
        </div>
    </div>

    <script>
        window.submitComissoesLote = function () {
            const form = document.getElementById('form-pagar-lote');
            const box = document.getElementById('lote-hidden-fields');
            if (!form || !box) return;
            box.replaceChildren();
            document.querySelectorAll('input.comissao-check:checked').forEach(function (cb) {
                const h = document.createElement('input');
                h.type = 'hidden';
                h.name = 'ids[]';
                h.value = cb.value;
                box.appendChild(h);
            });
            if (!box.children.length) {
                alert('Selecione ao menos uma comissão pendente.');
                return;
            }
            @if (request()->filled('status'))
                (function () {
                    const i = document.createElement('input');
                    i.type = 'hidden';
                    i.name = 'status';
                    i.value = @json(request('status'));
                    box.appendChild(i);
                })();
            @endif
            @if (request()->filled('vendedor'))
                (function () {
                    const i = document.createElement('input');
                    i.type = 'hidden';
                    i.name = 'vendedor';
                    i.value = @json(request('vendedor'));
                    box.appendChild(i);
                })();
            @endif
            form.submit();
        };
    </script>
</x-app-layout>
