<x-app-layout>
    @php
        $contextoNome = \App\Support\CurrentCompany::model()?->nome ?? 'Empresa Padrão';
    @endphp
    <div class="min-h-[calc(100vh-3.5rem)] bg-[#f3f4f6] px-4 py-8 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-6xl">
            <p class="text-sm font-medium text-gray-900">{{ $contextoNome }}</p>

            <div class="mt-3 flex items-start gap-3">
                <svg class="mt-0.5 h-9 w-9 shrink-0 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21" />
                </svg>
                <div>
                    <h1 class="text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">Empresas Clientes</h1>
                    <p class="mt-0.5 text-sm text-gray-500">Empresas contratantes do sistema</p>
                </div>
            </div>

            <div class="mt-8 overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-left text-sm">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-5 py-3.5 font-semibold text-gray-700 sm:px-6">Nome</th>
                                <th class="px-5 py-3.5 font-semibold text-gray-700 sm:px-6">Plano</th>
                                <th class="px-5 py-3.5 font-semibold text-gray-700 sm:px-6">Valor</th>
                                <th class="px-5 py-3.5 font-semibold text-gray-700 sm:px-6">Vencimento</th>
                                <th class="px-5 py-3.5 font-semibold text-gray-700 sm:px-6">Status</th>
                                <th class="px-5 py-3.5 font-semibold text-gray-700 sm:px-6">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            @forelse ($rows as $row)
                                @php
                                    $c = $row->company;
                                    if (! $c->ativo) {
                                        $statusClass = 'bg-gray-100 text-gray-800';
                                        $statusLabel = 'Inativa';
                                    } elseif ($c->billing_blocked) {
                                        $statusClass = 'bg-orange-100 text-orange-900';
                                        $statusLabel = 'Bloqueada';
                                    } else {
                                        $statusClass = 'bg-green-100 text-green-800';
                                        $statusLabel = 'Ativa';
                                    }
                                @endphp
                                <tr class="hover:bg-gray-50/80">
                                    <td class="px-5 py-4 font-medium text-gray-900 sm:px-6">{{ $c->nome }}</td>
                                    <td class="px-5 py-4 text-gray-600 sm:px-6">{{ $row->plano ?? '—' }}</td>
                                    <td class="whitespace-nowrap px-5 py-4 tabular-nums text-gray-700 sm:px-6">{{ $row->valor_label }}</td>
                                    <td class="whitespace-nowrap px-5 py-4 text-gray-600 sm:px-6">{{ $row->vencimento_label }}</td>
                                    <td class="px-5 py-4 sm:px-6">
                                        <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $statusClass }}">{{ $statusLabel }}</span>
                                    </td>
                                    <td class="whitespace-nowrap px-5 py-4 sm:px-6">
                                        <a href="{{ route('financeiro.saas.empresas.show', $c) }}" class="font-semibold text-indigo-600 hover:text-indigo-500 hover:underline">Ver</a>
                                        <span class="mx-2 text-gray-300">|</span>
                                        <a href="{{ route('empresas.edit', $c) }}" class="font-semibold text-indigo-600 hover:text-indigo-500 hover:underline">Editar</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-5 py-10 text-center text-gray-500 sm:px-6">Nenhuma empresa cadastrada.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
