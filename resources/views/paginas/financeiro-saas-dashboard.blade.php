<x-app-layout>
    @php
        $contextoNome = \App\Support\CurrentCompany::model()?->nome ?? 'Empresa Padrão';
        $brl = static fn ($v) => 'R$ ' . number_format((float) $v, 1, ',', '.');
        $f = static fn ($d) => $d ? $d->format('d/m/Y') : '—';
        $qEmpresa = request('empresa', '');
        $qSituacao = request('situacao', '');
        $qStatus = request('status', '');
        $qPlano = request('plano', '');
        $qDe = request('vencimento_de', '');
        $qAte = request('vencimento_ate', '');
    @endphp
    <div class="min-h-[calc(100vh-3.5rem)] bg-[#f8f9fa] px-4 py-6 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-7xl">
            @if (session('status'))
                <div class="mb-4 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('status') }}</div>
            @endif

            <p class="text-sm text-gray-500">{{ $contextoNome }}</p>

            <div class="mt-2 flex flex-wrap items-start gap-3">
                <svg class="mt-1 h-8 w-8 shrink-0 text-blue-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75A2.25 2.25 0 014.5 4.5h15a2.25 2.25 0 012.25 2.25v10.5A2.25 2.25 0 0119.5 19.5h-15a2.25 2.25 0 01-2.25-2.25V6.75zM2.25 10.5h19.5v3H2.25v-3z" />
                </svg>
                <div>
                    <h1 class="text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">Financeiro SaaS</h1>
                    <p class="mt-0.5 text-sm text-gray-500">Controle de cobranças e empresas clientes</p>
                </div>
            </div>

            {{-- KPIs --}}
            <div class="mt-8 grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-7">
                <div class="rounded-lg bg-emerald-500 p-4 text-white shadow-sm">
                    <p class="text-xs font-medium opacity-95">Recebido (mês)</p>
                    <p class="mt-1 text-lg font-bold tabular-nums sm:text-xl">{{ $brl($kpis['recebido_mes']) }}</p>
                </div>
                <div class="rounded-lg bg-orange-500 p-4 text-white shadow-sm">
                    <p class="text-xs font-medium opacity-95">Pendente</p>
                    <p class="mt-1 text-lg font-bold tabular-nums sm:text-xl">{{ $brl($kpis['pendente']) }}</p>
                </div>
                <div class="rounded-lg bg-red-500 p-4 text-white shadow-sm">
                    <p class="text-xs font-medium opacity-95">Vencido</p>
                    <p class="mt-1 text-lg font-bold tabular-nums sm:text-xl">{{ $brl($kpis['vencido']) }}</p>
                </div>
                <div class="rounded-lg bg-indigo-600 p-4 text-white shadow-sm">
                    <p class="text-xs font-medium opacity-95">Empresas ativas</p>
                    <p class="mt-1 text-lg font-bold tabular-nums sm:text-xl">{{ $kpis['empresas_ativas'] }}</p>
                </div>
                <div class="rounded-lg bg-slate-700 p-4 text-white shadow-sm">
                    <p class="text-xs font-medium opacity-95">Bloqueadas</p>
                    <p class="mt-1 text-lg font-bold tabular-nums sm:text-xl">{{ $kpis['bloqueadas'] }}</p>
                </div>
                <div class="rounded-lg bg-pink-500 p-4 text-white shadow-sm">
                    <p class="text-xs font-medium opacity-95">Vence hoje</p>
                    <p class="mt-1 text-lg font-bold tabular-nums sm:text-xl">{{ $kpis['vence_hoje'] }}</p>
                </div>
                <div class="rounded-lg bg-rose-900 p-4 text-white shadow-sm">
                    <p class="text-xs font-medium opacity-95">Em atraso</p>
                    <p class="mt-1 text-lg font-bold tabular-nums sm:text-xl">{{ $kpis['em_atraso'] }}</p>
                </div>
            </div>

            {{-- Filtros --}}
            <form method="get" action="{{ url()->current() }}" class="mt-8 rounded-xl border border-gray-200 bg-white p-4 shadow-sm sm:p-5">
                <div class="flex flex-wrap items-end gap-4">
                    <div class="min-w-[140px] flex-1">
                        <label for="filtro_empresa" class="block text-xs font-semibold uppercase tracking-wide text-gray-500">Empresa</label>
                        <select name="empresa" id="filtro_empresa" class="mt-1.5 w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                            <option value="">Todas</option>
                            @foreach ($companies as $c)
                                <option value="{{ $c->id }}" @selected((string) $qEmpresa === (string) $c->id)>{{ $c->nome }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="min-w-[130px] flex-1">
                        <label for="filtro_situacao" class="block text-xs font-semibold uppercase tracking-wide text-gray-500">Situação</label>
                        <select name="situacao" id="filtro_situacao" class="mt-1.5 w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                            <option value="" @selected($qSituacao === '')>Todas</option>
                            <option value="regular" @selected($qSituacao === 'regular')>Regular</option>
                            <option value="em_atraso" @selected($qSituacao === 'em_atraso')>Em atraso</option>
                        </select>
                    </div>
                    <div class="min-w-[120px] flex-1">
                        <label for="filtro_status" class="block text-xs font-semibold uppercase tracking-wide text-gray-500">Status</label>
                        <select name="status" id="filtro_status" class="mt-1.5 w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                            <option value="" @selected($qStatus === '')>Todos</option>
                            <option value="pendente" @selected($qStatus === 'pendente')>Pendente</option>
                            <option value="pago" @selected($qStatus === 'pago')>Pago</option>
                            <option value="vencido" @selected($qStatus === 'vencido')>Vencido</option>
                        </select>
                    </div>
                    <div class="min-w-[120px] flex-1">
                        <label for="filtro_plano" class="block text-xs font-semibold uppercase tracking-wide text-gray-500">Plano</label>
                        <select name="plano" id="filtro_plano" class="mt-1.5 w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                            <option value="">Todos</option>
                            @foreach ($planos as $p)
                                <option value="{{ $p->id }}" @selected((string) $qPlano === (string) $p->id)>{{ $p->nome }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="min-w-[150px]">
                        <label for="filtro_de" class="block text-xs font-semibold uppercase tracking-wide text-gray-500">Vencimento de</label>
                        <input type="date" name="vencimento_de" id="filtro_de" value="{{ $qDe }}" class="mt-1.5 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500" />
                    </div>
                    <div class="min-w-[150px]">
                        <label for="filtro_ate" class="block text-xs font-semibold uppercase tracking-wide text-gray-500">até</label>
                        <input type="date" name="vencimento_ate" id="filtro_ate" value="{{ $qAte }}" class="mt-1.5 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500" />
                    </div>
                    <div class="shrink-0">
                        <button type="submit" class="rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-blue-700">Filtrar</button>
                    </div>
                </div>
            </form>

            {{-- Tabela cobranças --}}
            <div id="secao-cobrancas" class="mt-8 scroll-mt-24 rounded-xl border border-gray-200 bg-white shadow-sm">
                <div class="flex flex-col gap-3 border-b border-gray-100 px-4 py-4 sm:flex-row sm:items-center sm:justify-between sm:px-6">
                    <div>
                        <h2 class="text-lg font-bold text-gray-900">Cobranças</h2>
                        <p class="text-sm text-gray-500">Últimas 100 registradas</p>
                    </div>
                    <a
                        href="{{ route('financeiro.saas.charges.create', array_filter(['empresa' => $qEmpresa, 'plano' => $qPlano, 'status' => $qStatus, 'situacao' => $qSituacao, 'vencimento_de' => $qDe, 'vencimento_ate' => $qAte], fn ($v) => $v !== null && $v !== '')) }}"
                        class="inline-flex items-center justify-center gap-2 rounded-lg bg-green-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-green-700"
                    >
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                        + Nova Cobrança
                    </a>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-left text-sm">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="whitespace-nowrap px-4 py-3 font-semibold text-gray-700 sm:px-6">#</th>
                                <th class="whitespace-nowrap px-4 py-3 font-semibold text-gray-700 sm:px-6">Empresa</th>
                                <th class="whitespace-nowrap px-4 py-3 font-semibold text-gray-700 sm:px-6">Competência</th>
                                <th class="whitespace-nowrap px-4 py-3 font-semibold text-gray-700 sm:px-6">Plano</th>
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
                                    <td class="whitespace-nowrap px-4 py-3 text-gray-600 sm:px-6">{{ $charge->saasPlan?->nome ?? '—' }}</td>
                                    <td class="whitespace-nowrap px-4 py-3 tabular-nums text-gray-900 sm:px-6">{{ $brl($charge->valor) }}</td>
                                    <td class="whitespace-nowrap px-4 py-3 text-gray-600 sm:px-6">{{ $f($charge->vencimento) }}</td>
                                    <td class="whitespace-nowrap px-4 py-3 text-gray-600 sm:px-6">{{ $f($charge->pagamento) }}</td>
                                    <td class="whitespace-nowrap px-4 py-3 sm:px-6">
                                        <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $badge }}">{{ $label }}</span>
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-3 text-gray-600 sm:px-6">{{ $atraso }}</td>
                                    <td class="whitespace-nowrap px-4 py-3 sm:px-6">
                                        <a href="{{ route('financeiro.saas.charges.edit', ['charge' => $charge] + array_filter(['empresa' => $qEmpresa, 'plano' => $qPlano, 'status' => $qStatus, 'situacao' => $qSituacao, 'vencimento_de' => $qDe, 'vencimento_ate' => $qAte], fn ($v) => $v !== null && $v !== '')) }}" class="font-semibold text-blue-600 hover:underline">Editar</a>
                                        <span class="mx-2 text-gray-300">|</span>
                                        <form action="{{ route('financeiro.saas.charges.destroy', $charge) }}" method="post" class="inline" onsubmit="return confirm('Excluir esta cobrança?');">
                                            @csrf
                                            @method('DELETE')
                                            <input type="hidden" name="empresa" value="{{ $qEmpresa }}" />
                                            <input type="hidden" name="situacao" value="{{ $qSituacao }}" />
                                            <input type="hidden" name="status" value="{{ $qStatus }}" />
                                            <input type="hidden" name="plano" value="{{ $qPlano }}" />
                                            <input type="hidden" name="vencimento_de" value="{{ $qDe }}" />
                                            <input type="hidden" name="vencimento_ate" value="{{ $qAte }}" />
                                            <button type="submit" class="font-semibold text-red-600 hover:underline">Excluir</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="px-4 py-12 text-center text-gray-500 sm:px-6">Nenhuma cobrança encontrada.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
