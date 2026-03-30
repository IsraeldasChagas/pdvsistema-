<x-login-layout>
    <x-auth-session-status class="mb-4" :status="session('status')" />

    @php
        $logoImagem = null;
        $try = [
            'imagem/pdv.png', 'imagem/PDV.png', 'imagem/pdv.jpg', 'imagem/PDV.jpg', 'imagem/pdv.jpeg', 'imagem/pdv.webp', 'imagem/pdv.svg',
            'image/pdv.png', 'image/PDV.png', 'image/pdv.jpg', 'image/PDV.jpg', 'image/pdv.jpeg', 'image/pdv.webp', 'image/pdv.svg',
        ];
        foreach ($try as $rel) {
            if (is_file(public_path($rel))) {
                $logoImagem = $rel;
                break;
            }
        }
        if ($logoImagem === null) {
            foreach (['imagem', 'image'] as $sub) {
                $dir = public_path($sub);
                if (! is_dir($dir)) {
                    continue;
                }
                foreach (scandir($dir) ?: [] as $f) {
                    if ($f === '.' || $f === '..') {
                        continue;
                    }
                    if (preg_match('/^pdv\.(png|jpe?g|webp|svg)$/i', $f)) {
                        $logoImagem = $sub.'/'.$f;
                        break 2;
                    }
                }
            }
        }
    @endphp

    @if ($logoImagem !== null)
        <div class="mb-2 flex justify-center">
            <img
                src="{{ asset($logoImagem) }}?v={{ (string) filemtime(public_path($logoImagem)) }}"
                alt="{{ config('app.name', 'PDV') }}"
                class="h-auto max-h-40 w-auto max-w-full object-contain"
                loading="eager"
                decoding="async"
            />
        </div>
    @endif

    <p class="mb-3 text-center text-sm text-gray-500">Entre com seu e-mail e senha</p>

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
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
