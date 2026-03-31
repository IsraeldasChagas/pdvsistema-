<x-app-layout>
    @php
        $contextoNome = \App\Support\CurrentCompany::model()?->nome ?? 'Empresa Padrão';
    @endphp
    <div class="min-h-[calc(100vh-3.5rem)] bg-[#f8f9fa] px-4 py-8 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-5xl">
            @if (session('status'))
                <div class="mb-4 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('status') }}</div>
            @endif
            @error('delete')
                <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">{{ $message }}</div>
            @enderror

            <p class="text-sm text-gray-500">{{ $contextoNome }}</p>
            <div class="mt-1 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <h1 class="text-3xl font-bold tracking-tight text-gray-900">Empresas</h1>
                <a
                    href="{{ route('empresas.create') }}"
                    class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700"
                >
                    + Nova Empresa
                </a>
            </div>

            <div class="mt-8 overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-left text-sm">
                        <thead class="bg-slate-100">
                            <tr>
                                <th class="px-4 py-3 font-semibold text-gray-700 sm:px-6">Nome</th>
                                <th class="px-4 py-3 font-semibold text-gray-700 sm:px-6">CNPJ</th>
                                <th class="px-4 py-3 font-semibold text-gray-700 sm:px-6">E-mail</th>
                                <th class="px-4 py-3 font-semibold text-gray-700 sm:px-6">Plano</th>
                                <th class="px-4 py-3 font-semibold text-gray-700 sm:px-6">Status</th>
                                <th class="px-4 py-3 font-semibold text-gray-700 sm:px-6">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            @forelse ($empresas as $e)
                                <tr class="hover:bg-gray-50/80">
                                    <td class="px-4 py-3 font-medium text-gray-900 sm:px-6">{{ $e->nome }}</td>
                                    <td class="px-4 py-3 text-gray-600 sm:px-6">{{ $e->cnpj ?: '—' }}</td>
                                    <td class="px-4 py-3 text-gray-600 sm:px-6">{{ $e->email ?: '—' }}</td>
                                    <td class="px-4 py-3 text-gray-600 sm:px-6">{{ $e->saasPlan?->nome ?? '—' }}</td>
                                    <td class="px-4 py-3 sm:px-6">
                                        @if ($e->ativo)
                                            <span class="inline-flex rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-semibold text-green-800">Ativo</span>
                                        @else
                                            <span class="inline-flex rounded-full bg-gray-200 px-2.5 py-0.5 text-xs font-semibold text-gray-700">Inativo</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 sm:px-6">
                                        <a href="{{ route('empresas.edit', $e) }}" class="font-semibold text-blue-600 hover:underline">Editar</a>
                                        <span class="mx-2 text-gray-300">|</span>
                                        <form action="{{ route('empresas.destroy', $e) }}" method="post" class="inline" onsubmit="return confirm('Excluir esta empresa?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="font-semibold text-red-600 hover:underline">Excluir</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-8 text-center text-gray-500">Nenhuma empresa cadastrada.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
