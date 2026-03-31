<x-app-layout>
    @php
        $contextoNome = \App\Support\CurrentCompany::model()?->nome ?? 'Empresa Padrão';
        $telasChecked = old('allowed_screens', $empresa->screensCheckedForForm());
        if (! is_array($telasChecked)) {
            $telasChecked = $empresa->screensCheckedForForm();
        }
    @endphp
    <div class="min-h-[calc(100vh-3.5rem)] bg-[#f8f9fa] px-4 py-8 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-2xl">
            <p class="text-sm text-gray-500">{{ $contextoNome }}</p>
            <h1 class="mt-1 text-3xl font-bold tracking-tight text-gray-900">Editar empresa</h1>

            <form action="{{ route('empresas.update', $empresa) }}" method="post" class="mt-8 space-y-4 rounded-xl border border-gray-200 bg-white p-6 shadow-sm sm:p-8">
                @csrf
                @method('PUT')
                <div>
                    <label for="nome" class="block text-sm font-medium text-gray-700">Nome <span class="text-red-500">*</span></label>
                    <input
                        type="text"
                        name="nome"
                        id="nome"
                        value="{{ old('nome', $empresa->nome) }}"
                        required
                        placeholder="Nome da empresa"
                        class="mt-1.5 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm placeholder:text-gray-400 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 @error('nome') border-red-500 @enderror"
                    />
                    @error('nome')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="cnpj" class="block text-sm font-medium text-gray-700">CNPJ</label>
                    <input
                        type="text"
                        name="cnpj"
                        id="cnpj"
                        value="{{ old('cnpj', $empresa->cnpj) }}"
                        inputmode="numeric"
                        placeholder="00.000.000/0001-00"
                        maxlength="18"
                        class="mt-1.5 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm placeholder:text-gray-400 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 @error('cnpj') border-red-500 @enderror"
                    />
                    @error('cnpj')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="endereco" class="block text-sm font-medium text-gray-700">Endereço</label>
                    <input
                        type="text"
                        name="endereco"
                        id="endereco"
                        value="{{ old('endereco', $empresa->endereco) }}"
                        placeholder="Endereço completo"
                        class="mt-1.5 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm placeholder:text-gray-400 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 @error('endereco') border-red-500 @enderror"
                    />
                    @error('endereco')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="telefone" class="block text-sm font-medium text-gray-700">Telefone</label>
                    <input
                        type="text"
                        name="telefone"
                        id="telefone"
                        value="{{ old('telefone', $empresa->telefone) }}"
                        inputmode="tel"
                        placeholder="(00) 00000-0000"
                        maxlength="15"
                        class="mt-1.5 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm placeholder:text-gray-400 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 @error('telefone') border-red-500 @enderror"
                    />
                    @error('telefone')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">E-mail da empresa</label>
                    <input
                        type="email"
                        name="email"
                        id="email"
                        value="{{ old('email', $empresa->email) }}"
                        placeholder="admin@syspdv.com"
                        class="mt-1.5 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm placeholder:text-gray-400 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 @error('email') border-red-500 @enderror"
                    />
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="saas_plan_id" class="block text-sm font-medium text-gray-700">Plano de cobrança</label>
                    <select
                        name="saas_plan_id"
                        id="saas_plan_id"
                        class="mt-1.5 w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 @error('saas_plan_id') border-red-500 @enderror"
                    >
                        <option value="">Sem plano</option>
                        @foreach ($planos as $plano)
                            <option value="{{ $plano->id }}" @selected((string) old('saas_plan_id', $empresa->saas_plan_id) === (string) $plano->id)>{{ $plano->nome }}</option>
                        @endforeach
                    </select>
                    @error('saas_plan_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                @include('paginas.empresas.partials.telas-form', ['checked' => $telasChecked])

                <input type="hidden" name="ativo" value="0" />
                <label class="flex items-center gap-2 text-sm text-gray-700">
                    <input type="checkbox" name="ativo" value="1" class="rounded border-gray-300 text-blue-600" @checked(old('ativo', $empresa->ativo)) />
                    Empresa ativa
                </label>
                <input type="hidden" name="billing_blocked" value="0" />
                <label class="flex items-center gap-2 text-sm text-gray-700">
                    <input type="checkbox" name="billing_blocked" value="1" class="rounded border-gray-300 text-rose-600" @checked(old('billing_blocked', $empresa->billing_blocked)) />
                    Bloqueada por cobrança (SaaS)
                </label>
                <div class="flex flex-wrap gap-3 border-t border-gray-100 pt-6">
                    <button type="submit" class="rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-blue-700">Salvar</button>
                    <a href="{{ route('empresas.index') }}" class="rounded-lg border border-gray-300 bg-white px-5 py-2.5 text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-50">Voltar</a>
                </div>
            </form>
        </div>
    </div>
    <script>
        (function () {
            function digits(s) {
                return String(s || '').replace(/\D/g, '');
            }
            function maskCnpj(v) {
                var d = digits(v).slice(0, 14);
                if (d.length <= 2) return d;
                if (d.length <= 5) return d.slice(0, 2) + '.' + d.slice(2);
                if (d.length <= 8) return d.slice(0, 2) + '.' + d.slice(2, 5) + '.' + d.slice(5);
                if (d.length <= 12) return d.slice(0, 2) + '.' + d.slice(2, 5) + '.' + d.slice(5, 8) + '/' + d.slice(8);
                return d.slice(0, 2) + '.' + d.slice(2, 5) + '.' + d.slice(5, 8) + '/' + d.slice(8, 12) + '-' + d.slice(12);
            }
            function maskPhone(v) {
                var d = digits(v).slice(0, 11);
                if (d.length <= 2) return d.length ? '(' + d : '';
                if (d.length <= 6) return '(' + d.slice(0, 2) + ') ' + d.slice(2);
                if (d.length <= 10) return '(' + d.slice(0, 2) + ') ' + d.slice(2, 6) + '-' + d.slice(6);
                return '(' + d.slice(0, 2) + ') ' + d.slice(2, 7) + '-' + d.slice(7);
            }
            var cnpj = document.getElementById('cnpj');
            var tel = document.getElementById('telefone');
            if (cnpj) {
                cnpj.addEventListener('input', function () {
                    var c = this.selectionStart;
                    var before = this.value.length;
                    this.value = maskCnpj(this.value);
                    var after = this.value.length;
                    this.setSelectionRange(Math.max(0, c + (after - before)), Math.max(0, c + (after - before)));
                });
            }
            if (tel) {
                tel.addEventListener('input', function () {
                    var c = this.selectionStart;
                    var before = this.value.length;
                    this.value = maskPhone(this.value);
                    var after = this.value.length;
                    this.setSelectionRange(Math.max(0, c + (after - before)), Math.max(0, c + (after - before)));
                });
            }
        })();
    </script>
</x-app-layout>
