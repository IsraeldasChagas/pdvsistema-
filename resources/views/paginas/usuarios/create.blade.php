<x-app-layout>
    <div class="min-h-[calc(100vh-3.5rem)] bg-[#f8f9fa] px-4 py-8 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-4xl">
            <h1 class="text-3xl font-bold tracking-tight text-gray-900">Novo Usuário</h1>

            <form
                action="{{ route('usuarios.store') }}"
                method="post"
                enctype="multipart/form-data"
                class="mt-8 space-y-6 rounded-xl border border-gray-200 bg-white p-6 shadow-sm sm:p-8"
            >
                @csrf

                @php
                    $checkedScreens = old('screens', $defaultCheckedScreens);
                @endphp

                @include('paginas.usuarios._fields', [
                    'user' => $user,
                    'empresas' => $empresas,
                    'showEmpresaSelect' => auth()->user()->isSuperAdmin(),
                    'showSuperAdminRole' => auth()->user()->isSuperAdmin(),
                    'screensConfig' => $screensConfig,
                    'checkedScreens' => $checkedScreens,
                    'isEdit' => false,
                ])

                <div class="flex flex-wrap gap-3 border-t border-gray-100 pt-6">
                    <button
                        type="submit"
                        class="rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                    >
                        Salvar
                    </button>
                    <a
                        href="{{ route('modulos.usuarios') }}"
                        class="rounded-lg border border-gray-300 bg-white px-5 py-2.5 text-sm font-semibold text-gray-800 shadow-sm transition hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-400 focus:ring-offset-2"
                    >
                        Voltar
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
