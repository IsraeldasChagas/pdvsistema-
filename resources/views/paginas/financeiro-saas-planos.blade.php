<x-app-layout>
    @php
        $contextoNome = \App\Support\CurrentCompany::model()?->nome ?? 'Empresa Padrão';
        $brl = static fn ($v) => 'R$ ' . number_format((float) $v, 2, ',', '.');
    @endphp
    <div class="min-h-[calc(100vh-3.5rem)] bg-[#f3f4f6] px-4 py-8 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-5xl">
            @if (session('status'))
                <div class="mb-4 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('status') }}</div>
            @endif

            <p class="text-sm font-medium text-gray-900">{{ $contextoNome }}</p>

            <div class="mt-3 flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                <div class="flex items-start gap-3">
                    <svg class="mt-0.5 h-9 w-9 shrink-0 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.903 2.903 0 00-.084-.869m-5.768 0c-.065.21-.1.433-.1.664v.75h-4.5A.75.75 0 013 6.375v-.75c0-.231.035-.454.1-.664M6.75 7.5H3v9.75A2.25 2.25 0 005.25 19.5h13.5A2.25 2.25 0 0021 17.25V7.5m-9 3h3.75" />
                    </svg>
                    <div>
                        <h1 class="text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">Planos</h1>
                        <p class="mt-0.5 text-sm text-gray-500">Planos de assinatura do sistema</p>
                    </div>
                </div>
                <a
                    href="{{ route('financeiro.saas.plans.create') }}"
                    class="inline-flex shrink-0 items-center justify-center rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-blue-700"
                >
                    + Novo Plano
                </a>
            </div>

            <div class="mt-8 overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
                @if ($planos->isEmpty())
                    <div class="px-6 py-16 text-center">
                        <p class="text-gray-500">Nenhum plano cadastrado.</p>
                        <a href="{{ route('financeiro.saas.plans.create') }}" class="mt-3 inline-block text-sm font-semibold text-blue-600 hover:text-blue-500 hover:underline">Criar primeiro plano</a>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 text-left text-sm">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="px-5 py-3.5 font-semibold text-gray-700 sm:px-6">Nome</th>
                                    <th class="px-5 py-3.5 font-semibold text-gray-700 sm:px-6">Valor</th>
                                    <th class="px-5 py-3.5 font-semibold text-gray-700 sm:px-6">Periodicidade</th>
                                    <th class="px-5 py-3.5 font-semibold text-gray-700 sm:px-6">Usuários / Unidades</th>
                                    <th class="px-5 py-3.5 font-semibold text-gray-700 sm:px-6">Ativo</th>
                                    <th class="px-5 py-3.5 font-semibold text-gray-700 sm:px-6">Ações</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                                @foreach ($planos as $p)
                                    <tr class="hover:bg-gray-50/80">
                                        <td class="px-5 py-4 font-medium text-gray-900 sm:px-6">{{ $p->nome }}</td>
                                        <td class="whitespace-nowrap px-5 py-4 tabular-nums text-gray-700 sm:px-6">{{ $brl($p->valor_mensal) }}</td>
                                        <td class="px-5 py-4 text-gray-600 sm:px-6">{{ $p->periodicidadeLabel() }}</td>
                                        <td class="px-5 py-4 text-sm text-gray-600 sm:px-6">
                                            {{ $p->limiteUsuariosLabel() }} · {{ $p->limiteUnidadesLabel() }}
                                        </td>
                                        <td class="px-5 py-4 sm:px-6">
                                            @if ($p->ativo)
                                                <span class="inline-flex rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-semibold text-green-800">Sim</span>
                                            @else
                                                <span class="inline-flex rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-semibold text-gray-700">Não</span>
                                            @endif
                                        </td>
                                        <td class="whitespace-nowrap px-5 py-4 sm:px-6">
                                            <a href="{{ route('financeiro.saas.plans.edit', $p) }}" class="font-semibold text-indigo-600 hover:text-indigo-500 hover:underline">Editar</a>
                                            <span class="mx-2 text-gray-300">|</span>
                                            <form action="{{ route('financeiro.saas.plans.destroy', $p) }}" method="post" class="inline" onsubmit="return confirm('Excluir este plano?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="font-semibold text-red-600 hover:underline">Excluir</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
