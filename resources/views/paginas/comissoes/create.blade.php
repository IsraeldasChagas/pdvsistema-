<x-app-layout>
    <div class="min-h-[calc(100vh-3.5rem)] bg-slate-50 px-4 py-8 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-2xl">
            <a
                href="{{ route('modulos.comissoes') }}"
                class="inline-flex items-center gap-1 text-sm font-medium text-gray-600 transition hover:text-gray-900"
            >
                <span aria-hidden="true">←</span> Voltar
            </a>

            <h1 class="mt-4 text-2xl font-bold tracking-tight text-gray-900">Adicionar Comissão</h1>
            <p class="mt-1 text-sm text-gray-500">Cadastro manual de comissão</p>

            <form action="{{ route('comissoes.store') }}" method="post" class="mt-8 space-y-6 rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                @csrf

                <div>
                    <label for="user_id" class="block text-sm font-medium text-gray-700">Usuário (vendedor)</label>
                    <select
                        name="user_id"
                        id="user_id"
                        required
                        class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 @error('user_id') border-red-500 @enderror"
                    >
                        <option value="" @selected(old('user_id', '') == '')>Selecione...</option>
                        @foreach ($vendedores as $v)
                            <option value="{{ $v->id }}" @selected(old('user_id') == $v->id)>{{ $v->name }}</option>
                        @endforeach
                    </select>
                    @error('user_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="valor" class="block text-sm font-medium text-gray-700">Valor (R$)</label>
                    <input
                        type="text"
                        name="valor"
                        id="valor"
                        value="{{ old('valor', '0,00') }}"
                        inputmode="decimal"
                        autocomplete="off"
                        required
                        class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 @error('valor') border-red-500 @enderror"
                    />
                    @error('valor')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="percentual" class="block text-sm font-medium text-gray-700">Percentual (%) - opcional</label>
                    <input
                        type="text"
                        name="percentual"
                        id="percentual"
                        value="{{ old('percentual') }}"
                        placeholder="Ex: 5"
                        inputmode="decimal"
                        autocomplete="off"
                        class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 @error('percentual') border-red-500 @enderror"
                    />
                    @error('percentual')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex flex-wrap gap-3 border-t border-gray-100 pt-6">
                    <button type="submit" class="rounded-lg bg-emerald-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2">
                        Salvar
                    </button>
                    <a
                        href="{{ route('modulos.comissoes') }}"
                        class="rounded-lg border border-gray-300 bg-white px-5 py-2.5 text-sm font-semibold text-gray-700 shadow-sm transition hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-400 focus:ring-offset-2"
                    >
                        Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
