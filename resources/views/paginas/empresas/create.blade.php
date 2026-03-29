<x-app-layout>
    @php
        $contextoNome = \App\Support\CurrentCompany::model()?->nome ?? 'Empresa Padrão';
        $telasChecked = old('allowed_screens', \App\Models\Company::tenantSelectableScreenKeys());
        if (! is_array($telasChecked)) {
            $telasChecked = \App\Models\Company::tenantSelectableScreenKeys();
        }
    @endphp
    <div class="min-h-[calc(100vh-3.5rem)] bg-[#f8f9fa] px-4 py-8 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-2xl">
            <p class="text-sm text-gray-500">{{ $contextoNome }}</p>
            <h1 class="mt-1 text-3xl font-bold tracking-tight text-gray-900">Nova Empresa</h1>

            <form action="{{ route('empresas.store') }}" method="post" class="mt-8 space-y-6 rounded-xl border border-gray-200 bg-white p-6 shadow-sm sm:p-8">
                @csrf

                <div class="space-y-4">
                    <div>
                        <label for="nome" class="block text-sm font-medium text-gray-700">Nome <span class="text-red-500">*</span></label>
                        <input
                            type="text"
                            name="nome"
                            id="nome"
                            value="{{ old('nome') }}"
                            required
                            autocomplete="organization"
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
                            value="{{ old('cnpj') }}"
                            inputmode="numeric"
                            autocomplete="off"
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
                            value="{{ old('endereco') }}"
                            autocomplete="street-address"
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
                            value="{{ old('telefone') }}"
                            inputmode="tel"
                            autocomplete="tel"
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
                            value="{{ old('email') }}"
                            autocomplete="email"
                            placeholder="admin@syspdv.com"
                            class="mt-1.5 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm placeholder:text-gray-400 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 @error('email') border-red-500 @enderror"
                        />
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                @include('paginas.empresas.partials.telas-form', ['checked' => $telasChecked])

                <hr class="border-gray-200" />

                <div class="space-y-4">
                    <h2 class="text-base font-semibold text-gray-900">Administrador da empresa</h2>
                    <div>
                        <label for="admin_nome" class="block text-sm font-medium text-gray-700">Nome do admin <span class="text-red-500">*</span></label>
                        <input
                            type="text"
                            name="admin_nome"
                            id="admin_nome"
                            value="{{ old('admin_nome') }}"
                            required
                            autocomplete="name"
                            placeholder="Nome do administrador"
                            class="mt-1.5 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm placeholder:text-gray-400 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 @error('admin_nome') border-red-500 @enderror"
                        />
                        @error('admin_nome')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="admin_email" class="block text-sm font-medium text-gray-700">E-mail do admin <span class="text-red-500">*</span></label>
                        <input
                            type="email"
                            name="admin_email"
                            id="admin_email"
                            value="{{ old('admin_email') }}"
                            required
                            autocomplete="username"
                            placeholder="admin@empresa.com"
                            class="mt-1.5 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm placeholder:text-gray-400 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 @error('admin_email') border-red-500 @enderror"
                        />
                        @error('admin_email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700">Senha <span class="text-red-500">*</span></label>
                        <input
                            type="password"
                            name="password"
                            id="password"
                            required
                            autocomplete="new-password"
                            class="mt-1.5 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 @error('password') border-red-500 @enderror"
                        />
                        @error('password')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirmar senha <span class="text-red-500">*</span></label>
                        <input
                            type="password"
                            name="password_confirmation"
                            id="password_confirmation"
                            required
                            autocomplete="new-password"
                            placeholder="Repita a senha"
                            class="mt-1.5 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm placeholder:text-gray-400 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                        />
                    </div>
                </div>

                <div class="flex flex-wrap gap-3 border-t border-gray-100 pt-6">
                    <button type="submit" class="rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-blue-700">Cadastrar</button>
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
