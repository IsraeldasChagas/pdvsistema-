<x-app-layout>
    @php
        $contextoNome = \App\Support\CurrentCompany::model()?->nome ?? 'Empresa Padrão';
        $isEdit = $plano->exists;
        $opts = \App\Models\SaasPlan::periodicidadeOptions();
    @endphp
    <div class="min-h-[calc(100vh-3.5rem)] bg-[#f8f9fa] px-4 py-8 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-3xl">
            <p class="text-sm text-gray-500">{{ $contextoNome }}</p>

            <a href="{{ route('financeiro.saas.planos') }}" class="mt-3 inline-flex items-center gap-1 text-sm font-medium text-gray-500 hover:text-gray-800">
                <span aria-hidden="true">←</span> Voltar
            </a>

            <h1 class="mt-2 text-2xl font-bold tracking-tight text-gray-900 sm:text-3xl">{{ $isEdit ? 'Editar plano' : 'Novo Plano' }}</h1>

            <form
                action="{{ $isEdit ? route('financeiro.saas.plans.update', $plano) : route('financeiro.saas.plans.store') }}"
                method="post"
                class="mt-8 space-y-5 rounded-xl border border-gray-200 bg-white p-6 shadow-sm sm:p-8"
            >
                @csrf
                @if ($isEdit)
                    @method('PUT')
                @endif

                <div>
                    <label for="nome" class="block text-sm font-medium text-gray-700">
                        Nome <span class="text-blue-600">*</span>
                    </label>
                    <input
                        type="text"
                        name="nome"
                        id="nome"
                        value="{{ old('nome', $plano->nome) }}"
                        required
                        placeholder="Ex: Básico, Profissional"
                        class="mt-1.5 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm placeholder:text-gray-400 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 @error('nome') border-red-500 @enderror"
                    />
                    @error('nome')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="descricao" class="block text-sm font-medium text-gray-700">Descrição</label>
                    <textarea
                        name="descricao"
                        id="descricao"
                        rows="4"
                        placeholder="Descreva o que está incluído neste plano…"
                        class="mt-1.5 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm placeholder:text-gray-400 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 @error('descricao') border-red-500 @enderror"
                    >{{ old('descricao', $plano->descricao) }}</textarea>
                    @error('descricao')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 gap-5 md:grid-cols-2 md:gap-6">
                    <div>
                        <label for="valor_mensal" class="block text-sm font-medium text-gray-700">
                            Valor (R$) <span class="text-blue-600">*</span>
                        </label>
                        <input
                            type="number"
                            name="valor_mensal"
                            id="valor_mensal"
                            step="0.01"
                            min="0"
                            required
                            value="{{ old('valor_mensal', $plano->valor_mensal) }}"
                            placeholder="0,00"
                            class="mt-1.5 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm placeholder:text-gray-400 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 @error('valor_mensal') border-red-500 @enderror"
                        />
                        @error('valor_mensal')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="periodicidade" class="block text-sm font-medium text-gray-700">Periodicidade</label>
                        <select
                            name="periodicidade"
                            id="periodicidade"
                            class="mt-1.5 w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 @error('periodicidade') border-red-500 @enderror"
                        >
                            @foreach ($opts as $value => $label)
                                <option value="{{ $value }}" @selected(old('periodicidade', $plano->periodicidade ?? 'mensal') === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('periodicidade')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-5 md:grid-cols-2 md:gap-6">
                    <div>
                        <label for="limite_usuarios" class="block text-sm font-medium text-gray-700">Limite de usuários</label>
                        <input
                            type="number"
                            name="limite_usuarios"
                            id="limite_usuarios"
                            min="1"
                            value="{{ old('limite_usuarios', $plano->limite_usuarios) }}"
                            placeholder="Ilimitado"
                            class="mt-1.5 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm placeholder:text-gray-400 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 @error('limite_usuarios') border-red-500 @enderror"
                        />
                        @error('limite_usuarios')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="limite_unidades" class="block text-sm font-medium text-gray-700">Limite de unidades</label>
                        <input
                            type="number"
                            name="limite_unidades"
                            id="limite_unidades"
                            min="1"
                            value="{{ old('limite_unidades', $plano->limite_unidades) }}"
                            placeholder="Ilimitado"
                            class="mt-1.5 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm placeholder:text-gray-400 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 @error('limite_unidades') border-red-500 @enderror"
                        />
                        @error('limite_unidades')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <input type="hidden" name="ativo" value="0" />
                <label class="flex items-center gap-2 text-sm text-gray-700">
                    <input type="checkbox" name="ativo" value="1" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500" @checked(old('ativo', $plano->ativo ?? true)) />
                    Plano ativo
                </label>

                <div class="border-t border-gray-100 pt-6">
                    <button type="submit" class="rounded-lg bg-blue-600 px-6 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-blue-700">Salvar</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
