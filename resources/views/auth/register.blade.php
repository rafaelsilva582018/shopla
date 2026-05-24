<x-guest-layout>
    <div class="mb-8">
        <p class="text-sm font-semibold tracking-widest uppercase text-pink-500">Criar conta</p>
        <h2 class="text-3xl md:text-4xl font-bold mt-2" style="font-family: serif;">
            Comece sua loja
        </h2>
        <p class="text-sm text-pink-900/60 mt-2">
            Esses dados ajudam a personalizar seu painel desde o primeiro acesso.
        </p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-5">
        @csrf

        <div class="grid md:grid-cols-2 gap-4">
            <div>
                <label for="name" class="block text-sm font-semibold mb-2">Nome</label>
                <input id="name" class="w-full rounded-2xl border border-pink-100 bg-pink-50/40 px-4 py-3 focus:border-pink-400 focus:ring-pink-200" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="given-name">
                <x-input-error :messages="$errors->get('name')" class="mt-2" />
            </div>

            <div>
                <label for="last_name" class="block text-sm font-semibold mb-2">Sobrenome</label>
                <input id="last_name" class="w-full rounded-2xl border border-pink-100 bg-pink-50/40 px-4 py-3 focus:border-pink-400 focus:ring-pink-200" type="text" name="last_name" value="{{ old('last_name') }}" autocomplete="family-name">
                <x-input-error :messages="$errors->get('last_name')" class="mt-2" />
            </div>
        </div>

        <div class="grid md:grid-cols-2 gap-4">
            <div>
                <label for="email" class="block text-sm font-semibold mb-2">E-mail</label>
                <input id="email" class="w-full rounded-2xl border border-pink-100 bg-pink-50/40 px-4 py-3 focus:border-pink-400 focus:ring-pink-200" type="email" name="email" value="{{ old('email') }}" required autocomplete="username">
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <div>
                <label for="phone" class="block text-sm font-semibold mb-2">WhatsApp</label>
                <input id="phone" class="w-full rounded-2xl border border-pink-100 bg-pink-50/40 px-4 py-3 focus:border-pink-400 focus:ring-pink-200" type="text" name="phone" value="{{ old('phone') }}" placeholder="(00) 00000-0000" autocomplete="tel">
                <x-input-error :messages="$errors->get('phone')" class="mt-2" />
            </div>
        </div>

        <div class="grid md:grid-cols-[1fr_110px] gap-4">
            <div>
                <label for="city" class="block text-sm font-semibold mb-2">Cidade</label>
                <input id="city" class="w-full rounded-2xl border border-pink-100 bg-pink-50/40 px-4 py-3 focus:border-pink-400 focus:ring-pink-200" type="text" name="city" value="{{ old('city') }}" placeholder="Ex: Guararapes">
                <x-input-error :messages="$errors->get('city')" class="mt-2" />
            </div>

            <div>
                <label for="state" class="block text-sm font-semibold mb-2">UF</label>
                <input id="state" class="w-full rounded-2xl border border-pink-100 bg-pink-50/40 px-4 py-3 uppercase focus:border-pink-400 focus:ring-pink-200" type="text" name="state" value="{{ old('state') }}" maxlength="2" placeholder="SP">
                <x-input-error :messages="$errors->get('state')" class="mt-2" />
            </div>
        </div>

        <div class="grid md:grid-cols-2 gap-4">
            <div>
                <label for="password" class="block text-sm font-semibold mb-2">Senha</label>
                <input id="password" class="w-full rounded-2xl border border-pink-100 bg-pink-50/40 px-4 py-3 focus:border-pink-400 focus:ring-pink-200" type="password" name="password" required autocomplete="new-password">
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <div>
                <label for="password_confirmation" class="block text-sm font-semibold mb-2">Confirmar senha</label>
                <input id="password_confirmation" class="w-full rounded-2xl border border-pink-100 bg-pink-50/40 px-4 py-3 focus:border-pink-400 focus:ring-pink-200" type="password" name="password_confirmation" required autocomplete="new-password">
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
            </div>
        </div>

        <button class="w-full rounded-2xl bg-pink-500 px-5 py-4 text-white font-bold shadow-sm">
            Criar minha conta
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
            Continuar com Google
        </a>

        <p class="text-center text-sm text-pink-900/60">
            Já tem conta?
            <a href="{{ route('login') }}" class="font-bold text-pink-600">
                Entrar no painel
            </a>
        </p>
    </form>
</x-guest-layout>
