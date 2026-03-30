<x-login-layout>
    <x-auth-session-status class="mb-4" :status="session('status')" />

    @php
        $logoImagem = null;
        $dir = public_path('imagem');
        foreach (['pdv.png', 'pdv.jpg', 'pdv.jpeg', 'pdv.webp', 'pdv.svg'] as $f) {
            if (is_file($dir . DIRECTORY_SEPARATOR . $f)) {
                $logoImagem = 'imagem/' . $f;
                break;
            }
        }
    @endphp

    @if ($logoImagem !== null)
        <div class="mb-8 flex justify-center">
            <img
                src="{{ asset($logoImagem) }}"
                alt="{{ config('app.name', 'PDV') }}"
                class="h-auto max-h-36 w-auto max-w-full object-contain"
                width="280"
                height="120"
                loading="eager"
            />
        </div>
    @endif

    <h1 class="text-center text-2xl font-bold text-gray-900">{{ config('app.name', 'Sistema PDV') }}</h1>
    <p class="mt-1 text-center text-sm text-gray-500">Entre com seu e-mail e senha</p>

    <form method="POST" action="{{ route('login') }}" class="mt-8 space-y-5">
        @csrf

        <div>
            <x-input-label for="email" value="E-mail" class="text-gray-900" />
            <input
                id="email"
                type="email"
                name="email"
                value="{{ old('email') }}"
                required
                autofocus
                autocomplete="username"
                placeholder="seu@email.com"
                class="mt-1 block w-full rounded-lg border-0 bg-slate-100 px-3 py-2.5 text-gray-900 shadow-sm ring-1 ring-inset ring-slate-200 placeholder:text-slate-400 focus:bg-white focus:ring-2 focus:ring-blue-600"
            />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="password" value="Senha" class="text-gray-900" />
            <input
                id="password"
                type="password"
                name="password"
                required
                autocomplete="current-password"
                class="mt-1 block w-full rounded-lg border-0 bg-slate-100 px-3 py-2.5 text-gray-900 shadow-sm ring-1 ring-inset ring-slate-200 focus:bg-white focus:ring-2 focus:ring-blue-600"
            />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <button
            type="submit"
            class="flex w-full justify-center rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:ring-offset-2"
        >
            Entrar
        </button>
    </form>

    <p class="mt-8 text-center text-sm text-gray-600">
        Não tem conta?
        @if (Route::has('register'))
            <a href="{{ route('register') }}" class="font-medium text-blue-600 hover:text-blue-500">Cadastrar empresa</a>
        @endif
    </p>

    @if (Route::has('password.request'))
        <p class="mt-3 text-center text-xs text-gray-500">
            <a href="{{ route('password.request') }}" class="text-gray-500 underline hover:text-gray-700">Esqueceu a senha?</a>
        </p>
    @endif
</x-login-layout>
