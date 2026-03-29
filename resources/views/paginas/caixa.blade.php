@php
    $brl = fn ($v) => number_format((float) $v, 2, ',', '.');
@endphp

<x-app-layout>
    <div class="min-h-[calc(100vh-3.5rem)] bg-slate-50 px-4 py-8 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-4xl">
            <h1 class="text-3xl font-bold tracking-tight text-gray-900">💰 Meu Caixa</h1>
            <p class="mt-1 text-sm text-gray-500 sm:text-base">Cada usuário tem seu próprio caixa. Abra o seu para usar o PDV.</p>

            @if (session('success'))
                <div class="mt-6 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm font-medium text-green-800">
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->has('fechar'))
                <div class="mt-6 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                    {{ $errors->first('fechar') }}
                </div>
            @endif

            @if ($errors->has('caixa'))
                <div class="mt-6 rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900">
                    {{ $errors->first('caixa') }}
                </div>
            @endif

            @if ($aberta)
                {{-- Faixa superior (caixa aberto) --}}
                <div class="mt-6 rounded-lg border border-green-200 bg-green-50 px-4 py-2.5 text-sm font-medium text-green-800">
                    Caixa aberto com R$ {{ $brl($aberta->valor_abertura) }}
                </div>

                {{-- Card principal: fechamento --}}
                <div class="mt-4 overflow-hidden rounded-xl border border-gray-200 bg-white shadow-md">
                    <div class="flex flex-wrap items-center justify-between gap-4 bg-emerald-600 px-4 py-5 sm:px-6">
                        <div>
                            <p class="text-xs font-medium uppercase tracking-wide text-white/90">Status</p>
                            <p class="mt-0.5 text-2xl font-bold text-white">Caixa Aberto</p>
                            <p class="mt-1 text-sm text-white/95">
                                Aberto em {{ $aberta->opened_at->format('d/m/Y') }} às {{ $aberta->opened_at->format('H:i') }}
                            </p>
                        </div>
                        <div
                            class="flex h-14 w-14 shrink-0 items-center justify-center rounded-full bg-amber-400 text-2xl font-bold text-amber-950 shadow-md ring-2 ring-amber-300/80"
                            aria-hidden="true"
                        >
                            $
                        </div>
                    </div>

                    <div class="border-t border-gray-100 p-4 sm:p-6">
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                            <div class="rounded-xl border border-slate-200/80 bg-slate-100 px-4 py-4">
                                <p class="text-xs font-medium text-gray-500">Valor Abertura</p>
                                <p class="mt-1 text-xl font-bold text-gray-900">R$ {{ $brl($aberta->valor_abertura) }}</p>
                            </div>
                            <div class="rounded-xl border border-amber-100 bg-amber-50 px-4 py-4">
                                <p class="text-xs font-medium text-amber-700">Total em Vendas</p>
                                <p class="mt-1 text-xl font-bold text-amber-600">R$ {{ $brl($totalVendas) }}</p>
                            </div>
                            <div class="rounded-xl border border-emerald-100 bg-emerald-50 px-4 py-4">
                                <p class="text-xs font-medium text-emerald-700">Saldo Esperado</p>
                                <p class="mt-1 text-xl font-bold text-emerald-600">R$ {{ $brl($saldoEsperado) }}</p>
                            </div>
                        </div>

                        <form method="post" action="{{ route('caixa.fechar') }}" class="mt-8">
                            @csrf
                            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                                <div>
                                    <label for="valor_fechamento" class="block text-sm font-medium text-gray-700">Valor no caixa (fechamento)</label>
                                    <input
                                        type="text"
                                        name="valor_fechamento"
                                        id="valor_fechamento"
                                        value="{{ old('valor_fechamento', '0,00') }}"
                                        inputmode="decimal"
                                        autocomplete="off"
                                        required
                                        class="mt-2 block w-full rounded-lg border border-gray-300 bg-white px-4 py-3 text-lg font-semibold tracking-wide text-gray-900 shadow-sm focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/30 @error('valor_fechamento') border-red-500 @enderror"
                                    />
                                    @error('valor_fechamento')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label for="observacao" class="block text-sm font-medium text-gray-700">Observação (opcional)</label>
                                    <input
                                        type="text"
                                        name="observacao"
                                        id="observacao"
                                        value="{{ old('observacao') }}"
                                        placeholder="Ex: Fechamento normal"
                                        class="mt-2 block w-full rounded-lg border border-gray-300 bg-white px-4 py-3 text-base text-gray-900 shadow-sm placeholder:text-gray-400 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/30"
                                    />
                                    @error('observacao')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                            <button
                                type="submit"
                                class="mt-8 rounded-lg bg-red-600 px-8 py-3 text-base font-bold text-white shadow-sm transition hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2"
                            >
                                Fechar Caixa
                            </button>
                        </form>
                    </div>
                </div>
            @else
                {{-- Caixa fechado: abertura --}}
                <div class="mt-8 overflow-hidden rounded-xl border border-gray-200 bg-white shadow-md">
                    <div class="flex flex-wrap items-center justify-between gap-4 bg-slate-700 px-4 py-4 sm:px-6">
                        <div>
                            <p class="text-xs font-medium uppercase tracking-wide text-slate-300">Status</p>
                            <p class="mt-0.5 text-lg font-bold text-white">Caixa Fechado</p>
                            <p class="mt-1 text-sm text-slate-300">Abra o caixa para iniciar as vendas</p>
                        </div>
                        <div class="shrink-0 text-amber-400" aria-hidden="true">
                            <svg class="h-14 w-14 sm:h-16 sm:w-16" fill="none" viewBox="0 0 24 24" stroke-width="1.25" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                            </svg>
                        </div>
                    </div>

                    <form method="post" action="{{ route('caixa.abrir') }}" class="border-t border-gray-100 p-4 sm:p-6">
                        @csrf
                        <label for="valor_abertura" class="block text-sm font-medium text-gray-700">
                            Valor de abertura (dinheiro em caixa)
                        </label>
                        <input
                            id="valor_abertura"
                            name="valor_abertura"
                            type="text"
                            value="{{ old('valor_abertura', '0,00') }}"
                            inputmode="decimal"
                            autocomplete="off"
                            required
                            class="mt-2 block w-full rounded-lg border border-gray-300 bg-white px-4 py-4 text-2xl font-semibold tracking-wide text-gray-900 shadow-sm focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/30 @error('valor_abertura') border-red-500 @enderror"
                        />
                        @error('valor_abertura')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-2 text-sm text-gray-500">
                            Informe o valor em dinheiro que está no caixa no momento da abertura.
                        </p>
                        <button
                            type="submit"
                            class="mt-6 w-full rounded-lg bg-emerald-600 px-4 py-3.5 text-base font-semibold text-white shadow-sm transition hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 sm:w-auto sm:min-w-[200px]"
                        >
                            Abrir Caixa
                        </button>
                    </form>
                </div>
            @endif

            <div class="mt-10 overflow-hidden rounded-xl border border-gray-200 bg-white shadow-md">
                <div class="border-b border-gray-100 px-4 py-4 sm:px-6">
                    <h2 class="text-lg font-bold text-gray-900">Histórico do seu caixa</h2>
                    <p class="mt-0.5 text-sm text-gray-500">Suas últimas aberturas e fechamentos</p>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-left text-sm">
                        <thead>
                            <tr class="border-b border-slate-200 bg-slate-100">
                                <th class="px-4 py-3 font-semibold text-slate-700 sm:px-6">#</th>
                                <th class="px-4 py-3 font-semibold text-slate-700 sm:px-6">Abertura</th>
                                <th class="px-4 py-3 font-semibold text-slate-700 sm:px-6">Fechamento</th>
                                <th class="px-4 py-3 font-semibold text-slate-700 sm:px-6">Abertura (R$)</th>
                                <th class="px-4 py-3 font-semibold text-slate-700 sm:px-6">Vendas (R$)</th>
                                <th class="px-4 py-3 font-semibold text-slate-700 sm:px-6">Fechamento (R$)</th>
                                <th class="px-4 py-3 font-semibold text-slate-700 sm:px-6">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($historico as $i => $h)
                                @php
                                    $vendasHist = $h->total_vendas_no_fechamento !== null
                                        ? (float) $h->total_vendas_no_fechamento
                                        : (float) $h->cashSales->sum('total');
                                @endphp
                                <tr class="{{ $i % 2 === 0 ? 'bg-white' : 'bg-slate-50/80' }}">
                                    <td class="whitespace-nowrap px-4 py-3 text-gray-700 sm:px-6">{{ $historico->count() - $i }}</td>
                                    <td class="whitespace-nowrap px-4 py-3 text-gray-700 sm:px-6">{{ $h->opened_at->format('d/m/Y H:i') }}</td>
                                    <td class="whitespace-nowrap px-4 py-3 text-gray-700 sm:px-6">{{ $h->closed_at?->format('d/m/Y H:i') ?? '—' }}</td>
                                    <td class="whitespace-nowrap px-4 py-3 text-gray-900 sm:px-6">{{ $brl($h->valor_abertura) }}</td>
                                    <td class="whitespace-nowrap px-4 py-3 text-amber-700 sm:px-6">{{ $brl($vendasHist) }}</td>
                                    <td class="whitespace-nowrap px-4 py-3 font-medium text-gray-900 sm:px-6">{{ $brl($h->valor_fechamento ?? 0) }}</td>
                                    <td class="whitespace-nowrap px-4 py-3 sm:px-6">
                                        <span class="inline-flex rounded-full bg-gray-200 px-2.5 py-0.5 text-xs font-medium text-gray-700">
                                            Fechado
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-4 py-12 text-center text-sm text-gray-500 sm:px-6">
                                        Nenhum histórico ainda. Abra o caixa para começar.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
