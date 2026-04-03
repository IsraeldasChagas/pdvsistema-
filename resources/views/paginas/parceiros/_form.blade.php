@php
    $showEmpresaSelect = $showEmpresaSelect ?? false;
    $empresas = $empresas ?? collect();
    $isEdit = $isEdit ?? false;
@endphp

<div class="rounded-lg border border-amber-200 bg-amber-50/70 px-4 py-3 text-sm text-amber-950">
    <p class="font-medium">Parceiro = usuário vendedor com entregas (estoque de rua).</p>
    <p class="mt-1 text-amber-900/90">Preencha CPF ou CNPJ e o endereço para acerto e documentação.</p>
</div>

<div class="rounded-lg border border-gray-200 bg-gray-50/80 p-4">
    <span class="block text-sm font-medium text-gray-700">Foto</span>
    <p class="mt-0.5 text-xs text-gray-500">Opcional. JPG, PNG ou WebP até 2&nbsp;MB.</p>
    @if ($isEdit && $user->avatar_path)
        <div class="mt-3 flex items-center gap-4">
            <img
                src="{{ $user->avatarUrl() }}"
                alt="Foto atual"
                class="h-20 w-20 shrink-0 rounded-full border border-gray-200 object-cover bg-white"
            />
            <label class="flex cursor-pointer items-start gap-2">
                <input
                    type="checkbox"
                    name="remover_foto"
                    value="1"
                    class="mt-1 h-4 w-4 rounded border-gray-300 text-amber-600 focus:ring-amber-500"
                />
                <span class="text-sm text-gray-700">Remover foto atual</span>
            </label>
        </div>
    @endif
    <input
        type="file"
        name="avatar"
        id="avatar_parceiro"
        accept="image/jpeg,image/png,image/webp,image/gif"
        class="mt-3 block w-full text-sm text-gray-600 file:mr-4 file:rounded-lg file:border-0 file:bg-amber-50 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-amber-800 hover:file:bg-amber-100"
    />
    @error('avatar')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>

<div>
    <label for="name_parceiro" class="block text-sm font-medium text-gray-700">Nome <span class="text-red-500">*</span></label>
    <input
        type="text"
        name="name"
        id="name_parceiro"
        value="{{ old('name', $user->name) }}"
        required
        autocomplete="name"
        class="mt-2 block w-full rounded-lg border border-gray-300 px-3 py-2 shadow-sm focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500 @error('name') border-red-500 @enderror"
    />
    @error('name')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>

<div>
    <label for="email_parceiro" class="block text-sm font-medium text-gray-700">E-mail (login) <span class="text-red-500">*</span></label>
    <input
        type="email"
        name="email"
        id="email_parceiro"
        value="{{ old('email', $user->email) }}"
        required
        autocomplete="email"
        class="mt-2 block w-full rounded-lg border border-gray-300 px-3 py-2 shadow-sm focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500 @error('email') border-red-500 @enderror"
    />
    @error('email')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>

<div class="space-y-4 rounded-lg border border-amber-200 bg-white p-4 shadow-sm">
    <h3 class="text-sm font-semibold text-gray-900">Documento e contato</h3>
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        <div>
            <label for="parceiro_tipo_documento" class="block text-sm font-medium text-gray-700">Tipo <span class="text-red-500">*</span></label>
            <select
                name="parceiro_tipo_documento"
                id="parceiro_tipo_documento"
                required
                class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 shadow-sm focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500 @error('parceiro_tipo_documento') border-red-500 @enderror"
            >
                <option value="">-- Selecione --</option>
                <option value="cpf" @selected(old('parceiro_tipo_documento', $user->parceiro_tipo_documento) === 'cpf')>Pessoa física (CPF)</option>
                <option value="cnpj" @selected(old('parceiro_tipo_documento', $user->parceiro_tipo_documento) === 'cnpj')>Pessoa jurídica (CNPJ)</option>
            </select>
            @error('parceiro_tipo_documento')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
        <div>
            <label for="parceiro_documento" class="block text-sm font-medium text-gray-700">Número <span class="text-red-500">*</span></label>
            <input
                type="text"
                name="parceiro_documento"
                id="parceiro_documento"
                value="{{ old('parceiro_documento', $user->parceiro_documento) }}"
                inputmode="numeric"
                autocomplete="off"
                placeholder="Somente números"
                class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 shadow-sm focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500 @error('parceiro_documento') border-red-500 @enderror"
            />
            @error('parceiro_documento')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
    </div>
    <div>
        <label for="parceiro_razao_social" class="block text-sm font-medium text-gray-700">Razão social <span class="font-normal text-gray-500">(opcional, PJ)</span></label>
        <input
            type="text"
            name="parceiro_razao_social"
            id="parceiro_razao_social"
            value="{{ old('parceiro_razao_social', $user->parceiro_razao_social) }}"
            class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 shadow-sm focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500 @error('parceiro_razao_social') border-red-500 @enderror"
        />
        @error('parceiro_razao_social')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>
    <div>
        <label for="telefone_parceiro" class="block text-sm font-medium text-gray-700">Telefone / WhatsApp</label>
        <input
            type="text"
            name="telefone"
            id="telefone_parceiro"
            value="{{ old('telefone', $user->telefone) }}"
            autocomplete="tel"
            class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 shadow-sm focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500 @error('telefone') border-red-500 @enderror"
        />
        @error('telefone')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>
</div>

<div class="space-y-4 rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
    <h3 class="text-sm font-semibold text-gray-900">Endereço</h3>
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-6">
        <div class="sm:col-span-4">
            <label for="endereco_logradouro" class="block text-sm font-medium text-gray-700">Logradouro</label>
            <input type="text" name="endereco_logradouro" id="endereco_logradouro" value="{{ old('endereco_logradouro', $user->endereco_logradouro) }}" class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 shadow-sm focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500 @error('endereco_logradouro') border-red-500 @enderror" />
            @error('endereco_logradouro')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
        <div class="sm:col-span-2">
            <label for="endereco_numero" class="block text-sm font-medium text-gray-700">Nº</label>
            <input type="text" name="endereco_numero" id="endereco_numero" value="{{ old('endereco_numero', $user->endereco_numero) }}" class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 shadow-sm focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500 @error('endereco_numero') border-red-500 @enderror" />
            @error('endereco_numero')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
    </div>
    <div>
        <label for="endereco_complemento" class="block text-sm font-medium text-gray-700">Complemento</label>
        <input type="text" name="endereco_complemento" id="endereco_complemento" value="{{ old('endereco_complemento', $user->endereco_complemento) }}" class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 shadow-sm focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500 @error('endereco_complemento') border-red-500 @enderror" />
        @error('endereco_complemento')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
        <div>
            <label for="endereco_bairro" class="block text-sm font-medium text-gray-700">Bairro</label>
            <input type="text" name="endereco_bairro" id="endereco_bairro" value="{{ old('endereco_bairro', $user->endereco_bairro) }}" class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 shadow-sm focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500 @error('endereco_bairro') border-red-500 @enderror" />
            @error('endereco_bairro')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
        <div>
            <label for="endereco_cidade" class="block text-sm font-medium text-gray-700">Cidade</label>
            <input type="text" name="endereco_cidade" id="endereco_cidade" value="{{ old('endereco_cidade', $user->endereco_cidade) }}" class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 shadow-sm focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500 @error('endereco_cidade') border-red-500 @enderror" />
            @error('endereco_cidade')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
        <div class="grid grid-cols-2 gap-3">
            <div>
                <label for="endereco_uf" class="block text-sm font-medium text-gray-700">UF</label>
                <input type="text" name="endereco_uf" id="endereco_uf" maxlength="2" value="{{ old('endereco_uf', $user->endereco_uf) }}" class="mt-1 block w-full uppercase rounded-lg border border-gray-300 px-3 py-2 shadow-sm focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500 @error('endereco_uf') border-red-500 @enderror" />
                @error('endereco_uf')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="endereco_cep" class="block text-sm font-medium text-gray-700">CEP</label>
                <input type="text" name="endereco_cep" id="endereco_cep" value="{{ old('endereco_cep', $user->endereco_cep) }}" inputmode="numeric" class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 shadow-sm focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500 @error('endereco_cep') border-red-500 @enderror" />
                @error('endereco_cep')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </div>
</div>

<div class="border-t border-gray-100 pt-6">
    <h2 class="text-sm font-semibold text-gray-900">Telas permitidas</h2>
    <p class="mt-1 text-sm text-gray-600">O que este parceiro pode acessar no sistema.</p>
    @error('screens')
        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
    @enderror
    <div class="mt-4 grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-3">
        @foreach ($screensConfig as $screen)
            @php
                $key = $screen['key'];
                $isChecked = in_array($key, $checkedScreens, true);
            @endphp
            <label class="flex cursor-pointer items-center gap-2 rounded-lg border border-gray-200 bg-white px-3 py-2.5 text-sm text-gray-800 shadow-sm hover:border-gray-300">
                <input
                    type="checkbox"
                    name="screens[]"
                    value="{{ $key }}"
                    class="h-4 w-4 rounded border-gray-300 text-amber-600 focus:ring-amber-500"
                    @checked($isChecked)
                />
                <span>{{ $screen['label'] }}</span>
            </label>
        @endforeach
    </div>
</div>

@if ($showEmpresaSelect)
    <div>
        <label for="company_id_parceiro" class="block text-sm font-medium text-gray-700">Empresa <span class="text-red-500">*</span></label>
        <select
            name="company_id"
            id="company_id_parceiro"
            required
            class="mt-2 block w-full rounded-lg border border-gray-300 px-3 py-2 shadow-sm focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500 @error('company_id') border-red-500 @enderror"
        >
            <option value="">-- Selecione --</option>
            @foreach ($empresas as $empresa)
                <option value="{{ $empresa->id }}" @selected((int) old('company_id', $user->company_id) === $empresa->id)>
                    {{ $empresa->nome }}
                </option>
            @endforeach
        </select>
        @error('company_id')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>
@endif

@if ($isEdit)
    <div class="rounded-lg border border-gray-100 bg-gray-50/80 px-4 py-3">
        <label class="flex cursor-pointer items-start gap-3">
            <input
                type="checkbox"
                name="is_active"
                value="1"
                class="mt-1 h-4 w-4 rounded border-gray-300 text-amber-600 focus:ring-amber-500"
                @checked(old('is_active', $user->is_active ?? true))
            />
            <span>
                <span class="block text-sm font-medium text-gray-900">Parceiro ativo</span>
                <span class="mt-0.5 block text-sm text-gray-600">Desmarque para bloquear o acesso ao sistema.</span>
            </span>
        </label>
    </div>
@endif

<div>
    <label for="password_parceiro" class="block text-sm font-medium text-gray-700">
        Senha de acesso
        @unless ($isEdit)
            <span class="text-red-500">*</span>
        @else
            <span class="font-normal text-gray-500">(opcional)</span>
        @endunless
    </label>
    <input
        type="password"
        name="password"
        id="password_parceiro"
        @unless ($isEdit) required @endunless
        autocomplete="new-password"
        class="mt-2 block w-full rounded-lg border border-gray-300 px-3 py-2 shadow-sm focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500 @error('password') border-red-500 @enderror"
    />
    @error('password')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>

<div>
    <label for="password_confirmation_parceiro" class="block text-sm font-medium text-gray-700">
        Confirmar senha
        @unless ($isEdit)
            <span class="text-red-500">*</span>
        @endunless
    </label>
    <input
        type="password"
        name="password_confirmation"
        id="password_confirmation_parceiro"
        @unless ($isEdit) required @endunless
        autocomplete="new-password"
        class="mt-2 block w-full rounded-lg border border-gray-300 px-3 py-2 shadow-sm focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500"
    />
</div>
