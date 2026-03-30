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
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75A2.25 2.25 0 014.5 4.5h15a2.25 2.25 0 012.25 2.25v10.5A2.25 2.25 0 0119.5 19.5h-15a2.25 2.25 0 01-2.25-2.25V6.75zM2.25 10.5h19.5v3H2.25v-3z" />
                    </svg>
                </div>
                <div>
                    <h1 class="text-3xl font-bold tracking-tight text-gray-900">Despesas Fixas</h1>
                    <p class="mt-1 text-sm text-gray-600">Cadastre despesas recorrentes (ex.: aluguel, internet, salários).</p>
                </div>
            </div>

            <section class="mt-8 overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
                <div class="border-b border-gray-100 px-5 py-4">
                    <h2 class="text-base font-bold text-gray-900">Nova despesa fixa</h2>
                    <p class="mt-0.5 text-sm text-gray-500">Preencha o básico agora; os campos úteis são opcionais.</p>
                </div>

                <form action="{{ route('financeiro.despesas_fixas.store') }}" method="post" enctype="multipart/form-data" class="space-y-5 px-5 py-5">
                    @csrf
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div class="sm:col-span-2">
                            <label for="descricao" class="block text-sm font-bold text-gray-900">Descrição</label>
                            <input
                                id="descricao"
                                name="descricao"
                                type="text"
                                value="{{ old('descricao') }}"
                                placeholder="Ex: Aluguel"
                                class="mt-1.5 w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm text-gray-900 shadow-sm focus:border-sky-500 focus:outline-none focus:ring-1 focus:ring-sky-500"
                                required
                            />
                        </div>

                        <div>
                            <label for="categoria" class="block text-sm font-bold text-gray-900">Categoria</label>
                            <input
                                id="categoria"
                                name="categoria"
                                type="text"
                                value="{{ old('categoria') }}"
                                placeholder="Ex: Utilidades"
                                class="mt-1.5 w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm text-gray-900 shadow-sm focus:border-sky-500 focus:outline-none focus:ring-1 focus:ring-sky-500"
                            />
                        </div>

                        <div>
                            <label for="valor" class="block text-sm font-bold text-gray-900">Valor</label>
                            <input
                                id="valor"
                                name="valor"
                                type="text"
                                value="{{ old('valor') }}"
                                placeholder="Ex: 350,00"
                                inputmode="decimal"
                                class="mt-1.5 w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm text-gray-900 shadow-sm focus:border-sky-500 focus:outline-none focus:ring-1 focus:ring-sky-500"
                                required
                            />
                        </div>

                        <div>
                            <label for="periodicidade" class="block text-sm font-bold text-gray-900">Periodicidade</label>
                            <select
                                id="periodicidade"
                                name="periodicidade"
                                class="mt-1.5 w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm text-gray-900 shadow-sm focus:border-sky-500 focus:outline-none focus:ring-1 focus:ring-sky-500"
                            >
                                @php
                                    $per = old('periodicidade', 'mensal');
                                @endphp
                                <option value="mensal" @selected($per === 'mensal')>Mensal</option>
                                <option value="semanal" @selected($per === 'semanal')>Semanal</option>
                                <option value="anual" @selected($per === 'anual')>Anual</option>
                                <option value="a_cada_x_dias" @selected($per === 'a_cada_x_dias')>A cada X dias</option>
                            </select>
                        </div>

                        <div>
                            <label for="dia_vencimento" class="block text-sm font-bold text-gray-900">Dia do vencimento (1–31)</label>
                            <input
                                id="dia_vencimento"
                                name="dia_vencimento"
                                type="number"
                                min="1"
                                max="31"
                                value="{{ old('dia_vencimento') }}"
                                class="mt-1.5 w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm text-gray-900 shadow-sm focus:border-sky-500 focus:outline-none focus:ring-1 focus:ring-sky-500"
                            />
                        </div>

                        <div>
                            <label for="intervalo" class="block text-sm font-bold text-gray-900">Intervalo (dias) — se “A cada X dias”</label>
                            <input
                                id="intervalo"
                                name="intervalo"
                                type="number"
                                min="1"
                                max="3650"
                                value="{{ old('intervalo') }}"
                                class="mt-1.5 w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm text-gray-900 shadow-sm focus:border-sky-500 focus:outline-none focus:ring-1 focus:ring-sky-500"
                            />
                        </div>

                        <div>
                            <label for="data_inicio" class="block text-sm font-bold text-gray-900">Data de início</label>
                            <input
                                id="data_inicio"
                                name="data_inicio"
                                type="date"
                                value="{{ old('data_inicio') }}"
                                class="mt-1.5 w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm text-gray-900 shadow-sm focus:border-sky-500 focus:outline-none focus:ring-1 focus:ring-sky-500"
                            />
                        </div>

                        <div>
                            <label for="status" class="block text-sm font-bold text-gray-900">Status</label>
                            @php
                                $st = old('status', 'ativo');
                            @endphp
                            <select
                                id="status"
                                name="status"
                                class="mt-1.5 w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm text-gray-900 shadow-sm focus:border-sky-500 focus:outline-none focus:ring-1 focus:ring-sky-500"
                            >
                                <option value="ativo" @selected($st === 'ativo')>Ativo</option>
                                <option value="pausado" @selected($st === 'pausado')>Pausado</option>
                                <option value="cancelado" @selected($st === 'cancelado')>Cancelado</option>
                            </select>
                        </div>

                        <div>
                            <label for="forma_pagamento" class="block text-sm font-bold text-gray-900">Forma de pagamento (padrão)</label>
                            <input
                                id="forma_pagamento"
                                name="forma_pagamento"
                                type="text"
                                value="{{ old('forma_pagamento') }}"
                                placeholder="Ex: PIX"
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
                                    placeholder="Ex: Imobiliária X"
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
                                    placeholder="Ex: Administrativo"
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
                                <label for="alerta_dias" class="block text-sm font-bold text-gray-900">Alerta (dias antes)</label>
                                <input
                                    id="alerta_dias"
                                    name="alerta_dias"
                                    type="number"
                                    min="0"
                                    max="365"
                                    value="{{ old('alerta_dias') }}"
                                    placeholder="Ex: 3"
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
                    <p class="mt-0.5 text-sm text-gray-500">Últimas 200 despesas fixas desta empresa.</p>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-left text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="whitespace-nowrap px-4 py-3 font-semibold text-gray-700">Descrição</th>
                                <th class="whitespace-nowrap px-4 py-3 font-semibold text-gray-700">Categoria</th>
                                <th class="whitespace-nowrap px-4 py-3 font-semibold text-gray-700">Valor</th>
                                <th class="whitespace-nowrap px-4 py-3 font-semibold text-gray-700">Periodicidade</th>
                                <th class="whitespace-nowrap px-4 py-3 font-semibold text-gray-700">Venc.</th>
                                <th class="whitespace-nowrap px-4 py-3 font-semibold text-gray-700">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            @forelse ($rows as $r)
                                <tr class="hover:bg-gray-50/80">
                                    <td class="px-4 py-3 font-medium text-gray-900">{{ $r->descricao }}</td>
                                    <td class="whitespace-nowrap px-4 py-3 text-gray-700">{{ $r->categoria ?: '—' }}</td>
                                    <td class="whitespace-nowrap px-4 py-3 font-semibold text-gray-900">
                                        R$ {{ number_format((float) $r->valor, 2, ',', '.') }}
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-3 text-gray-700">{{ $r->periodicidade }}</td>
                                    <td class="whitespace-nowrap px-4 py-3 text-gray-700">
                                        {{ $r->dia_vencimento ? 'Dia '.$r->dia_vencimento : '—' }}
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-3">
                                        @if ($r->status === 'ativo')
                                            <span class="inline-flex rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800">Ativo</span>
                                        @elseif ($r->status === 'pausado')
                                            <span class="inline-flex rounded-full bg-amber-100 px-2.5 py-0.5 text-xs font-medium text-amber-800">Pausado</span>
                                        @else
                                            <span class="inline-flex rounded-full bg-gray-200 px-2.5 py-0.5 text-xs font-medium text-gray-700">Cancelado</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-10 text-center text-sm text-gray-500">
                                        Nenhuma despesa fixa cadastrada ainda.
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

