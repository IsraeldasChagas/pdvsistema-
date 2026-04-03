@php
    $showEmpresaSelect = $showEmpresaSelect ?? false;
    $showSuperAdminRole = $showSuperAdminRole ?? false;
    $empresas = $empresas ?? collect();
    $roleValue = old('role', $user->role ?? 'vendedor');
@endphp

<div class="rounded-lg border border-gray-200 bg-gray-50/80 p-4">
    <span class="block text-sm font-medium text-gray-700">Foto do usuário</span>
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
                    class="mt-1 h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                />
                <span class="text-sm text-gray-700">Remover foto atual</span>
            </label>
        </div>
    @endif
    <input
        type="file"
        name="avatar"
        id="avatar"
        accept="image/jpeg,image/png,image/webp,image/gif"
        class="mt-3 block w-full text-sm text-gray-600 file:mr-4 file:rounded-lg file:border-0 file:bg-blue-50 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-blue-700 hover:file:bg-blue-100"
    />
    @error('avatar')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>

<div>
    <label for="name" class="block text-sm font-medium text-gray-700">
        Nome <span class="text-red-500">*</span>
    </label>
    <input
        type="text"
        name="name"
        id="name"
        value="{{ old('name', $user->name) }}"
        placeholder="Nome completo"
        required
        autocomplete="name"
        class="mt-2 block w-full rounded-lg border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 @error('name') border-red-500 @enderror"
    />
    @error('name')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>

<div>
    <label for="email" class="block text-sm font-medium text-gray-700">
        E-mail <span class="text-red-500">*</span>
    </label>
    <input
        type="email"
        name="email"
        id="email"
        value="{{ old('email', $user->email) }}"
        required
        autocomplete="email"
        class="mt-2 block w-full rounded-lg border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 @error('email') border-red-500 @enderror"
    />
    @error('email')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>

<div>
    <label for="role" class="block text-sm font-medium text-gray-700">
        Cargo <span class="text-red-500">*</span> <span class="font-normal text-gray-500">(Role)</span>
    </label>
    <select
        name="role"
        id="role"
        required
        class="mt-2 block w-full rounded-lg border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 @error('role') border-red-500 @enderror"
    >
        <option value="vendedor" @selected($roleValue === 'vendedor')>Vendedor</option>
        <option value="gerente" @selected($roleValue === 'gerente')>Gerente</option>
        <option value="administrador" @selected($roleValue === 'administrador')>Administrador</option>
        @if ($showSuperAdminRole)
            <option value="super_admin" @selected($roleValue === 'super_admin')>Super administrador</option>
        @endif
    </select>
    @error('role')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>

<div class="rounded-lg border border-gray-100 bg-gray-50/80 px-4 py-3">
    <label class="flex cursor-pointer items-start gap-3">
        <input
            type="checkbox"
            name="vendedor_rua"
            value="1"
            class="mt-1 h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
            @checked(old('vendedor_rua', $user->vendedor_rua ?? false))
        />
        <span>
            <span class="block text-sm font-medium text-gray-900">Vendedor de rua</span>
            <span class="mt-0.5 block text-sm text-gray-600">
                Vende fora da loja e pode receber estoque (entregas). Cadastro completo de parceiro: use o menu <span class="font-semibold">Parceiros</span>.
            </span>
        </span>
    </label>
</div>

<div class="border-t border-gray-100 pt-6">
    <h2 class="text-sm font-semibold text-gray-900">Telas permitidas</h2>
    <p class="mt-1 text-sm text-gray-600">Marque as telas que este usuário pode acessar.</p>
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
                    class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                    @checked($isChecked)
                />
                <span>{{ $screen['label'] }}</span>
            </label>
        @endforeach
    </div>
</div>

@if ($showEmpresaSelect)
    <div>
        <label for="company_id" class="block text-sm font-medium text-gray-700">
            Empresa <span class="text-red-500">*</span>
        </label>
        <select
            name="company_id"
            id="company_id"
            class="mt-2 block w-full rounded-lg border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 @error('company_id') border-red-500 @enderror"
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
        <p class="mt-1 text-xs text-gray-500">Obrigatório para Administrador e Vendedor. Super administrador não fica vinculado a uma empresa.</p>
    </div>
@endif

@if ($isEdit)
    <div class="rounded-lg border border-gray-100 bg-gray-50/80 px-4 py-3">
        <label class="flex cursor-pointer items-start gap-3">
            <input
                type="checkbox"
                name="is_active"
                value="1"
                class="mt-1 h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                @checked(old('is_active', $user->is_active ?? true))
            />
            <span>
                <span class="block text-sm font-medium text-gray-900">Usuário ativo</span>
                <span class="mt-0.5 block text-sm text-gray-600">Desmarque para bloquear o acesso ao sistema.</span>
            </span>
        </label>
    </div>
@endif

<div>
    <label for="password" class="block text-sm font-medium text-gray-700">
        Senha
        @unless ($isEdit)
            <span class="text-red-500">*</span>
        @else
            <span class="font-normal text-gray-500">(opcional)</span>
        @endunless
    </label>
    <input
        type="password"
        name="password"
        id="password"
        @unless ($isEdit) required @endunless
        autocomplete="new-password"
        class="mt-2 block w-full rounded-lg border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 @error('password') border-red-500 @enderror"
    />
    @error('password')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>

<div>
    <label for="password_confirmation" class="block text-sm font-medium text-gray-700">
        Confirmar senha
        @unless ($isEdit)
            <span class="text-red-500">*</span>
        @endunless
    </label>
    <input
        type="password"
        name="password_confirmation"
        id="password_confirmation"
        @unless ($isEdit) required @endunless
        placeholder="{{ $isEdit ? '' : 'Repita a senha' }}"
        autocomplete="new-password"
        class="mt-2 block w-full rounded-lg border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
    />
</div>
