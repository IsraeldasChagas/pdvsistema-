<x-app-layout>
    <div class="min-h-[calc(100vh-3.5rem)] bg-[#f8f9fa] px-4 py-8 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-7xl">
            <h1 class="text-3xl font-bold tracking-tight text-gray-900">Parceiros</h1>
            <p class="mt-1 text-sm text-gray-600">Vendedores de rua e cadastro completo (CPF/CNPJ, endereço) para entregas e acerto.</p>

            @if (session('status'))
                <p class="mt-4 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800" role="status">
                    {{ session('status') }}
                </p>
            @endif

            @error('delete')
                <p class="mt-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">{{ $message }}</p>
            @enderror

            <a
                href="{{ route('parceiros.create') }}"
                class="mt-4 inline-flex items-center justify-center rounded-lg bg-amber-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:ring-offset-2"
            >
                + Novo parceiro
            </a>

            <div class="mt-8 overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-left text-sm">
                        <thead class="bg-amber-50">
                            <tr>
                                <th scope="col" class="px-4 py-3 font-semibold text-gray-700 sm:px-6">Nome</th>
                                <th scope="col" class="px-4 py-3 font-semibold text-gray-700 sm:px-6">Documento</th>
                                <th scope="col" class="px-4 py-3 font-semibold text-gray-700 sm:px-6">Cidade</th>
                                @if (auth()->user()->isSuperAdmin())
                                    <th scope="col" class="px-4 py-3 font-semibold text-gray-700 sm:px-6">Empresa</th>
                                @endif
                                <th scope="col" class="px-4 py-3 font-semibold text-gray-700 sm:px-6">Status</th>
                                <th scope="col" class="px-4 py-3 font-semibold text-gray-700 sm:px-6">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            @forelse ($parceiros as $p)
                                <tr class="hover:bg-gray-50/80">
                                    <td class="px-4 py-3 font-medium text-gray-900 sm:px-6">
                                        <div class="flex items-center gap-3">
                                            @if ($p->avatar_path)
                                                <img
                                                    src="{{ $p->avatarUrl() }}"
                                                    alt=""
                                                    class="h-9 w-9 shrink-0 rounded-full border border-gray-200 object-cover bg-white"
                                                />
                                            @else
                                                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-amber-100 text-xs font-semibold uppercase text-amber-800">
                                                    {{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($p->name, 0, 1)) }}
                                                </span>
                                            @endif
                                            <div>
                                                <span class="whitespace-nowrap">{{ $p->name }}</span>
                                                <p class="text-xs text-gray-500">{{ $p->email }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-3 text-gray-700 sm:px-6">
                                        @if ($p->parceiro_tipo_documento)
                                            <span class="font-mono text-xs">{{ strtoupper($p->parceiro_tipo_documento) }}</span>
                                            <span class="block text-gray-600">{{ $p->parceiro_documento }}</span>
                                        @else
                                            <span class="text-amber-700">Em cadastro</span>
                                        @endif
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-3 text-gray-600 sm:px-6">
                                        {{ $p->endereco_cidade ?? '—' }}{{ $p->endereco_uf ? ' / '.$p->endereco_uf : '' }}
                                    </td>
                                    @if (auth()->user()->isSuperAdmin())
                                        <td class="whitespace-nowrap px-4 py-3 text-gray-600 sm:px-6">
                                            {{ $p->company?->nome ?? '—' }}
                                        </td>
                                    @endif
                                    <td class="whitespace-nowrap px-4 py-3 sm:px-6">
                                        @if ($p->is_active)
                                            <span class="inline-flex rounded-full bg-emerald-100 px-2.5 py-0.5 text-xs font-semibold text-emerald-800">Ativo</span>
                                        @else
                                            <span class="inline-flex rounded-full bg-gray-200 px-2.5 py-0.5 text-xs font-semibold text-gray-700">Inativo</span>
                                        @endif
                                        @if ($p->vendedor_rua)
                                            <span class="ml-1 inline-flex rounded-full bg-violet-100 px-2 py-0.5 text-xs font-medium text-violet-800">Rua</span>
                                        @endif
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-3 sm:px-6">
                                        <a
                                            href="{{ route('parceiros.edit', $p) }}"
                                            class="font-medium text-amber-700 hover:text-amber-900"
                                        >
                                            Editar
                                        </a>
                                        @if (! auth()->user()->is($p))
                                            <form
                                                action="{{ route('parceiros.destroy', $p) }}"
                                                method="post"
                                                class="ml-3 inline"
                                                onsubmit="return confirm('Remover este parceiro? Esta ação não pode ser desfeita.');"
                                            >
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="font-medium text-red-600 hover:text-red-800">Excluir</button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ auth()->user()->isSuperAdmin() ? 6 : 5 }}" class="px-4 py-12 text-center text-gray-500 sm:px-6">
                                        Nenhum parceiro cadastrado. Use &quot;Novo parceiro&quot; para incluir.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
