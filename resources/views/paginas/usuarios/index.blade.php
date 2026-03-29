<x-app-layout>
    <div class="min-h-[calc(100vh-3.5rem)] bg-[#f8f9fa] px-4 py-8 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-7xl">
            <h1 class="text-3xl font-bold tracking-tight text-gray-900">Usuários</h1>

            @if (session('status'))
                <p class="mt-4 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800" role="status">
                    {{ session('status') }}
                </p>
            @endif

            @error('delete')
                <p class="mt-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">{{ $message }}</p>
            @enderror

            <a
                href="{{ route('usuarios.create') }}"
                class="mt-4 inline-flex items-center justify-center rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
            >
                + Novo Usuário
            </a>

            <div class="mt-8 overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-left text-sm">
                        <thead class="bg-slate-100">
                            <tr>
                                <th scope="col" class="px-4 py-3 font-semibold text-gray-700 sm:px-6">Nome</th>
                                <th scope="col" class="px-4 py-3 font-semibold text-gray-700 sm:px-6">E-mail</th>
                                @if (auth()->user()->isSuperAdmin())
                                    <th scope="col" class="px-4 py-3 font-semibold text-gray-700 sm:px-6">Empresa</th>
                                @endif
                                <th scope="col" class="px-4 py-3 font-semibold text-gray-700 sm:px-6">Cargo</th>
                                <th scope="col" class="px-4 py-3 font-semibold text-gray-700 sm:px-6">Status</th>
                                <th scope="col" class="px-4 py-3 font-semibold text-gray-700 sm:px-6">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            @forelse ($usuarios as $u)
                                <tr class="hover:bg-gray-50/80">
                                    <td class="px-4 py-3 font-medium text-gray-900 sm:px-6">
                                        <div class="flex items-center gap-3">
                                            @if ($u->avatar_path)
                                                <img
                                                    src="{{ $u->avatarUrl() }}"
                                                    alt=""
                                                    class="h-9 w-9 shrink-0 rounded-full border border-gray-200 object-cover bg-white"
                                                />
                                            @else
                                                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-slate-200 text-xs font-semibold uppercase text-slate-600">
                                                    {{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($u->name, 0, 1)) }}
                                                </span>
                                            @endif
                                            <span class="whitespace-nowrap">{{ $u->name }}</span>
                                        </div>
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-3 text-gray-700 sm:px-6">{{ $u->email }}</td>
                                    @if (auth()->user()->isSuperAdmin())
                                        <td class="whitespace-nowrap px-4 py-3 text-gray-600 sm:px-6">
                                            {{ $u->company?->nome ?? '—' }}
                                        </td>
                                    @endif
                                    <td class="whitespace-nowrap px-4 py-3 sm:px-6">
                                        @if ($u->role === 'super_admin')
                                            <span class="inline-flex rounded-full bg-rose-100 px-2.5 py-0.5 text-xs font-semibold text-rose-800">
                                                Super administrador
                                            </span>
                                        @elseif ($u->role === 'administrador')
                                            <span class="inline-flex rounded-full bg-violet-100 px-2.5 py-0.5 text-xs font-semibold text-violet-800">
                                                Administrador
                                            </span>
                                        @elseif ($u->role === 'gerente')
                                            <span class="inline-flex rounded-full bg-blue-100 px-2.5 py-0.5 text-xs font-semibold text-blue-800">
                                                Gerente
                                            </span>
                                        @else
                                            <span class="inline-flex rounded-full bg-sky-100 px-2.5 py-0.5 text-xs font-semibold text-sky-800">
                                                Vendedor
                                            </span>
                                        @endif
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-3 sm:px-6">
                                        @if ($u->is_active)
                                            <span class="inline-flex rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-semibold text-green-800">
                                                Ativo
                                            </span>
                                        @else
                                            <span class="inline-flex rounded-full bg-gray-200 px-2.5 py-0.5 text-xs font-semibold text-gray-700">
                                                Inativo
                                            </span>
                                        @endif
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-3 sm:px-6">
                                        <span class="flex flex-wrap items-center gap-x-2 text-sm">
                                            <a href="{{ route('usuarios.edit', $u) }}" class="font-semibold text-blue-600 hover:text-blue-700 hover:underline">Editar</a>
                                            @if (! auth()->user()->is($u))
                                                <span class="text-gray-300" aria-hidden="true">|</span>
                                                <form
                                                    action="{{ route('usuarios.destroy', $u) }}"
                                                    method="post"
                                                    class="inline"
                                                    onsubmit="return confirm('Excluir este usuário?');"
                                                >
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="font-semibold text-red-600 hover:text-red-700 hover:underline">
                                                        Excluir
                                                    </button>
                                                </form>
                                            @endif
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ auth()->user()->isSuperAdmin() ? 6 : 5 }}" class="px-4 py-8 text-center text-gray-500 sm:px-6">Nenhum usuário cadastrado.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
