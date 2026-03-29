<x-app-layout>
    @php
        $comissaoField =
            old('comissao_pct') !== null && old('comissao_pct') !== ''
                ? number_format((float) old('comissao_pct'), 2, ',', '.')
                : $comissaoPctDisplay;
    @endphp
    <div class="min-h-[calc(100vh-3.5rem)] bg-[#f8f9fa] px-4 py-8 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-2xl">
            <div class="flex gap-3">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center text-violet-600" aria-hidden="true">
                    <svg class="h-9 w-9" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 010 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.281z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </div>
                <div>
                    <h1 class="text-2xl font-bold tracking-tight text-gray-900 sm:text-3xl">Configurações</h1>
                    <p class="mt-0.5 text-sm text-gray-500">Personalize o comportamento do sistema</p>
                </div>
            </div>

            @if (session('status'))
                <p class="mt-6 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800" role="status">
                    {{ session('status') }}
                </p>
            @endif

            @if ($errors->any())
                <div class="mt-6 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800" role="alert">
                    <p class="font-semibold">Não foi possível salvar.</p>
                    <ul class="mt-2 list-inside list-disc">
                        @foreach ($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @php
                $logoSrcTop = $settings->logoPublicUrl();
                $transparentPixel = 'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///ywAAAAAAQABAAACAUwAOw==';
            @endphp

            <section class="mt-8 overflow-hidden rounded-xl border border-emerald-200 bg-white shadow-sm ring-1 ring-emerald-100/80">
                <div class="flex gap-3 border-b border-emerald-50 bg-white px-5 py-4">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-slate-800 text-white shadow-sm ring-1 ring-slate-900/20" aria-hidden="true">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3A1.5 1.5 0 001.5 6v12a1.5 1.5 0 001.5 1.5z" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-gray-900">Logo da empresa</h2>
                        <p class="text-sm text-gray-600">Escolha a imagem e clique em <strong>Salvar logo</strong> (envio separado das demais configurações).</p>
                    </div>
                </div>
                <form action="{{ route('configuracoes.logo') }}" method="post" enctype="multipart/form-data" class="space-y-4 px-5 py-5">
                    @csrf
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-start">
                        <div class="relative h-16 w-16 shrink-0 overflow-hidden rounded-lg bg-white shadow-inner ring-1 ring-gray-300">
                            <img
                                id="pdv-logo-preview"
                                src="{{ $logoSrcTop ?: $transparentPixel }}"
                                alt=""
                                class="h-16 w-16 object-contain p-1 {{ $logoSrcTop ? '' : 'hidden' }}"
                                width="64"
                                height="64"
                                @if ($logoSrcTop) loading="lazy" @endif
                            />
                            <div
                                id="pdv-logo-placeholder"
                                class="{{ $logoSrcTop ? 'hidden' : 'flex' }} absolute inset-0 items-center justify-center bg-black"
                                title="Pré-visualização"
                            >
                                <span class="text-2xl font-bold text-emerald-400">{{ strtoupper(Str::substr($settings->displayName(), 0, 1)) }}</span>
                            </div>
                        </div>
                        <div class="min-w-0 flex-1 space-y-3">
                            <div class="flex flex-wrap items-center gap-3">
                                <label class="inline-flex cursor-pointer items-center justify-center rounded-lg border border-gray-300 bg-gray-100 px-4 py-2 text-sm font-medium text-gray-700 shadow-sm transition hover:bg-gray-200">
                                    <span>Escolher imagem</span>
                                    <input
                                        id="pdv-logo-input"
                                        type="file"
                                        name="logo"
                                        accept=".png,.jpg,.jpeg,.gif,.svg,.webp,image/*"
                                        class="sr-only"
                                    />
                                </label>
                                <button
                                    type="submit"
                                    class="rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-1"
                                >
                                    Salvar logo
                                </button>
                            </div>
                            <span id="pdv-logo-filename" class="block text-sm text-gray-600"></span>
                            <p class="text-xs text-gray-500">PNG, JPG, GIF, WebP ou SVG. Máximo 10&nbsp;MB.</p>
                            @error('logo')
                                <p class="text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </form>
            </section>

            <form action="{{ route('configuracoes.update') }}" method="post" class="mt-8 space-y-6">
                @csrf

                <section class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
                    <div class="flex gap-3 border-b border-gray-100 bg-white px-5 py-4">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-emerald-500 text-white shadow-sm">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-base font-bold text-gray-900">Vendas e Comissão</h2>
                            <p class="text-sm text-gray-500">Configurações de PDV e comissões automáticas</p>
                        </div>
                    </div>
                    <div class="px-5 py-5">
                        <label for="comissao_pct" class="block text-sm font-medium text-gray-700">Percentual de Comissão (%)</label>
                        <input
                            id="comissao_pct"
                            type="text"
                            name="comissao_pct"
                            value="{{ $comissaoField }}"
                            inputmode="decimal"
                            autocomplete="off"
                            class="mt-1.5 w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm text-gray-900 shadow-sm focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500 @error('comissao_pct') border-red-500 @enderror"
                        />
                        @error('comissao_pct')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-2 text-xs text-gray-500">Aplicado automaticamente em cada venda realizada no PDV. Use vírgula ou ponto para decimais (ex.: 5 ou 5,5).</p>
                    </div>
                </section>

                <section class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
                    <div class="flex gap-3 border-b border-gray-100 bg-white px-5 py-4">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-amber-600 text-white shadow-sm">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-base font-bold text-gray-900">Estoque</h2>
                            <p class="text-sm text-gray-500">Alertas e relatórios</p>
                        </div>
                    </div>
                    <div class="px-5 py-5">
                        <label for="estoque_min" class="block text-sm font-medium text-gray-700">Estoque Mínimo para alerta</label>
                        <input
                            id="estoque_min"
                            type="number"
                            name="estoque_min"
                            min="0"
                            max="999999"
                            value="{{ old('estoque_min', $settings->estoque_min) }}"
                            class="mt-1.5 w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm text-gray-900 shadow-sm focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500 @error('estoque_min') border-red-500 @enderror"
                        />
                        @error('estoque_min')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-2 text-xs text-gray-500">Produtos com estoque até este valor aparecem na aba Estoque dos relatórios (valor padrão do filtro).</p>
                    </div>
                </section>

                <section class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
                    <div class="flex gap-3 border-b border-gray-100 bg-white px-5 py-4">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-indigo-600 text-white shadow-sm">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-base font-bold text-gray-900">Formas de Pagamento</h2>
                            <p class="text-sm text-gray-500">Opções disponíveis no PDV e Caixa</p>
                        </div>
                    </div>
                    <div class="px-5 py-5">
                        <label for="formas_pagamento" class="block text-sm font-medium text-gray-700">Lista (separada por vírgula)</label>
                        <input
                            id="formas_pagamento"
                            type="text"
                            name="formas_pagamento"
                            value="{{ old('formas_pagamento', $settings->formasPagamentoCsv()) }}"
                            class="mt-1.5 w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm text-gray-900 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 @error('formas_pagamento') border-red-500 @enderror"
                        />
                        @error('formas_pagamento')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-2 text-xs text-gray-500">Alterar a lista muda as opções do Mini PDV; vendas antigas continuam com o código salvo na época.</p>
                    </div>
                </section>

                <section class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
                    <div class="flex gap-3 border-b border-gray-100 bg-white px-5 py-4">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-sky-500 text-white shadow-sm">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3.75h.008v.008H17.25v-.008zm0 3.75h.008v.008H17.25v-.008zm0 3.75h.008v.008H17.25v-.008z" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-base font-bold text-gray-900">Dados da Empresa</h2>
                            <p class="text-sm text-gray-500">Informações exibidas no sistema</p>
                        </div>
                    </div>
                    <div class="space-y-4 px-5 py-5">
                        <div>
                            <label for="empresa_nome" class="block text-sm font-medium text-gray-700">Nome</label>
                            <input
                                id="empresa_nome"
                                type="text"
                                name="empresa_nome"
                                value="{{ old('empresa_nome', $settings->empresa_nome) }}"
                                class="mt-1.5 w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm text-gray-900 shadow-sm focus:border-sky-500 focus:outline-none focus:ring-1 focus:ring-sky-500"
                            />
                        </div>
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div>
                                <label for="empresa_cnpj" class="block text-sm font-medium text-gray-700">CNPJ</label>
                                <input
                                    id="empresa_cnpj"
                                    type="text"
                                    name="empresa_cnpj"
                                    value="{{ old('empresa_cnpj', $settings->empresa_cnpj) }}"
                                    class="mt-1.5 w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm text-gray-900 shadow-sm focus:border-sky-500 focus:outline-none focus:ring-1 focus:ring-sky-500"
                                />
                            </div>
                            <div>
                                <label for="empresa_telefone" class="block text-sm font-medium text-gray-700">Telefone</label>
                                <input
                                    id="empresa_telefone"
                                    type="text"
                                    name="empresa_telefone"
                                    value="{{ old('empresa_telefone', $settings->empresa_telefone) }}"
                                    class="mt-1.5 w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm text-gray-900 shadow-sm focus:border-sky-500 focus:outline-none focus:ring-1 focus:ring-sky-500"
                                />
                            </div>
                        </div>
                        <div>
                            <label for="empresa_email" class="block text-sm font-medium text-gray-700">E-mail</label>
                            <input
                                id="empresa_email"
                                type="email"
                                name="empresa_email"
                                value="{{ old('empresa_email', $settings->empresa_email) }}"
                                class="mt-1.5 w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm text-gray-900 shadow-sm focus:border-sky-500 focus:outline-none focus:ring-1 focus:ring-sky-500"
                            />
                        </div>
                        <div>
                            <label for="empresa_endereco" class="block text-sm font-medium text-gray-700">Endereço</label>
                            <input
                                id="empresa_endereco"
                                type="text"
                                name="empresa_endereco"
                                value="{{ old('empresa_endereco', $settings->empresa_endereco) }}"
                                class="mt-1.5 w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm text-gray-900 shadow-sm focus:border-sky-500 focus:outline-none focus:ring-1 focus:ring-sky-500"
                            />
                        </div>
                        <div>
                            <label for="nome_loja" class="block text-sm font-medium text-gray-700">Nome da Loja (opcional)</label>
                            <input
                                id="nome_loja"
                                type="text"
                                name="nome_loja"
                                value="{{ old('nome_loja', $settings->nome_loja) }}"
                                placeholder="Ex: Minha Loja"
                                class="mt-1.5 w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm text-gray-900 placeholder:text-gray-400 shadow-sm focus:border-sky-500 focus:outline-none focus:ring-1 focus:ring-sky-500"
                            />
                            <p class="mt-1 text-xs text-gray-500">Aparece no topo e no menu; se vazio, usa o nome da empresa ou o nome do app.</p>
                        </div>
                    </div>
                </section>

                <div class="rounded-lg border border-gray-200 bg-gray-50 px-4 py-3">
                    <label class="flex cursor-pointer items-start gap-2 text-sm text-gray-800">
                        <input
                            type="checkbox"
                            name="remover_logo"
                            value="1"
                            id="pdv-remover-logo"
                            class="mt-0.5 h-4 w-4 shrink-0 rounded border-gray-300 text-emerald-600 focus:ring-emerald-500"
                            @checked(old('remover_logo'))
                        />
                        <span>Remover logo atual (use junto com <strong>Salvar configurações</strong>)</span>
                    </label>
                </div>

                <div class="flex justify-end pt-2">
                    <button
                        type="submit"
                        class="rounded-lg bg-emerald-600 px-6 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2"
                    >
                        Salvar Configurações
                    </button>
                </div>
            </form>

            <div class="mt-6 rounded-xl border border-sky-200 bg-sky-50 px-4 py-3 text-sm text-gray-600">
                O percentual de comissão e as formas de pagamento passam a valer nas próximas vendas do Mini PDV. O estoque mínimo padrão é usado nos relatórios.
            </div>
        </div>
    </div>
    <script>
        (function () {
            var input = document.getElementById('pdv-logo-input');
            var img = document.getElementById('pdv-logo-preview');
            var ph = document.getElementById('pdv-logo-placeholder');
            var nameEl = document.getElementById('pdv-logo-filename');
            var remover = document.getElementById('pdv-remover-logo');
            if (!input || !img || !ph) {
                return;
            }
            input.addEventListener('change', function () {
                var f = this.files && this.files[0];
                if (remover) {
                    remover.checked = false;
                }
                if (!f) {
                    return;
                }
                if (nameEl) {
                    nameEl.textContent = f.name;
                }
                var prev = img.getAttribute('data-blob-url');
                if (prev) {
                    URL.revokeObjectURL(prev);
                }
                var url = URL.createObjectURL(f);
                img.setAttribute('data-blob-url', url);
                img.src = url;
                img.classList.remove('hidden');
                ph.classList.add('hidden');
            });
            if (remover) {
                remover.addEventListener('change', function () {
                    if (!this.checked) {
                        return;
                    }
                    var prev = img.getAttribute('data-blob-url');
                    if (prev) {
                        URL.revokeObjectURL(prev);
                        img.removeAttribute('data-blob-url');
                    }
                    input.value = '';
                    if (nameEl) {
                        nameEl.textContent = '';
                    }
                    img.classList.add('hidden');
                    ph.classList.remove('hidden');
                });
            }
        })();
    </script>
</x-app-layout>
