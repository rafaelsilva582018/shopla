@php
    $planTone = [
        'free' => 'bg-slate-100 text-slate-700 ring-slate-200',
        'plus' => 'bg-pink-100 text-pink-700 ring-pink-200',
        'pro' => 'bg-violet-100 text-violet-700 ring-violet-200',
        'premium' => 'bg-amber-100 text-amber-800 ring-amber-200',
        'enterprise' => 'bg-slate-950 text-white ring-slate-950',
    ];
@endphp

<x-admin.layout title="Usuarios admin">
    <section class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <p class="text-xs font-black uppercase tracking-[0.22em] text-pink-500">usuarios</p>
            <h1 class="mt-2 text-3xl font-black tracking-tight text-slate-950">Contas do sistema</h1>
            <p class="mt-2 max-w-2xl text-sm font-semibold leading-6 text-slate-500">
                Busque clientes, veja lojas vinculadas e altere planos quando precisar liberar acesso manualmente.
            </p>
        </div>

        <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center justify-center gap-2 rounded-2xl bg-slate-950 px-5 py-3 text-sm font-black text-white">
            <x-dashboard-icon name="trend" class="h-5 w-5" />
            Voltar ao dashboard
        </a>
    </section>

    <section class="mt-6 rounded-[2rem] border border-slate-200 bg-white p-5 shadow-sm">
        <form method="GET" action="{{ route('admin.users.index') }}" class="grid gap-3 lg:grid-cols-[1fr_260px_auto] lg:items-end">
            <label class="block">
                <span class="mb-2 block text-sm font-black text-slate-700">Buscar usuario, e-mail ou loja</span>
                <input
                    name="search"
                    value="{{ $search }}"
                    placeholder="Ex: Rafael, shopla@email.com, minha-loja"
                    class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-semibold text-slate-700 outline-none transition focus:border-pink-400 focus:bg-white focus:ring-4 focus:ring-pink-100"
                >
            </label>

            <label class="block">
                <span class="mb-2 block text-sm font-black text-slate-700">Filtrar por plano</span>
                <select
                    name="plan"
                    class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-bold text-slate-700 outline-none transition focus:border-pink-400 focus:bg-white focus:ring-4 focus:ring-pink-100"
                >
                    <option value="">Todos os planos</option>
                    @foreach($plans as $key => $planConfig)
                        <option value="{{ $key }}" @selected($plan === $key)>{{ $planConfig['name'] }}</option>
                    @endforeach
                </select>
            </label>

            <div class="flex gap-2">
                <button class="inline-flex h-12 flex-1 items-center justify-center gap-2 rounded-2xl bg-pink-600 px-5 text-sm font-black text-white shadow-lg shadow-pink-600/20 lg:flex-none">
                    <x-dashboard-icon name="settings" class="h-5 w-5" />
                    Filtrar
                </button>

                <a href="{{ route('admin.users.index') }}" class="inline-flex h-12 items-center justify-center rounded-2xl border border-slate-200 px-5 text-sm font-black text-slate-600">
                    Limpar
                </a>
            </div>
        </form>
    </section>

    <section class="mt-6 overflow-hidden rounded-[2rem] border border-slate-200 bg-white shadow-sm">
        <div class="hidden grid-cols-[1.2fr_1fr_0.65fr_0.7fr_0.65fr] bg-slate-50 px-5 py-4 text-xs font-black uppercase tracking-[0.14em] text-slate-400 lg:grid">
            <span>Conta</span>
            <span>Loja</span>
            <span>Plano</span>
            <span>Uso</span>
            <span class="text-right">Cadastro</span>
        </div>

        @forelse($users as $user)
            @php
                $planKey = $user->plan ?: 'free';
                $planConfig = $plans[$planKey] ?? $plans['free'];
            @endphp

            <a href="{{ route('admin.users.show', $user) }}" class="grid gap-4 border-t border-slate-100 px-5 py-5 transition hover:bg-pink-50/30 lg:grid-cols-[1.2fr_1fr_0.65fr_0.7fr_0.65fr] lg:items-center">
                <span class="flex min-w-0 items-center gap-3">
                    <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-slate-950 text-lg font-black text-white">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </span>

                    <span class="min-w-0">
                        <span class="block truncate font-black text-slate-950">{{ trim($user->name . ' ' . ($user->last_name ?? '')) }}</span>
                        <span class="block truncate text-sm font-semibold text-slate-400">{{ $user->email }}</span>
                    </span>
                </span>

                <span>
                    <span class="block font-black text-slate-800">{{ $user->store?->name ?? 'Sem loja criada' }}</span>
                    <span class="block text-sm font-semibold text-slate-400">
                        {{ $user->store?->slug ? '/' . $user->store->slug : 'Sem link' }}
                    </span>
                </span>

                <span>
                    <span class="inline-flex rounded-full px-3 py-1 text-xs font-black ring-1 {{ $planTone[$planKey] ?? 'bg-slate-100 text-slate-700 ring-slate-200' }}">
                        {{ $planConfig['name'] }}
                    </span>
                </span>

                <span class="text-sm font-bold text-slate-500">
                    {{ $user->store?->products_count ?? 0 }} produtos<br>
                    {{ $user->store?->orders_count ?? 0 }} pedidos
                </span>

                <span class="text-sm font-bold text-slate-400 lg:text-right">
                    {{ $user->created_at?->format('d/m/Y') }}
                </span>
            </a>
        @empty
            <div class="border-t border-slate-100 px-5 py-12 text-center">
                <x-dashboard-icon name="store" class="mx-auto h-10 w-10 text-slate-300" />
                <p class="mt-3 font-black text-slate-700">Nenhum usuario encontrado</p>
                <p class="mt-1 text-sm font-semibold text-slate-400">Tente limpar os filtros ou buscar por outro termo.</p>
            </div>
        @endforelse
    </section>

    <div class="mt-6">
        {{ $users->links() }}
    </div>
</x-admin.layout>
