<x-app-layout>
    <div class="px-4 py-8 sm:px-6 lg:px-8">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h1 class="text-3xl font-bold tracking-tight text-gray-900">Histórico de movimentações</h1>
                <p class="mt-1 text-sm text-gray-500">Últimas 200 movimentações de estoque.</p>
            </div>
            <a
                href="{{ route('modulos.estoque') }}"
                class="inline-flex rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-50"
            >
                Voltar ao estoque
            </a>
        </div>

        <form method="get" action="{{ route('estoque.historico') }}" class="mt-6 flex flex-wrap items-end gap-3 rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
            <div>
                <label for="produto" class="block text-sm font-medium text-gray-700">Produto</label>
                <select name="produto" id="produto" class="mt-1 block rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Todos</option>
                    @foreach ($produtos as $p)
                        <option value="{{ $p->id }}" @selected(request('produto') == $p->id)>{{ $p->codigo }} — {{ $p->nome }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="tipo" class="block text-sm font-medium text-gray-700">Tipo</label>
                <select name="tipo" id="tipo" class="mt-1 block rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Todos</option>
                    <option value="entrada" @selected(request('tipo') === 'entrada')>Entrada</option>
                    <option value="saida" @selected(request('tipo') === 'saida')>Saída</option>
                    <option value="entrega" @selected(request('tipo') === 'entrega')>Entrega</option>
                </select>
            </div>
            <button type="submit" class="rounded-lg bg-slate-800 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-900">
                Filtrar
            </button>
        </form>

        <div class="mt-6 overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-left text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 font-semibold text-gray-700">Data</th>
                            <th class="px-4 py-3 font-semibold text-gray-700">Produto</th>
                            <th class="px-4 py-3 font-semibold text-gray-700">Tipo</th>
                            <th class="px-4 py-3 font-semibold text-gray-700">Qtd</th>
                            <th class="px-4 py-3 font-semibold text-gray-700">Saldo após</th>
                            <th class="px-4 py-3 font-semibold text-gray-700">Vendedor (destino)</th>
                            <th class="px-4 py-3 font-semibold text-gray-700">Usuário</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($movimentos as $m)
                            <tr class="hover:bg-gray-50/80">
                                <td class="whitespace-nowrap px-4 py-3 text-gray-700">{{ $m->created_at->format('d/m/Y H:i') }}</td>
                                <td class="px-4 py-3">
                                    <span class="font-medium text-gray-900">{{ $m->product?->nome ?? '—' }}</span>
                                    <span class="text-gray-500">{{ $m->product?->codigo }}</span>
                                </td>
                                <td class="whitespace-nowrap px-4 py-3">
                                    @php
                                        $labels = ['entrada' => 'Entrada', 'saida' => 'Saída', 'entrega' => 'Entrega'];
                                        $colors = ['entrada' => 'bg-green-100 text-green-800', 'saida' => 'bg-red-100 text-red-800', 'entrega' => 'bg-purple-100 text-purple-800'];
                                    @endphp
                                    <span class="inline-flex rounded-full px-2 py-0.5 text-xs font-medium {{ $colors[$m->tipo] ?? 'bg-gray-100 text-gray-800' }}">
                                        {{ $labels[$m->tipo] ?? $m->tipo }}
                                    </span>
                                </td>
                                <td class="whitespace-nowrap px-4 py-3 font-medium text-gray-900">{{ $m->quantidade }} UN</td>
                                <td class="whitespace-nowrap px-4 py-3 text-gray-700">{{ $m->saldo_apos }} UN</td>
                                <td class="whitespace-nowrap px-4 py-3 text-gray-600">{{ $m->destinatario?->name ?? '—' }}</td>
                                <td class="whitespace-nowrap px-4 py-3 text-gray-600">{{ $m->user?->name ?? '—' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-12 text-center text-sm text-gray-500">Nenhuma movimentação registrada.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
