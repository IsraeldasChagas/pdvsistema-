@php
    $telas = \App\Models\Company::tenantSelectableScreens();
    $checked = $checked ?? [];
@endphp
<div class="rounded-lg border border-gray-100 bg-gray-50/80 p-4 sm:p-5">
    <h2 class="text-base font-semibold text-gray-900">Módulos liberados nesta empresa</h2>
    <p class="mt-1 text-xs leading-relaxed text-gray-500">
        Escolha quais telas do PDV ficam disponíveis para esta empresa. O administrador e os demais usuários só acessam o que estiver marcado aqui (e, para não administradores, também o que estiver liberado no cadastro do usuário).
    </p>
    <div class="mt-4 grid grid-cols-1 gap-2 sm:grid-cols-2">
        @foreach ($telas as $t)
            <label class="flex cursor-pointer items-center gap-2.5 rounded-lg border border-gray-200 bg-white px-3 py-2.5 text-sm text-gray-800 shadow-sm transition hover:border-gray-300">
                <input
                    type="checkbox"
                    name="allowed_screens[]"
                    value="{{ $t['key'] }}"
                    @checked(in_array($t['key'], $checked, true))
                    class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                />
                <span>{{ $t['label'] }}</span>
            </label>
        @endforeach
    </div>
    @error('allowed_screens')
        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
    @enderror
    @error('allowed_screens.*')
        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>
