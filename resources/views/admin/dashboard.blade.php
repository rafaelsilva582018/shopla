@php
    $money = fn ($value) => 'R$ ' . number_format((float) $value, 2, ',', '.');
    $planTone = [
        'free' => 'bg-slate-100 text-slate-700 ring-slate-200',
        'plus' => 'bg-pink-100 text-pink-700 ring-pink-200',
        'pro' => 'bg-violet-100 text-violet-700 ring-violet-200',
        'premium' => 'bg-amber-100 text-amber-800 ring-amber-200',
        'enterprise' => 'bg-slate-950 text-white ring-slate-950',
    ];

    $cards = [
        ['label' => 'Usuarios cadastrados', 'value' => $metrics['users'], 'hint' => '+' . $metrics['new_users'] . ' este mes', 'icon' => 'store', 'tone' => 'bg-pink-500'],
        ['label' => 'Planos pagos', 'value' => $metrics['paid_users'], 'hint' => $metrics['active_subscriptions'] . ' assinaturas ativas', 'icon' => 'diamond', 'tone' => 'bg-violet-500'],
        ['label' => 'Lojas criadas', 'value' => $metrics['stores'], 'hint' => '+' . $metrics['new_stores'] . ' este mes', 'icon' => 'store', 'tone' => 'bg-emerald-500'],
        ['label' => 'Produtos', 'value' => $metrics['products'], 'hint' => $metrics['stores_without_products'] . ' lojas sem produto', 'icon' => 'package', 'tone' => 'bg-sky-500'],
        ['label' => 'Pedidos', 'value' => $metrics['orders'], 'hint' => $money($metrics['monthly_revenue']) . ' no mes', 'icon' => 'orders', 'tone' => 'bg-orange-500'],
        ['label' => 'Pendencias', 'value' => $metrics['pending_subscriptions'], 'hint' => 'checkouts aguardando', 'icon' => 'alert', 'tone' => 'bg-red-500'],
    ];
@endphp

<x-admin.layout title="Dashboard admin">
    <section class="overflow-hidden rounded-[2rem] bg-slate-950 text-white shadow-2xl shadow-slate-950/15">
        <div class="relative px-6 py-8 sm:px-8 lg:px-10">
            <div class="absolute -right-20 -top-24 h-72 w-72 rounded-full bg-pink-400/20"></div>
            <div class="absolute right-24 top-12 h-40 w-40 rounded-full bg-white/10"></div>

            <div class="relative flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <p class="text-xs font-black uppercase tracking-[0.24em] text-pink-200">visao geral</p>
                    <h1 class="mt-3 text-3xl font-black tracking-tight sm:text-4xl">Painel administrativo</h1>
                    <p class="mt-3 max-w-2xl text-sm leading-6 text-slate-300">
                        Acompanhe crescimento, planos, checkouts pendentes e contas que precisam de atencao.
                    </p>
                </div>

                <div class="flex flex-wrap gap-2">
                    <a
                        href="{{ route('admin.users.index') }}"
                        class="inline-flex items-center justify-center gap-2 rounded-2xl bg-white px-5 py-3 text-sm font-black text-slate-950 shadow-lg shadow-black/10"
                    >
                        <x-dashboard-icon name="store" class="h-5 w-5" />
                        Gerenciar usuarios
                    </a>

                    <a
                        href="{{ route('admin.settings.index') }}"
                        class="inline-flex items-center justify-center gap-2 rounded-2xl bg-pink-500 px-5 py-3 text-sm font-black text-white shadow-lg shadow-black/10"
                    >
                        <x-dashboard-icon name="settings" class="h-5 w-5" />
                        Configurar planos
                    </a>
                </div>
            </div>
        </div>
    </section>

    <section class="mt-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
        @foreach($cards as $card)
            <article class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="text-sm font-bold text-slate-500">{{ $card['label'] }}</p>
                        <strong class="mt-2 block text-3xl font-black text-slate-950">{{ $card['value'] }}</strong>
                        <span class="mt-2 block text-xs font-bold uppercase tracking-[0.12em] text-slate-400">{{ $card['hint'] }}</span>
                    </div>

                    <span class="{{ $card['tone'] }} flex h-12 w-12 items-center justify-center rounded-2xl text-white shadow-lg shadow-slate-900/10">
                        <x-dashboard-icon :name="$card['icon']" class="h-6 w-6" />
                    </span>
                </div>
            </article>
        @endforeach
    </section>

    <section class="mt-6 grid gap-6 lg:grid-cols-[1.1fr_0.9fr]">
        <article class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <p class="text-xs font-black uppercase tracking-[0.2em] text-slate-400">planos</p>
                    <h2 class="mt-1 text-xl font-black text-slate-950">Distribuicao dos usuarios</h2>
                </div>
            </div>

            <div class="mt-6 space-y-4">
                @foreach($plans as $key => $plan)
                    @php
                        $total = (int) ($planCounts[$key] ?? 0);
                        $percent = $metrics['users'] > 0 ? min(100, round(($total / $metrics['users']) * 100)) : 0;
                    @endphp

                    <div>
                        <div class="mb-2 flex items-center justify-between gap-3">
                            <span class="inline-flex items-center gap-2 rounded-full px-3 py-1 text-xs font-black ring-1 {{ $planTone[$key] ?? 'bg-slate-100 text-slate-700 ring-slate-200' }}">
                                {{ $plan['name'] }}
                            </span>
                            <span class="text-sm font-black text-slate-700">{{ $total }} usuario(s)</span>
                        </div>
                        <div class="h-3 overflow-hidden rounded-full bg-slate-100">
                            <div class="h-full rounded-full bg-slate-950" style="width: {{ $percent }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </article>

        <article class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <p class="text-xs font-black uppercase tracking-[0.2em] text-slate-400">assinaturas</p>
                    <h2 class="mt-1 text-xl font-black text-slate-950">Ultimas tentativas</h2>
                </div>
            </div>

            <div class="mt-5 space-y-3">
                @forelse($latestSubscriptions as $subscription)
                    <a href="{{ route('admin.users.show', $subscription->user) }}" class="flex items-center justify-between gap-3 rounded-2xl border border-slate-100 p-3 transition hover:border-pink-200 hover:bg-pink-50/40">
                        <span class="min-w-0">
                            <span class="block truncate text-sm font-black text-slate-950">{{ $subscription->user?->name ?? 'Usuario removido' }}</span>
                            <span class="block text-xs font-bold text-slate-400">Plano {{ $plans[$subscription->plan]['name'] ?? $subscription->plan }} - {{ $subscription->created_at?->format('d/m H:i') }}</span>
                        </span>
                        <span class="rounded-full px-3 py-1 text-xs font-black {{ $subscription->status === 'active' ? 'bg-emerald-100 text-emerald-700' : ($subscription->status === 'pending' ? 'bg-amber-100 text-amber-700' : 'bg-slate-100 text-slate-600') }}">
                            {{ $subscription->status }}
                        </span>
                    </a>
                @empty
                    <p class="rounded-2xl bg-slate-50 p-4 text-sm font-bold text-slate-500">Nenhuma assinatura registrada ainda.</p>
                @endforelse
            </div>
        </article>
    </section>

    <section class="mt-6 rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-xs font-black uppercase tracking-[0.2em] text-slate-400">novas contas</p>
                <h2 class="mt-1 text-xl font-black text-slate-950">Ultimos usuarios cadastrados</h2>
            </div>

            <a href="{{ route('admin.users.index') }}" class="inline-flex items-center gap-2 rounded-2xl bg-slate-950 px-4 py-2 text-sm font-black text-white">
                Ver todos
                <x-dashboard-icon name="external" class="h-4 w-4" />
            </a>
        </div>

        <div class="mt-5 overflow-hidden rounded-3xl border border-slate-100">
            <div class="hidden grid-cols-[1.2fr_0.8fr_0.7fr_0.8fr] bg-slate-50 px-4 py-3 text-xs font-black uppercase tracking-[0.14em] text-slate-400 md:grid">
                <span>Usuario</span>
                <span>Loja</span>
                <span>Plano</span>
                <span class="text-right">Atividade</span>
            </div>

            @forelse($latestUsers as $user)
                @php($plan = $plans[$user->plan ?: 'free'] ?? $plans['free'])

                <a href="{{ route('admin.users.show', $user) }}" class="grid gap-3 border-t border-slate-100 px-4 py-4 transition hover:bg-slate-50 md:grid-cols-[1.2fr_0.8fr_0.7fr_0.8fr] md:items-center">
                    <span>
                        <span class="block font-black text-slate-950">{{ trim($user->name . ' ' . ($user->last_name ?? '')) }}</span>
                        <span class="block text-sm font-semibold text-slate-400">{{ $user->email }}</span>
                    </span>

                    <span class="text-sm font-bold text-slate-600">{{ $user->store?->name ?? 'Sem loja' }}</span>

                    <span>
                        <span class="inline-flex rounded-full px-3 py-1 text-xs font-black ring-1 {{ $planTone[$user->plan ?: 'free'] ?? 'bg-slate-100 text-slate-700 ring-slate-200' }}">
                            {{ $plan['name'] }}
                        </span>
                    </span>

                    <span class="text-left text-sm font-bold text-slate-500 md:text-right">
                        {{ $user->store?->products_count ?? 0 }} produtos / {{ $user->store?->orders_count ?? 0 }} pedidos
                    </span>
                </a>
            @empty
                <p class="border-t border-slate-100 p-5 text-sm font-bold text-slate-500">Nenhum usuario cadastrado.</p>
            @endforelse
        </div>
    </section>
</x-admin.layout>
