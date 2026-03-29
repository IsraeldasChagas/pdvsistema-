<x-app-layout>
    @php
        $contextoNome = \App\Support\CurrentCompany::model()?->nome ?? 'Empresa Padrão';
        $brl = static fn ($v) => 'R$ ' . number_format((float) $v, 2, ',', '.');
        $f = static fn ($d) => $d ? $d->format('d/m/Y') : '—';
        if (! $empresa->ativo) {
            $statusClass = 'bg-gray-100 text-gray-800';
            $statusLabel = 'Inativa';
        } elseif ($empresa->billing_blocked) {
            $statusClass = 'bg-orange-100 text-orange-900';
            $statusLabel = 'Bloqueada';
        } else {
            $statusClass = 'bg-green-100 text-green-800';
            $statusLabel = 'Ativa';
        }
    @endphp
    <div class="min-h-[calc(100vh-3.5rem)] bg-[#f3f4f6] px-4 py-8 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-4xl">
            <p class="text-sm font-medium text-gray-900">{{ $contextoNome }}</p>

            <div class="mt-3 flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                <div class="flex items-start gap-3">
                    <svg class="mt-0.5 h-9 w-9 shrink-0 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21" />
                    </svg>
                    <div>
                        <h1 class="text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">{{ $empresa->nome }}</h1>
                        <p class="mt-0.5 text-sm text-gray-500">Detalhes da empresa cliente</p>
                    </div>
                </div>
                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('financeiro.saas.empresas') }}" class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-50">Voltar à lista</a>
                    <a href="{{ route('empresas.edit', $empresa) }}" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">Editar cadastro</a>
                </div>
            </div>

            <div class="mt-8 grid gap-6 sm:grid-cols-2">
                <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                    <h2 class="text-sm font-semibold uppercase tracking-wide text-gray-500">Resumo</h2>
                    <dl class="mt-4 space-y-3 text-sm">
                        <div class="flex justify-between gap-4">
                            <dt class="text-gray-500">Status</dt>
                            <dd><span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $statusClass }}">{{ $statusLabel }}</span></dd>
                        </div>
                        <div class="flex justify-between gap-4">
                            <dt class="text-gray-500">CNPJ</dt>
                            <dd class="text-right font-medium text-gray-900">{{ $empresa->cnpj ?: '—' }}</dd>
                        </div>
                        <div class="flex justify-between gap-4">
                            <dt class="text-gray-500">E-mail</dt>
                            <dd class="text-right font-medium text-gray-900">{{ $empresa->email ?: '—' }}</dd>
                        </div>
                        @if ($nextPending)
                            <div class="flex justify-between gap-4 border-t border-gray-100 pt-3">
                                <dt class="text-gray-500">Próx. vencimento</dt>
                                <dd class="text-right font-medium text-gray-900">{{ $f($nextPending->vencimento) }} · {{ $brl($nextPending->valor) }}</dd>
                            </div>
                            <div class="flex justify-between gap-4">
                                <dt class="text-gray-500">Plano (cobrança)</dt>
                                <dd class="text-right font-medium text-gray-900">{{ $nextPending->saasPlan?->nome ?? '—' }}</dd>
                            </div>
                        @endif
                    </dl>
                </div>
                <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                    <h2 class="text-sm font-semibold uppercase tracking-wide text-gray-500">Contato</h2>
                    <dl class="mt-4 space-y-3 text-sm">
                        <div>
                            <dt class="text-gray-500">Telefone</dt>
                            <dd class="mt-0.5 font-medium text-gray-900">{{ $empresa->telefone ?: '—' }}</dd>
                        </div>
                        <div>
                            <dt class="text-gray-500">Endereço</dt>
                            <dd class="mt-0.5 font-medium text-gray-900">{{ $empresa->endereco ?: '—' }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

            <div class="mt-8 overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
                <div class="border-b border-gray-100 px-6 py-4">
                    <h2 class="text-lg font-bold text-gray-900">Cobranças recentes</h2>
                    <p class="text-sm text-gray-500">Até 50 registros, por vencimento</p>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-left text-sm">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-4 py-3 font-semibold text-gray-700 sm:px-6">Plano</th>
                                <th class="px-4 py-3 font-semibold text-gray-700 sm:px-6">Valor</th>
                                <th class="px-4 py-3 font-semibold text-gray-700 sm:px-6">Vencimento</th>
                                <th class="px-4 py-3 font-semibold text-gray-700 sm:px-6">Pagamento</th>
                                <th class="px-4 py-3 font-semibold text-gray-700 sm:px-6">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse ($charges as $charge)
                                @php
                                    $label = $charge->displayStatusLabel();
                                    $badge =
                                        $label === 'Pago'
                                            ? 'bg-green-100 text-green-800'
                                            : ($label === 'Vencido'
                                                ? 'bg-red-100 text-red-800'
                                                : 'bg-amber-100 text-amber-900');
                                @endphp
                                <tr class="hover:bg-gray-50/80">
                                    <td class="px-4 py-3 sm:px-6">{{ $charge->saasPlan?->nome ?? '—' }}</td>
                                    <td class="whitespace-nowrap px-4 py-3 tabular-nums sm:px-6">{{ $brl($charge->valor) }}</td>
                                    <td class="whitespace-nowrap px-4 py-3 sm:px-6">{{ $f($charge->vencimento) }}</td>
                                    <td class="whitespace-nowrap px-4 py-3 sm:px-6">{{ $f($charge->pagamento) }}</td>
                                    <td class="px-4 py-3 sm:px-6">
                                        <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $badge }}">{{ $label }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-10 text-center text-gray-500 sm:px-6">Nenhuma cobrança para esta empresa.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
