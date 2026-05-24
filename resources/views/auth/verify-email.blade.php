<x-guest-layout>
    <div class="text-center">
        <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-pink-100 text-pink-600">
            <x-dashboard-icon name="receipt" class="h-7 w-7" />
        </div>

        <h1 class="mt-5 text-2xl font-black text-slate-950">
            Confirme seu e-mail
        </h1>

        <p class="mt-3 text-sm leading-6 text-slate-600">
            Enviamos um link de confirmacao para o e-mail usado no cadastro. Clique nele para liberar o painel da sua loja.
        </p>
    </div>

    <div class="mt-6 rounded-2xl border border-pink-200 bg-pink-50 px-4 py-4 text-sm leading-6 text-pink-800">
        <div class="flex gap-3">
            <span class="mt-0.5 flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-white text-pink-600">
                <x-dashboard-icon name="alert" class="h-5 w-5" />
            </span>
            <div>
                <strong class="block text-slate-950">Nao encontrou o e-mail?</strong>
                Veja tambem em Spam, Lixo eletronico ou Promocoes. Se estiver la, marque como "Nao e spam" para receber os proximos avisos da Shopla normalmente.
            </div>
        </div>
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="mt-6 rounded-2xl border border-green-200 bg-green-50 px-4 py-3 text-sm font-semibold text-green-700">
            Enviamos um novo link de confirmacao para o seu e-mail.
        </div>
    @endif

    <div class="mt-7 space-y-3">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf

            <button type="submit" class="flex w-full items-center justify-center gap-2 rounded-2xl bg-pink-600 px-5 py-4 font-black text-white shadow-lg shadow-pink-200 transition hover:bg-pink-700">
                <x-dashboard-icon name="external" class="h-5 w-5" />
                Reenviar e-mail de confirmacao
            </button>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf

            <button type="submit" class="flex w-full items-center justify-center rounded-2xl border border-slate-200 bg-white px-5 py-4 font-bold text-slate-700 transition hover:bg-slate-50">
                Sair e usar outra conta
            </button>
        </form>
    </div>
</x-guest-layout>
