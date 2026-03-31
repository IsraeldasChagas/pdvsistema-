<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        @include('layouts.partials.favicon')

        <title>
            @if (! empty($empresaNomeOperacao))
                {{ $empresaNomeOperacao }}
            @else
                {{ config('app.name', 'PDV Sistema') }}
            @endif
        </title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body
        class="font-sans antialiased text-gray-900"
        x-data="{ sidebarOpen: false, sidebarCollapsed: false }"
        x-init="sidebarCollapsed = window.innerWidth >= 1024 && localStorage.getItem('pdv.sidebarCollapsed') === '1'"
    >
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
                            <button type="submit" class="btn-pdv-ghost btn-pdv-ghost-red px-3 py-1.5 text-sm">
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
