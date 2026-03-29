<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'PDV Sistema') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased text-gray-900" x-data="{ sidebarOpen: false }">
        <div class="flex min-h-screen bg-zinc-100">
            @include('layouts.pdv-sidebar')

            <div class="flex min-w-0 flex-1 flex-col lg:pl-0">
                <header class="sticky top-0 z-20 flex h-14 shrink-0 items-center justify-between border-b border-gray-200 bg-white px-4 shadow-sm sm:px-6">
                    <div class="flex items-center gap-3">
                        <button
                            type="button"
                            class="inline-flex rounded-lg p-2 text-gray-600 hover:bg-gray-100 lg:hidden"
                            @click="sidebarOpen = true"
                            aria-label="Abrir menu"
                        >
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                            </svg>
                        </button>
                        <span class="shrink-0 font-bold tracking-tight text-gray-900">{{ config('pdv.brand_name') }}</span>
                        @if (auth()->user()->isSuperAdmin() && isset($empresasSwitcher) && $empresasSwitcher->isNotEmpty())
                            <form method="post" action="{{ route('empresa.context') }}" class="flex min-w-0 flex-1 items-center gap-2">
                                @csrf
                                <label for="header_company_id" class="sr-only">Empresa</label>
                                <select
                                    id="header_company_id"
                                    name="company_id"
                                    onchange="this.form.submit()"
                                    class="max-w-[min(100%,220px)] rounded-lg border border-gray-300 bg-white py-1.5 pl-2 pr-8 text-sm font-semibold text-gray-800 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                                >
                                    @foreach ($empresasSwitcher as $ec)
                                        <option value="{{ $ec->id }}" @selected(\App\Support\CurrentCompany::id() === $ec->id)>{{ $ec->nome }}</option>
                                    @endforeach
                                </select>
                            </form>
                        @elseif (auth()->user()->isSuperAdmin())
                            <span class="truncate text-sm font-medium text-gray-600">{{ \App\Support\CurrentCompany::model()?->nome ?? 'Empresa' }}</span>
                        @endif
                    </div>
                    <div class="flex items-center gap-3">
                        @php
                            $u = Auth::user();
                            $avatar = $u->avatarUrl();
                        @endphp
                        @if ($avatar)
                            <img
                                src="{{ $avatar }}"
                                alt=""
                                class="h-9 w-9 shrink-0 rounded-full object-cover ring-1 ring-gray-200"
                                width="36"
                                height="36"
                                loading="lazy"
                            />
                        @else
                            <span
                                class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-slate-200 text-xs font-semibold text-slate-700"
                                aria-hidden="true"
                            >{{ strtoupper(\Illuminate\Support\Str::substr($u->name, 0, 2)) }}</span>
                        @endif
                        <span class="max-w-[200px] truncate text-sm font-medium text-gray-800 sm:max-w-none">{{ $u->name }}</span>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="text-sm font-medium text-red-600 hover:text-red-700">
                                Sair
                            </button>
                        </form>
                    </div>
                </header>

                @isset($header)
                    <div class="border-b border-gray-200 bg-white px-4 py-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                @endisset

                <main class="flex min-h-0 flex-1 flex-col">
                    {{ $slot }}
                </main>
            </div>
        </div>
    </body>
</html>
