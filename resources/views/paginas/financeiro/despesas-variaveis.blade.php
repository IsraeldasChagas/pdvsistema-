<x-app-layout>
    <div class="min-h-[calc(100vh-3.5rem)] bg-[#f8f9fa] px-4 py-8 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-5xl">
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
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <h1 class="text-3xl font-bold tracking-tight text-gray-900">Despesas Variáveis</h1>
                    <p class="mt-1 text-sm text-gray-600">Registre gastos pontuais (ex.: manutenção, compras extras, taxas avulsas).</p>
                </div>
            </div>

            <section class="mt-8 overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
                <div class="border-b border-gray-100 px-5 py-4">
                    <h2 class="text-base font-bold text-gray-900">Nova despesa variável</h2>
                    <p class="mt-0.5 text-sm text-gray-500">Informe o valor e a data em que a despesa ocorreu; o restante é opcional.</p>
                </div>

                <form action="{{ route('financeiro.despesas_variaveis.store') }}" method="post" enctype="multipart/form-data" class="space-y-5 px-5 py-5">
                    @csrf
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div class="sm:col-span-2">
                            <label for="descricao" class="block text-sm font-bold text-gray-900">Descrição</label>
                            <input
                                id="descricao"
                                name="descricao"
                                type="text"
                                value="{{ old('descricao') }}"
                                placeholder="Ex: Troca de peça, frete, material de escritório"
                                class="mt-1.5 w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm text-gray-900 shadow-sm focus:border-sky-500 focus:outline-none focus:ring-1 focus:ring-sky-500"
                                required
                            />
                        </div>

                        <div x-data="{ showCatModal: false }">
                            <div class="flex items-center justify-between gap-3">
                                <label for="variable_expense_category_id" class="block text-sm font-bold text-gray-900">Categoria</label>
                                <button type="button" class="text-xs font-semibold text-blue-700 hover:text-blue-900" @click="showCatModal = true">
                                    + Nova categoria
                                </button>
                            </div>
                            <select
                                id="variable_expense_category_id"
                                name="variable_expense_category_id"
                                class="mt-1.5 w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm text-gray-900 shadow-sm focus:border-sky-500 focus:outline-none focus:ring-1 focus:ring-sky-500"
                            >
                                <option value="">— Selecione —</option>
                                @php
                                    $selCat = old('variable_expense_category_id', session('select_variable_expense_category_id'));
                                @endphp
                                @foreach ($categorias as $c)
                                    <option value="{{ $c->id }}" @selected((string) $selCat === (string) $c->id)>{{ $c->nome }}</option>
                                @endforeach
                            </select>

                            <div
                                x-show="showCatModal"
                                x-cloak
                                class="fixed inset-0 z-50 flex items-end justify-center bg-black/50 p-6 backdrop-blur-[2px] sm:items-center sm:p-12"
                                @click="showCatModal = false"
                            >
                                <div
                                    @click.stop
                                    class="w-full max-w-xl overflow-hidden rounded-xl bg-white shadow-xl ring-1 ring-black/10"
                                >
                                    <form
                                        action="{{ route('financeiro.categorias_despesas_variaveis.store') }}"
                                        method="post"
                                        class="px-10 py-9 pb-14 sm:px-14 sm:py-12 sm:pb-16"
                                    >
                                        @csrf
                                        <div class="mx-auto max-w-md space-y-7">
                                            <div class="space-y-1.5 pb-1">
                                                <h3 class="text-lg font-bold tracking-tight text-gray-900">Nova categoria</h3>
                                                <p class="text-sm leading-relaxed text-gray-500">Cria a categoria e já volta selecionada.</p>
                                            </div>
                                            <div class="space-y-1.5">
                                                <label for="var_cat_nome" class="block text-sm font-bold text-gray-900">Nome</label>
                                                <input
                                                    id="var_cat_nome"
                                                    name="nome"
                                                    type="text"
                                                    placeholder="Ex: Manutenção"
                                                    class="w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm text-gray-900 shadow-sm focus:border-sky-500 focus:outline-none focus:ring-1 focus:ring-sky-500"
                                                    required
                                                />
                                            </div>
                                            <div class="space-y-1.5">
                                                <label for="var_cat_cor" class="block text-sm font-bold text-gray-900">Cor (opcional)</label>
                                                <input
                                                    id="var_cat_cor"
                                                    name="cor"
                                                    type="text"
                                                    placeholder="Ex: #2563eb"
                                                    class="w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm text-gray-900 shadow-sm focus:border-sky-500 focus:outline-none focus:ring-1 focus:ring-sky-500"
                                                />
                                            </div>
                                            <div class="flex flex-row flex-wrap items-center justify-end gap-3 border-t border-gray-100 pt-6">
                                                <button type="button" class="btn-pdv-ghost btn-pdv-ghost-red px-6 py-2.5" @click="showCatModal = false">
                                                    Cancelar
                                                </button>
                                                <button type="submit" class="btn-pdv btn-pdv-primary px-7 py-2.5">
                                                    Salvar
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <div>
                            <label for="valor" class="block text-sm font-bold text-gray-900">Valor</label>
                            <input
                                id="valor"
                                name="valor"
                                type="text"
                                value="{{ old('valor') }}"
                                placeholder="Ex: 150,00"
                                inputmode="decimal"
                                class="mt-1.5 w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm text-gray-900 shadow-sm focus:border-sky-500 focus:outline-none focus:ring-1 focus:ring-sky-500"
                                required
                            />
                        </div>

                        <div>
                            <label for="data_despesa" class="block text-sm font-bold text-gray-900">Data da despesa</label>
                            <input
                                id="data_despesa"
                                name="data_despesa"
                                type="date"
                                value="{{ old('data_despesa', now()->format('Y-m-d')) }}"
                                class="mt-1.5 w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm text-gray-900 shadow-sm focus:border-sky-500 focus:outline-none focus:ring-1 focus:ring-sky-500"
                                required
                            />
                        </div>

                        <div class="sm:col-span-2">
                            <label for="forma_pagamento" class="block text-sm font-bold text-gray-900">Forma de pagamento</label>
                            <input
                                id="forma_pagamento"
                                name="forma_pagamento"
                                type="text"
                                value="{{ old('forma_pagamento') }}"
                                placeholder="Ex: PIX, cartão, dinheiro"
                                class="mt-1.5 w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm text-gray-900 shadow-sm focus:border-sky-500 focus:outline-none focus:ring-1 focus:ring-sky-500"
                            />
                        </div>
                    </div>

                    <div class="rounded-lg border border-gray-200 bg-gray-50 p-4">
                        <p class="text-sm font-bold text-gray-900">Campos úteis (opcionais)</p>
                        <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div>
                                <label for="fornecedor_nome" class="block text-sm font-bold text-gray-900">Fornecedor / Beneficiário</label>
                                <input
                                    id="fornecedor_nome"
                                    name="fornecedor_nome"
                                    type="text"
                                    value="{{ old('fornecedor_nome') }}"
                                    placeholder="Ex: Oficina X"
                                    class="mt-1.5 w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm text-gray-900 shadow-sm focus:border-sky-500 focus:outline-none focus:ring-1 focus:ring-sky-500"
                                />
                            </div>
                            <div>
                                <label for="fornecedor_doc" class="block text-sm font-bold text-gray-900">Documento (CPF/CNPJ)</label>
                                <input
                                    id="fornecedor_doc"
                                    name="fornecedor_doc"
                                    type="text"
                                    value="{{ old('fornecedor_doc') }}"
                                    placeholder="Opcional"
                                    class="mt-1.5 w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm text-gray-900 shadow-sm focus:border-sky-500 focus:outline-none focus:ring-1 focus:ring-sky-500"
                                />
                            </div>
                            <div>
                                <label for="centro_custo" class="block text-sm font-bold text-gray-900">Centro de custo / Setor</label>
                                <input
                                    id="centro_custo"
                                    name="centro_custo"
                                    type="text"
                                    value="{{ old('centro_custo') }}"
                                    placeholder="Ex: Operacional"
                                    class="mt-1.5 w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm text-gray-900 shadow-sm focus:border-sky-500 focus:outline-none focus:ring-1 focus:ring-sky-500"
                                />
                            </div>
                            <div>
                                <label for="conta" class="block text-sm font-bold text-gray-900">Conta / Caixa</label>
                                <input
                                    id="conta"
                                    name="conta"
                                    type="text"
                                    value="{{ old('conta') }}"
                                    placeholder="Ex: Banco"
                                    class="mt-1.5 w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm text-gray-900 shadow-sm focus:border-sky-500 focus:outline-none focus:ring-1 focus:ring-sky-500"
                                />
                            </div>
                            <div>
                                <label for="anexo" class="block text-sm font-bold text-gray-900">Anexo (PDF/imagem)</label>
                                <input
                                    id="anexo"
                                    name="anexo"
                                    type="file"
                                    accept=".pdf,.png,.jpg,.jpeg,.gif,.webp"
                                    class="mt-1.5 block w-full text-sm text-gray-700 file:mr-4 file:rounded file:border-0 file:bg-blue-600 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-white"
                                />
                            </div>
                            <div class="sm:col-span-2">
                                <label for="observacoes" class="block text-sm font-bold text-gray-900">Observações</label>
                                <textarea
                                    id="observacoes"
                                    name="observacoes"
                                    rows="3"
                                    class="mt-1.5 w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm text-gray-900 shadow-sm focus:border-sky-500 focus:outline-none focus:ring-1 focus:ring-sky-500"
                                >{{ old('observacoes') }}</textarea>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" class="btn-pdv btn-pdv-primary px-6 py-3">
                            Salvar
                        </button>
                    </div>
                </form>
            </section>

            <section class="mt-8 overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
                <div class="border-b border-gray-100 px-5 py-4">
                    <h2 class="text-base font-bold text-gray-900">Cadastradas</h2>
                    <p class="mt-0.5 text-sm text-gray-500">Últimas 200 despesas variáveis desta empresa (mais recentes primeiro).</p>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-left text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="whitespace-nowrap px-4 py-3 font-semibold text-gray-700">Descrição</th>
                                <th class="whitespace-nowrap px-4 py-3 font-semibold text-gray-700">Categoria</th>
                                <th class="whitespace-nowrap px-4 py-3 font-semibold text-gray-700">Valor</th>
                                <th class="whitespace-nowrap px-4 py-3 font-semibold text-gray-700">Data</th>
                                <th class="whitespace-nowrap px-4 py-3 font-semibold text-gray-700">Pagamento</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            @forelse ($rows as $r)
                                <tr class="hover:bg-gray-50/80">
                                    <td class="px-4 py-3 font-medium text-gray-900">{{ $r->descricao }}</td>
                                    <td class="whitespace-nowrap px-4 py-3 text-gray-700">{{ $r->category?->nome ?? '—' }}</td>
                                    <td class="whitespace-nowrap px-4 py-3 font-semibold text-gray-900">
                                        R$ {{ number_format((float) $r->valor, 2, ',', '.') }}
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-3 text-gray-700">
                                        {{ $r->data_despesa?->format('d/m/Y') ?? '—' }}
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-3 text-gray-700">{{ $r->forma_pagamento ?: '—' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-10 text-center text-sm text-gray-500">
                                        Nenhuma despesa variável cadastrada ainda.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </div>
</x-app-layout>
