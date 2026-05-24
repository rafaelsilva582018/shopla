<x-guest-layout>
    <div class="mb-8">
        <p class="text-sm font-semibold tracking-widest uppercase text-pink-500">Entrar</p>
        <h2 class="text-3xl md:text-4xl font-bold mt-2" style="font-family: serif;">
            Bem-vindo de volta
        </h2>
        <p class="text-sm text-pink-900/60 mt-2">
            Acesse seu painel para acompanhar pedidos, produtos e vendas.
        </p>
    </div>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        <div>
            <label for="email" class="block text-sm font-semibold mb-2">E-mail</label>
            <input id="email" class="w-full rounded-2xl border border-pink-100 bg-pink-50/40 px-4 py-3 focus:border-pink-400 focus:ring-pink-200" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username">
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div>
            <label for="password" class="block text-sm font-semibold mb-2">Senha</label>
            <input id="password" class="w-full rounded-2xl border border-pink-100 bg-pink-50/40 px-4 py-3 focus:border-pink-400 focus:ring-pink-200" type="password" name="password" required autocomplete="current-password">
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-pink-200 text-pink-500 shadow-sm focus:ring-pink-300" name="remember">
                <span class="ms-2 text-sm text-pink-900/70">Manter conectado</span>
            </label>

            @if (Route::has('password.request'))
                <a class="text-sm font-semibold text-pink-600" href="{{ route('password.request') }}">
                    Esqueci minha senha
                </a>
            @endif
        </div>

        <button class="w-full rounded-2xl bg-pink-500 px-5 py-4 text-white font-bold shadow-sm">
            Entrar no painel
        </button>

        <div class="flex items-center gap-3 text-xs font-semibold uppercase tracking-widest text-pink-900/40">
            <span class="h-px flex-1 bg-pink-100"></span>
            ou
            <span class="h-px flex-1 bg-pink-100"></span>
        </div>

        <a
            href="{{ route('auth.google.redirect') }}"
            class="flex w-full items-center justify-center gap-3 rounded-2xl border border-pink-100 bg-white px-5 py-4 text-sm font-bold text-pink-950 shadow-sm transition hover:border-pink-200 hover:bg-pink-50 focus:outline-none focus:ring-4 focus:ring-pink-100"
        >
            <svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" aria-hidden="true">
                <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                <path fill="#FBBC05" d="M5.84 14.1c-.22-.66-.35-1.36-.35-2.1s.13-1.44.35-2.1V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l3.66-2.84z"/>
                <path fill="#EA4335" d="M12 5.38c1.62 0 3.06 0.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06L5.84 9.9C6.71 7.3 9.14 5.38 12 5.38z"/>
            </svg>
            Entrar com Google
        </a>

        <p class="text-center text-sm text-pink-900/60">
            Ainda não tem conta?
            <a href="{{ route('register') }}" class="font-bold text-pink-600">
                Criar conta
            </a>
        </p>
    </form>
</x-guest-layout>
