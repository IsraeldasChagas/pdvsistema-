<x-app-layout>
    <div class="min-h-[calc(100vh-3.5rem)] bg-[#f8f9fa] px-4 py-8 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-4xl">
            <h1 class="text-3xl font-bold tracking-tight text-gray-900">Editar parceiro</h1>
            <p class="mt-1 text-sm text-gray-600">{{ $user->email }}</p>

            <form
                action="{{ route('parceiros.update', $user) }}"
                method="post"
                enctype="multipart/form-data"
                class="mt-8 space-y-6 rounded-xl border border-amber-200/80 bg-white p-6 shadow-sm sm:p-8"
            >
                @csrf
                @method('PUT')

                @php
                    $checkedScreens = old('screens', $checkedScreens);
                @endphp

                @include('paginas.parceiros._form', [
                    'user' => $user,
                    'empresas' => $empresas,
                    'showEmpresaSelect' => auth()->user()->isSuperAdmin(),
                    'screensConfig' => $screensConfig,
                    'checkedScreens' => $checkedScreens,
                    'isEdit' => true,
                ])

                <div class="flex flex-wrap gap-3 border-t border-gray-100 pt-6">
                    <button
                        type="submit"
                        class="rounded-lg bg-amber-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:ring-offset-2"
                    >
                        Salvar
                    </button>
                    <a
                        href="{{ route('modulos.parceiros') }}"
                        class="rounded-lg border border-gray-300 bg-white px-5 py-2.5 text-sm font-semibold text-gray-800 shadow-sm transition hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-400 focus:ring-offset-2"
                    >
                        Voltar
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
