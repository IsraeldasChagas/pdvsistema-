@php
    $isSuper = auth()->user()->isSuperAdmin();
    $brandName = config('pdv.brand_name', 'PDV Sistema');
    $barraTitulo = ! empty($empresaNomeOperacao) ? $empresaNomeOperacao : $brandName;
    $barraIniciais = strtoupper(\Illuminate\Support\Str::substr($barraTitulo, 0, 2));

    if ($isSuper) {
        $logoUrl = $pdvSetting?->logoPublicUrl();
        $financeiroSaasOpen = request()->routeIs('financeiro.saas.*');
    } else {
        $caixaAberto = auth()->check()
            && \App\Models\CashRegisterSession::query()
                ->where('user_id', auth()->id())
                ->whereNull('closed_at')
                ->exists();

        $pdvHint = $caixaAberto ? 'Caixa aberto' : 'Abra seu caixa';

        $items = [
            ['route' => 'dashboard', 'label' => 'Dashboard', 'icon' => 'home', 'routes' => ['dashboard'], 'hint' => null],
            ['route' => 'modulos.produtos', 'label' => 'Produtos', 'icon' => 'box', 'routes' => ['modulos.produtos'], 'hint' => null],
            ['route' => 'modulos.categorias', 'label' => 'Categorias', 'icon' => 'grid', 'routes' => ['modulos.categorias'], 'hint' => null],
            ['route' => 'modulos.estoque', 'label' => 'Estoque', 'icon' => 'stock', 'routes' => ['modulos.estoque', 'estoque.*'], 'hint' => null],
            ['route' => 'modulos.entradas', 'label' => 'Entradas', 'icon' => 'in', 'routes' => ['modulos.entradas'], 'hint' => null],
            ['route' => 'modulos.saidas', 'label' => 'Saídas', 'icon' => 'out', 'routes' => ['modulos.saidas'], 'hint' => null],
            ['route' => 'modulos.entregas', 'label' => 'Entregas', 'icon' => 'truck', 'routes' => ['modulos.entregas'], 'hint' => null],
            ['route' => 'modulos.venda', 'label' => 'Mini PDV', 'icon' => 'pdv', 'hint' => $pdvHint, 'routes' => ['modulos.venda']],
            ['route' => 'modulos.caixa', 'label' => 'Caixa', 'icon' => 'cash', 'routes' => ['modulos.caixa'], 'hint' => null],
            ['route' => 'modulos.comissoes', 'label' => 'Comissões', 'icon' => 'coin', 'routes' => ['modulos.comissoes', 'comissoes.*'], 'hint' => null],
            [
                'label' => 'Financeiro',
                'icon' => 'card',
                'routes' => ['financeiro.*'],
                'children' => [
                    ['route' => 'financeiro.fluxo_caixa', 'label' => 'Fluxo de caixa', 'icon' => 'chart', 'routes' => ['financeiro.fluxo_caixa']],
                    ['route' => 'financeiro.despesas_fixas', 'label' => 'Despesas Fixas', 'icon' => 'clipboard-plan', 'routes' => ['financeiro.despesas_fixas']],
                    ['route' => 'financeiro.despesas_variaveis', 'label' => 'Despesas Variáveis', 'icon' => 'coin', 'routes' => ['financeiro.despesas_variaveis']],
                ],
            ],
            ['route' => 'modulos.relatorios', 'label' => 'Relatórios', 'icon' => 'chart', 'routes' => ['modulos.relatorios'], 'hint' => null],
            ['route' => 'modulos.usuarios', 'label' => 'Usuários', 'icon' => 'users', 'routes' => ['modulos.usuarios', 'usuarios.*'], 'hint' => null],
            ['route' => 'modulos.configuracoes', 'label' => 'Configurações', 'icon' => 'settings', 'routes' => ['modulos.configuracoes'], 'hint' => null],
        ];
        $logoUrl = $pdvSetting?->logoPublicUrl();
    }
@endphp

<aside
    :class="[
        sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0',
        sidebarCollapsed ? 'lg:w-20' : 'lg:w-64'
    ]"
    class="fixed inset-y-0 left-0 z-40 flex w-64 flex-col border-r border-slate-800 bg-[#0c1929] transition-transform duration-200 ease-out lg:static lg:transition-all lg:duration-200"
>
    <div class="flex min-h-16 shrink-0 flex-col justify-center gap-1 border-b border-slate-800 px-4 py-3" :class="sidebarCollapsed ? 'lg:px-2' : 'lg:px-4'">
        <div class="flex items-center gap-3">
            @if ($logoUrl)
                <div class="relative h-10 w-10 shrink-0">
                    <img
                        src="{{ $logoUrl }}"
                        alt=""
                        class="h-10 w-10 rounded-full object-cover ring-1 ring-slate-600"
                        loading="lazy"
                        decoding="async"
                        onerror="this.classList.add('hidden'); this.nextElementSibling?.classList.remove('hidden')"
                    />
                    <div
                        class="absolute inset-0 hidden inline-flex items-center justify-center rounded-full bg-emerald-500 text-sm font-bold text-white ring-1 ring-slate-600"
                        data-logo-fallback
                        aria-hidden="true"
                    >
                        {{ $barraIniciais }}
                    </div>
                </div>
            @else
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-emerald-500 text-sm font-bold text-white">
                    {{ $barraIniciais }}
                </div>
            @endif
            <div class="min-w-0 flex-1 sidebar-label" x-show="!sidebarCollapsed" x-cloak>
                <span class="block truncate text-lg font-semibold tracking-tight text-white" title="{{ $barraTitulo }}">{{ $barraTitulo }}</span>
            </div>
            <button
                type="button"
                class="hidden rounded-md border border-slate-600 bg-slate-800 px-2 py-1 text-xs font-semibold text-white hover:bg-slate-700 lg:inline-flex"
                @click="sidebarCollapsed = !sidebarCollapsed; localStorage.setItem('pdv.sidebarCollapsed', sidebarCollapsed ? '1' : '0')"
                x-text="sidebarCollapsed ? 'Expandir' : 'Recolher'"
                :aria-label="sidebarCollapsed ? 'Expandir menu lateral' : 'Recolher menu lateral'"
                :title="sidebarCollapsed ? 'Expandir menu lateral' : 'Recolher menu lateral'"
            ></button>
        </div>
    </div>

    <nav class="flex-1 space-y-0.5 overflow-y-auto px-2 py-4" :class="sidebarCollapsed ? 'lg:px-1' : 'lg:px-2'">
        @if ($isSuper)
            @php
                $empresasAtivo = request()->routeIs('empresas.*');
                $usuariosAtivo = request()->routeIs('modulos.usuarios', 'usuarios.*');
            @endphp
            <a
                href="{{ route('empresas.index') }}"
                @click="sidebarOpen = false"
                class="{{ $empresasAtivo ? 'border-l-4 border-blue-500 bg-blue-600 text-white' : 'border-l-4 border-transparent text-slate-300 hover:bg-slate-800/80 hover:text-white' }} group flex flex-col rounded-r-lg py-2.5 pl-3 pr-2 text-sm font-medium transition-colors"
                :class="sidebarCollapsed ? 'lg:items-center lg:pl-0 lg:pr-0' : ''"
            >
                <span class="flex items-center gap-3">
                    @include('layouts.partials.sidebar-icon', ['name' => 'building', 'active' => $empresasAtivo])
                    <span class="sidebar-label" x-show="!sidebarCollapsed" x-cloak>Empresas</span>
                </span>
            </a>
            <a
                href="{{ route('modulos.usuarios') }}"
                @click="sidebarOpen = false"
                class="{{ $usuariosAtivo ? 'border-l-4 border-blue-500 bg-blue-600 text-white' : 'border-l-4 border-transparent text-slate-300 hover:bg-slate-800/80 hover:text-white' }} group flex flex-col rounded-r-lg py-2.5 pl-3 pr-2 text-sm font-medium transition-colors"
                :class="sidebarCollapsed ? 'lg:items-center lg:pl-0 lg:pr-0' : ''"
            >
                <span class="flex items-center gap-3">
                    @include('layouts.partials.sidebar-icon', ['name' => 'users', 'active' => $usuariosAtivo])
                    <span class="sidebar-label" x-show="!sidebarCollapsed" x-cloak>Usuários</span>
                </span>
            </a>
            <div x-data="{ open: {{ $financeiroSaasOpen ? 'true' : 'false' }} }" class="space-y-0.5">
                @php
                    $finAtivo = request()->routeIs('financeiro.saas.*');
                    $finSub = [
                        ['route' => 'financeiro.saas.dashboard', 'label' => 'Dashboard', 'icon' => 'chart-bars-color', 'routes' => ['financeiro.saas.dashboard']],
                        ['route' => 'financeiro.saas.empresas', 'label' => 'Empresas', 'icon' => 'building', 'routes' => ['financeiro.saas.empresas', 'financeiro.saas.empresas.show']],
                        ['route' => 'financeiro.saas.planos', 'label' => 'Planos', 'icon' => 'clipboard-plan', 'routes' => ['financeiro.saas.planos', 'financeiro.saas.plans.create', 'financeiro.saas.plans.edit']],
                        ['route' => 'financeiro.saas.cobrancas', 'label' => 'Cobranças', 'icon' => 'document-invoice', 'routes' => ['financeiro.saas.cobrancas']],
                    ];
                @endphp
                <button
                    type="button"
                    @click="open = !open"
                    class="{{ $finAtivo ? 'border-l-4 border-blue-500 bg-blue-600 text-white' : 'border-l-4 border-transparent text-slate-300 hover:bg-slate-800/80 hover:text-white' }} group flex w-full items-center gap-2 rounded-r-lg py-2.5 pl-2 pr-2 text-left text-sm font-medium transition-colors"
                >
                    <svg
                        class="h-4 w-4 shrink-0 text-current transition-transform"
                        :class="open ? '' : '-rotate-90'"
                        fill="currentColor"
                        viewBox="0 0 24 24"
                        aria-hidden="true"
                        x-show="!sidebarCollapsed"
                        x-cloak
                    >
                        <path d="M12 16.5l-6-6h12l-6 6z" />
                    </svg>
                    <span class="flex min-w-0 flex-1 items-center gap-3">
                        @include('layouts.partials.sidebar-icon', ['name' => 'card', 'active' => $finAtivo])
                        <span class="truncate sidebar-label" x-show="!sidebarCollapsed" x-cloak>Financeiro SaaS</span>
                    </span>
                </button>
                <div x-show="open && !sidebarCollapsed" x-cloak class="mt-0.5 ml-3 space-y-0.5 border-l border-slate-700/80 pl-2">
                    @foreach ($finSub as $sub)
                        @php
                            $subActive = collect($sub['routes'])->contains(fn ($r) => request()->routeIs($r));
                        @endphp
                        <a
                            href="{{ route($sub['route']) }}"
                            @click="sidebarOpen = false"
                            class="{{ $subActive ? 'border-l-4 border-blue-500 bg-blue-600 text-white' : 'border-l-4 border-transparent text-slate-400 hover:bg-slate-800/70 hover:text-white' }} group flex items-center gap-3 rounded-r-lg py-2 pl-3 pr-2 text-sm font-medium transition-colors"
                        >
                            @include('layouts.partials.sidebar-icon', ['name' => $sub['icon'], 'active' => $subActive])
                            {{ $sub['label'] }}
                        </a>
                    @endforeach
                </div>
            </div>
        @else
            @foreach ($items as $item)
                @php
                    $active = collect($item['routes'])->contains(fn ($r) => request()->routeIs($r));
                @endphp
                @if (isset($item['children']))
                    <div x-data="{ open: {{ $active ? 'true' : 'false' }} }" class="space-y-0.5">
                        <button
                            type="button"
                            @click="open = !open"
                            class="{{ $active ? 'border-l-4 border-blue-500 bg-blue-600 text-white' : 'border-l-4 border-transparent text-slate-300 hover:bg-slate-800/80 hover:text-white' }} group flex w-full items-center gap-2 rounded-r-lg py-2.5 pl-2 pr-2 text-left text-sm font-medium transition-colors"
                            :class="sidebarCollapsed ? 'lg:justify-center lg:pl-0 lg:pr-0' : ''"
                        >
                            <svg
                                class="h-4 w-4 shrink-0 text-current transition-transform"
                                :class="open ? '' : '-rotate-90'"
                                fill="currentColor"
                                viewBox="0 0 24 24"
                                aria-hidden="true"
                                x-show="!sidebarCollapsed"
                                x-cloak
                            >
                                <path d="M12 16.5l-6-6h12l-6 6z" />
                            </svg>
                            <span class="flex min-w-0 flex-1 items-center gap-3">
                                @include('layouts.partials.sidebar-icon', ['name' => $item['icon'], 'active' => $active])
                                <span class="truncate sidebar-label" x-show="!sidebarCollapsed" x-cloak>{{ $item['label'] }}</span>
                            </span>
                        </button>
                        <div x-show="open && !sidebarCollapsed" x-cloak class="mt-0.5 ml-3 space-y-0.5 border-l border-slate-700/80 pl-2">
                            @foreach ($item['children'] as $sub)
                                @php
                                    $subActive = collect($sub['routes'])->contains(fn ($r) => request()->routeIs($r));
                                @endphp
                                <a
                                    href="{{ route($sub['route']) }}"
                                    @click="sidebarOpen = false"
                                    class="{{ $subActive ? 'border-l-4 border-blue-500 bg-blue-600 text-white' : 'border-l-4 border-transparent text-slate-400 hover:bg-slate-800/70 hover:text-white' }} group flex items-center gap-3 rounded-r-lg py-2 pl-3 pr-2 text-sm font-medium transition-colors"
                                >
                                    @include('layouts.partials.sidebar-icon', ['name' => $sub['icon'], 'active' => $subActive])
                                    {{ $sub['label'] }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                @else
                    <a
                        href="{{ route($item['route']) }}"
                        @click="sidebarOpen = false"
                        class="{{ $active ? 'border-l-4 border-blue-500 bg-blue-600 text-white' : 'border-l-4 border-transparent text-slate-300 hover:bg-slate-800/80 hover:text-white' }} group flex flex-col rounded-r-lg py-2.5 pl-3 pr-2 text-sm font-medium transition-colors"
                        :class="sidebarCollapsed ? 'lg:items-center lg:pl-0 lg:pr-0' : ''"
                    >
                        <span class="flex items-center gap-3">
                            @include('layouts.partials.sidebar-icon', ['name' => $item['icon'], 'active' => $active])
                            <span class="sidebar-label" x-show="!sidebarCollapsed" x-cloak>{{ $item['label'] }}</span>
                        </span>
                        @if (! empty($item['hint']))
                            <span class="sidebar-label mt-0.5 pl-8 text-xs font-normal text-amber-300/90" x-show="!sidebarCollapsed" x-cloak>{{ $item['hint'] }}</span>
                        @endif
                    </a>
                @endif
            @endforeach
        @endif
    </nav>
</aside>

<div
    x-show="sidebarOpen"
    x-transition.opacity
    @click="sidebarOpen = false"
    class="fixed inset-0 z-30 bg-black/50 lg:hidden"
    x-cloak
></div>
