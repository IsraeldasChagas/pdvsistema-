<x-app-layout>
    @php
        $contextoNome = \App\Support\CurrentCompany::model()?->nome ?? 'Empresa Padrão';
        $brl = static fn ($v) => 'R$ ' . number_format((float) $v, 2, ',', '.');
        $f = static fn ($d) => $d ? $d->format('d/m/Y') : '—';
        $qEmpresa = request('empresa', '');
        $qSituacao = request('situacao', '');
        $qStatus = request('status', '');
        $qPlano = request('plano', '');
        $filterQuery = array_filter(
            ['empresa' => $qEmpresa, 'plano' => $qPlano, 'status' => $qStatus, 'situacao' => $qSituacao],
            static fn ($v) => $v !== null && $v !== ''
        );
    @endphp
    <div class="min-h-[calc(100vh-3.5rem)] bg-[#f3f4f6] px-4 py-8 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-7xl">
            @if (session('status'))
                <div class="mb-4 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('status') }}</div>
            @endif

            <p class="text-sm font-medium text-gray-900">{{ $contextoNome }}</p>

            <div class="mt-3 flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                <div class="flex items-start gap-3">
                    <svg class="mt-0.5 h-9 w-9 shrink-0 text-slate-700" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                    </svg>
                    <div>
                        <h1 class="text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">Cobranças</h1>
                        <p class="mt-0.5 text-sm text-gray-500">Gestão de mensalidades e cobranças</p>
                    </div>
                </div>
                <a
                    href="{{ route('financeiro.saas.charges.create', $filterQuery) }}"
                    class="inline-flex shrink-0 items-center justify-center gap-2 rounded-lg bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-emerald-700"
                >
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                    + Nova Cobrança
                </a>
            </div>

            <form method="get" action="{{ url()->current() }}" class="mt-8 rounded-xl border border-gray-200 bg-white p-4 shadow-sm sm:p-5">
                <div class="flex flex-wrap items-end gap-4">
                    <div class="min-w-[140px] flex-1">
                        <label for="cb_empresa" class="block text-xs font-semibold uppercase tracking-wide text-gray-500">Empresa</label>
                        <select name="empresa" id="cb_empresa" class="mt-1.5 w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                            <option value="">Todas</option>
                            @foreach ($companies as $c)
                                <option value="{{ $c->id }}" @selected((string) $qEmpresa === (string) $c->id)>{{ $c->nome }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="min-w-[130px] flex-1">
                        <label for="cb_situacao" class="block text-xs font-semibold uppercase tracking-wide text-gray-500">Situação</label>
                        <select name="situacao" id="cb_situacao" class="mt-1.5 w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                            <option value="" @selected($qSituacao === '')>Todas</option>
                            <option value="regular" @selected($qSituacao === 'regular')>Regular</option>
                            <option value="em_atraso" @selected($qSituacao === 'em_atraso')>Em atraso</option>
                        </select>
                    </div>
                    <div class="min-w-[120px] flex-1">
                        <label for="cb_status" class="block text-xs font-semibold uppercase tracking-wide text-gray-500">Status</label>
                        <select name="status" id="cb_status" class="mt-1.5 w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                            <option value="" @selected($qStatus === '')>Todos</option>
                            <option value="pendente" @selected($qStatus === 'pendente')>Pendente</option>
                            <option value="pago" @selected($qStatus === 'pago')>Pago</option>
                            <option value="vencido" @selected($qStatus === 'vencido')>Vencido</option>
                        </select>
                    </div>
                    <div class="min-w-[120px] flex-1">
                        <label for="cb_plano" class="block text-xs font-semibold uppercase tracking-wide text-gray-500">Plano</label>
                        <select name="plano" id="cb_plano" class="mt-1.5 w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                            <option value="">Todos</option>
                            @foreach ($planos as $p)
                                <option value="{{ $p->id }}" @selected((string) $qPlano === (string) $p->id)>{{ $p->nome }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="shrink-0">
                        <button type="submit" class="rounded-lg bg-slate-800 px-6 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-slate-900">Filtrar</button>
                    </div>
                </div>
            </form>

            <div class="mt-8 overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-left text-sm">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="whitespace-nowrap px-4 py-3 font-semibold text-gray-700 sm:px-6">#</th>
                                <th class="whitespace-nowrap px-4 py-3 font-semibold text-gray-700 sm:px-6">Empresa</th>
                                <th class="whitespace-nowrap px-4 py-3 font-semibold text-gray-700 sm:px-6">Competência</th>
                                <th class="whitespace-nowrap px-4 py-3 font-semibold text-gray-700 sm:px-6">Valor</th>
                                <th class="whitespace-nowrap px-4 py-3 font-semibold text-gray-700 sm:px-6">Vencimento</th>
                                <th class="whitespace-nowrap px-4 py-3 font-semibold text-gray-700 sm:px-6">Pagamento</th>
                                <th class="whitespace-nowrap px-4 py-3 font-semibold text-gray-700 sm:px-6">Status</th>
                                <th class="whitespace-nowrap px-4 py-3 font-semibold text-gray-700 sm:px-6">Atraso</th>
                                <th class="whitespace-nowrap px-4 py-3 font-semibold text-gray-700 sm:px-6">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            @forelse ($charges as $charge)
                                @php
                                    $label = $charge->displayStatusLabel();
                                    $badge =
                                        $label === 'Pago'
                                            ? 'bg-green-100 text-green-800'
                                            : ($label === 'Vencido'
                                                ? 'bg-red-100 text-red-800'
                                                : 'bg-amber-100 text-amber-900');
                                    $atraso =
                                        $charge->status === \App\Models\SaasCharge::STATUS_PAGO
                                            ? '—'
                                            : ($charge->isOverdue()
                                                ? 'Sim'
                                                : 'Não');
                                @endphp
                                <tr class="hover:bg-gray-50/80">
                                    <td class="whitespace-nowrap px-4 py-3 text-gray-600 sm:px-6">{{ $loop->iteration }}</td>
                                    <td class="px-4 py-3 font-medium text-gray-900 sm:px-6">{{ $charge->company->nome }}</td>
                                    <td class="whitespace-nowrap px-4 py-3 text-gray-600 sm:px-6">{{ $charge->competenciaLabel() }}</td>
                                    <td class="whitespace-nowrap px-4 py-3 tabular-nums text-gray-900 sm:px-6">{{ $brl($charge->valor) }}</td>
                                    <td class="whitespace-nowrap px-4 py-3 text-gray-600 sm:px-6">{{ $f($charge->vencimento) }}</td>
                                    <td class="whitespace-nowrap px-4 py-3 text-gray-600 sm:px-6">{{ $f($charge->pagamento) }}</td>
                                    <td class="whitespace-nowrap px-4 py-3 sm:px-6">
                                        <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $badge }}">{{ $label }}</span>
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-3 text-gray-600 sm:px-6">{{ $atraso }}</td>
                                    <td class="whitespace-nowrap px-4 py-3 sm:px-6">
                                        <a href="{{ route('financeiro.saas.charges.edit', ['charge' => $charge] + $filterQuery) }}" class="font-semibold text-indigo-600 hover:text-indigo-500 hover:underline">Editar</a>
                                        <span class="mx-2 text-gray-300">|</span>
                                        <form action="{{ route('financeiro.saas.charges.destroy', $charge) }}" method="post" class="inline" onsubmit="return confirm('Excluir esta cobrança?');">
                                            @csrf
                                            @method('DELETE')
                                            @foreach ($filterQuery as $k => $v)
                                                <input type="hidden" name="{{ $k }}" value="{{ $v }}" />
                                            @endforeach
                                            <button type="submit" class="font-semibold text-red-600 hover:underline">Excluir</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="px-4 py-12 text-center text-gray-500 sm:px-6">Nenhuma cobrança encontrada.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
