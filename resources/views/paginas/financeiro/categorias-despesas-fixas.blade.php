<x-app-layout>
    <div class="min-h-[calc(100vh-3.5rem)] bg-[#f8f9fa] px-4 py-8 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-4xl">
            @if (session('status'))
                <div class="mb-4 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                    <p class="font-semibold">Não foi possível salvar.</p>
                    <ul class="mt-2 list-inside list-disc">
                        @foreach ($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="flex items-start gap-3">
                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-white text-slate-700 shadow-sm ring-1 ring-gray-200">
                    <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 01 13.5 18v-2.25z" />
                    </svg>
                </div>
                <div>
                    <h1 class="text-3xl font-bold tracking-tight text-gray-900">Categorias (Despesas Fixas)</h1>
                    <p class="mt-1 text-sm text-gray-600">Crie categorias para usar no select do formulário.</p>
                </div>
            </div>

            <section class="mt-8 overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
                <div class="border-b border-gray-100 px-5 py-4">
                    <h2 class="text-base font-bold text-gray-900">Nova categoria</h2>
                </div>
                <form action="{{ route('financeiro.categorias_despesas_fixas.store') }}" method="post" class="grid grid-cols-1 gap-4 px-5 py-5 sm:grid-cols-3">
                    @csrf
                    <div class="sm:col-span-2">
                        <label for="nome" class="block text-sm font-bold text-gray-900">Nome</label>
                        <input
                            id="nome"
                            name="nome"
                            type="text"
                            value="{{ old('nome') }}"
                            placeholder="Ex: Utilidades"
                            class="mt-1.5 w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm text-gray-900 shadow-sm focus:border-sky-500 focus:outline-none focus:ring-1 focus:ring-sky-500"
                            required
                        />
                    </div>
                    <div>
                        <label for="cor" class="block text-sm font-bold text-gray-900">Cor (opcional)</label>
                        <input
                            id="cor"
                            name="cor"
                            type="text"
                            value="{{ old('cor') }}"
                            placeholder="Ex: #2563eb"
                            class="mt-1.5 w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm text-gray-900 shadow-sm focus:border-sky-500 focus:outline-none focus:ring-1 focus:ring-sky-500"
                        />
                    </div>
                    <div class="sm:col-span-3 flex justify-end">
                        <button type="submit" class="btn-pdv btn-pdv-primary px-6 py-3">Salvar</button>
                    </div>
                </form>
            </section>

            <section class="mt-8 overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
                <div class="border-b border-gray-100 px-5 py-4">
                    <h2 class="text-base font-bold text-gray-900">Cadastradas</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-left text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 font-semibold text-gray-700">Nome</th>
                                <th class="px-4 py-3 font-semibold text-gray-700">Cor</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            @forelse ($rows as $r)
                                <tr class="hover:bg-gray-50/80">
                                    <td class="px-4 py-3 font-medium text-gray-900">{{ $r->nome }}</td>
                                    <td class="px-4 py-3 text-gray-700">{{ $r->cor ?: '—' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="px-4 py-10 text-center text-sm text-gray-500">
                                        Nenhuma categoria cadastrada ainda.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="border-t border-gray-100 px-5 py-4">
                    <a href="{{ route('financeiro.despesas_fixas') }}" class="text-sm font-semibold text-blue-700 hover:text-blue-900">
                        ← Voltar para Despesas Fixas
                    </a>
                </div>
            </section>
        </div>
    </div>
</x-app-layout>

