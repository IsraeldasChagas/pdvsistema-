<x-login-layout>
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <h1 class="text-2xl font-bold text-gray-900">{{ config('app.name', 'Sistema PDV') }}</h1>
    <p class="mt-1 text-sm text-gray-500">Entre com seu e-mail e senha</p>
    <p class="mt-3 text-xs leading-relaxed text-slate-600">
        <strong>Administrador de empresa nova?</strong> Use o <strong>mesmo e-mail e senha</strong> do campo “E-mail do admin” ao cadastrar a empresa (super admin). Não existe conta automática em <span class="font-mono">admin@pdvsistema.com</span> a menos que você tenha cadastrado esse e-mail.
    </p>
    @if (app()->environment('local'))
        <p class="mt-2 text-xs text-slate-500">
            Ambiente local com <span class="font-mono">php artisan db:seed</span>: tente <span class="font-mono">admin@sistema.pdv</span> ou <span class="font-mono">super@sistema.pdv</span>, senha <span class="font-mono">password</span>.
        </p>
    @endif

    <form method="POST" action="{{ route('login') }}" class="mt-8 space-y-5">
        @csrf

        <div>
            <x-input-label for="email" value="E-mail" class="text-gray-900" />
            <input
                id="email"
                type="email"
                name="email"
                value="{{ old('email', app()->environment('local') ? 'admin@sistema.pdv' : '') }}"
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
