@php
    $planKey = $user->plan ?: 'free';
    $planConfig = $plans[$planKey] ?? $plans['free'];
    $inputClass = 'w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-semibold text-slate-700 outline-none transition focus:border-pink-400 focus:bg-white focus:ring-4 focus:ring-pink-100';
    $labelClass = 'mb-2 block text-sm font-black text-slate-700';
    $planTone = [
        'free' => 'bg-slate-100 text-slate-700 ring-slate-200',
        'plus' => 'bg-pink-100 text-pink-700 ring-pink-200',
        'pro' => 'bg-violet-100 text-violet-700 ring-violet-200',
        'premium' => 'bg-amber-100 text-amber-800 ring-amber-200',
        'enterprise' => 'bg-slate-950 text-white ring-slate-950',
    ];
@endphp

<x-admin.layout title="Conta admin">
    <section class="flex flex-col gap-5 lg:flex-row lg:items-start lg:justify-between">
        <div class="flex min-w-0 items-center gap-4">
            <span class="flex h-16 w-16 shrink-0 items-center justify-center rounded-3xl bg-slate-950 text-2xl font-black text-white shadow-xl shadow-slate-950/15">
                {{ strtoupper(substr($user->name, 0, 1)) }}
            </span>

            <div class="min-w-0">
                <p class="text-xs font-black uppercase tracking-[0.22em] text-pink-500">conta do usuario</p>
                <h1 class="mt-1 truncate text-3xl font-black tracking-tight text-slate-950">{{ trim($user->name . ' ' . ($user->last_name ?? '')) }}</h1>
                <p class="mt-1 truncate text-sm font-semibold text-slate-500">{{ $user->email }}</p>
            </div>
        </div>

        <div class="flex flex-wrap gap-2">
            <a href="{{ route('admin.users.index') }}" class="inline-flex items-center justify-center gap-2 rounded-2xl border border-slate-200 bg-white px-5 py-3 text-sm font-black text-slate-700">
                <x-dashboard-icon name="external" class="h-5 w-5 rotate-180" />
                Voltar
            </a>

            @if($user->store)
                <a href="{{ route('store.public', $user->store->slug) }}" target="_blank" class="inline-flex items-center justify-center gap-2 rounded-2xl bg-slate-950 px-5 py-3 text-sm font-black text-white">
                    <x-dashboard-icon name="link" class="h-5 w-5" />
                    Abrir vitrine
                </a>
            @endif
        </div>
    </section>

    <section class="mt-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <article class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-sm font-bold text-slate-500">Plano atual</p>
            <span class="mt-3 inline-flex rounded-full px-3 py-1 text-xs font-black ring-1 {{ $planTone[$planKey] ?? 'bg-slate-100 text-slate-700 ring-slate-200' }}">
                {{ $planConfig['name'] }}
            </span>
            <p class="mt-3 text-xs font-bold uppercase tracking-[0.12em] text-slate-400">limite: {{ $user->productLimitLabel() }} produtos</p>
        </article>

        <article class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-sm font-bold text-slate-500">Produtos</p>
            <strong class="mt-2 block text-3xl font-black text-slate-950">{{ $user->store?->products_count ?? 0 }}</strong>
            <p class="mt-2 text-xs font-bold uppercase tracking-[0.12em] text-slate-400">cadastrados na loja</p>
        </article>

        <article class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-sm font-bold text-slate-500">Pedidos</p>
            <strong class="mt-2 block text-3xl font-black text-slate-950">{{ $user->store?->orders_count ?? 0 }}</strong>
            <p class="mt-2 text-xs font-bold uppercase tracking-[0.12em] text-slate-400">registrados na vitrine</p>
        </article>

        <article class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-sm font-bold text-slate-500">Assinaturas</p>
            <strong class="mt-2 block text-3xl font-black text-slate-950">{{ $user->plan_subscriptions_count }}</strong>
            <p class="mt-2 text-xs font-bold uppercase tracking-[0.12em] text-slate-400">
                {{ (int) ($subscriptionStats['active'] ?? 0) }} ativa(s)
            </p>
        </article>
    </section>

    <section class="mt-6 grid gap-6 lg:grid-cols-[1.1fr_0.9fr]">
        <form method="POST" action="{{ route('admin.users.update', $user) }}" class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm">
            @csrf
            @method('PATCH')

            <div class="mb-6 flex items-center gap-3">
                <span class="flex h-11 w-11 items-center justify-center rounded-2xl bg-pink-100 text-pink-700">
                    <x-dashboard-icon name="settings" class="h-5 w-5" />
                </span>
                <div>
                    <h2 class="text-xl font-black text-slate-950">Controle da conta</h2>
                    <p class="text-sm font-semibold text-slate-500">Altere plano, contato e status da vitrine.</p>
                </div>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <label class="block">
                    <span class="{{ $labelClass }}">Nome</span>
                    <input name="name" value="{{ old('name', $user->name) }}" class="{{ $inputClass }}" required>
                    @error('name') <span class="mt-1 block text-xs font-bold text-red-500">{{ $message }}</span> @enderror
                </label>

                <label class="block">
                    <span class="{{ $labelClass }}">Sobrenome</span>
                    <input name="last_name" value="{{ old('last_name', $user->last_name) }}" class="{{ $inputClass }}">
                    @error('last_name') <span class="mt-1 block text-xs font-bold text-red-500">{{ $message }}</span> @enderror
                </label>

                <label class="block sm:col-span-2">
                    <span class="{{ $labelClass }}">E-mail</span>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" class="{{ $inputClass }}" required>
                    @error('email') <span class="mt-1 block text-xs font-bold text-red-500">{{ $message }}</span> @enderror
                </label>

                <label class="block">
                    <span class="{{ $labelClass }}">WhatsApp / celular</span>
                    <input name="phone" value="{{ old('phone', $user->phone) }}" class="{{ $inputClass }}" placeholder="(00) 00000-0000">
                    @error('phone') <span class="mt-1 block text-xs font-bold text-red-500">{{ $message }}</span> @enderror
                </label>

                <label class="block">
                    <span class="{{ $labelClass }}">CPF/CNPJ</span>
                    <input name="document" value="{{ old('document', $user->document) }}" class="{{ $inputClass }}" placeholder="000.000.000-00">
                    @error('document') <span class="mt-1 block text-xs font-bold text-red-500">{{ $message }}</span> @enderror
                </label>

                <label class="block">
                    <span class="{{ $labelClass }}">Plano</span>
                    <select name="plan" class="{{ $inputClass }}">
                        @foreach($plans as $key => $plan)
                            <option value="{{ $key }}" @selected(old('plan', $planKey) === $key)>
                                {{ $plan['name'] }} - {{ $plan['limit'] ? $plan['limit'] . ' produtos' : 'sem limite' }}
                            </option>
                        @endforeach
                    </select>
                    @error('plan') <span class="mt-1 block text-xs font-bold text-red-500">{{ $message }}</span> @enderror
                </label>

                @if($user->store)
                    <label class="block">
                        <span class="{{ $labelClass }}">Status da vitrine</span>
                        <input type="hidden" name="store_is_active" value="0">
                        <label class="flex h-[50px] items-center gap-3 rounded-2xl border border-slate-200 bg-slate-50 px-4 text-sm font-black text-slate-700">
                            <input type="checkbox" name="store_is_active" value="1" class="rounded border-slate-300 text-pink-600 focus:ring-pink-500" @checked(old('store_is_active', $user->store->is_active))>
                            Loja ativa e publica
                        </label>
                    </label>
                @endif
            </div>

            <button class="mt-6 inline-flex w-full items-center justify-center gap-2 rounded-2xl bg-pink-600 px-5 py-4 text-sm font-black text-white shadow-lg shadow-pink-600/20 sm:w-auto">
                <x-dashboard-icon name="settings" class="h-5 w-5" />
                Salvar alteracoes
            </button>
        </form>

        <aside class="space-y-6">
            <article class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm">
                <div class="mb-5 flex items-center gap-3">
                    <span class="flex h-11 w-11 items-center justify-center rounded-2xl bg-slate-100 text-slate-700">
                        <x-dashboard-icon name="store" class="h-5 w-5" />
                    </span>
                    <div>
                        <h2 class="text-xl font-black text-slate-950">Loja vinculada</h2>
                        <p class="text-sm font-semibold text-slate-500">Resumo da vitrine do usuario.</p>
                    </div>
                </div>

                @if($user->store)
                    <dl class="space-y-4 text-sm">
                        <div class="flex items-center justify-between gap-4 border-b border-slate-100 pb-3">
                            <dt class="font-bold text-slate-500">Nome</dt>
                            <dd class="text-right font-black text-slate-900">{{ $user->store->name }}</dd>
                        </div>
                        <div class="flex items-center justify-between gap-4 border-b border-slate-100 pb-3">
                            <dt class="font-bold text-slate-500">Link</dt>
                            <dd class="text-right font-black text-slate-900">/{{ $user->store->slug }}</dd>
                        </div>
                        <div class="flex items-center justify-between gap-4 border-b border-slate-100 pb-3">
                            <dt class="font-bold text-slate-500">Categorias</dt>
                            <dd class="font-black text-slate-900">{{ $user->store->categories_count }}</dd>
                        </div>
                        <div class="flex items-center justify-between gap-4">
                            <dt class="font-bold text-slate-500">Status</dt>
                            <dd class="rounded-full px-3 py-1 text-xs font-black {{ $user->store->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700' }}">
                                {{ $user->store->is_active ? 'Ativa' : 'Inativa' }}
                            </dd>
                        </div>
                    </dl>
                @else
                    <p class="rounded-2xl bg-slate-50 p-4 text-sm font-bold text-slate-500">
                        Este usuario ainda nao criou a loja.
                    </p>
                @endif
            </article>

            <article class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm">
                <div class="mb-5 flex items-center gap-3">
                    <span class="flex h-11 w-11 items-center justify-center rounded-2xl bg-violet-100 text-violet-700">
                        <x-dashboard-icon name="diamond" class="h-5 w-5" />
                    </span>
                    <div>
                        <h2 class="text-xl font-black text-slate-950">Historico de planos</h2>
                        <p class="text-sm font-semibold text-slate-500">Ultimas tentativas no Asaas.</p>
                    </div>
                </div>

                <div class="space-y-3">
                    @forelse($user->planSubscriptions as $subscription)
                        <div class="rounded-2xl border border-slate-100 p-4">
                            <div class="flex items-center justify-between gap-3">
                                <strong class="text-sm text-slate-950">{{ $plans[$subscription->plan]['name'] ?? $subscription->plan }}</strong>
                                <span class="rounded-full px-3 py-1 text-xs font-black {{ $subscription->status === 'active' ? 'bg-emerald-100 text-emerald-700' : ($subscription->status === 'pending' ? 'bg-amber-100 text-amber-700' : 'bg-slate-100 text-slate-600') }}">
                                    {{ $subscription->status }}
                                </span>
                            </div>
                            <p class="mt-2 text-xs font-bold text-slate-400">
                                R$ {{ number_format((float) $subscription->amount, 2, ',', '.') }} - {{ $subscription->created_at?->format('d/m/Y H:i') }}
                            </p>
                        </div>
                    @empty
                        <p class="rounded-2xl bg-slate-50 p-4 text-sm font-bold text-slate-500">
                            Nenhuma tentativa de assinatura registrada.
                        </p>
                    @endforelse
                </div>
            </article>
        </aside>
    </section>
</x-admin.layout>
