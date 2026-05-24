@php
    $inputClass = 'w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-semibold text-slate-700 outline-none transition focus:border-pink-400 focus:bg-white focus:ring-4 focus:ring-pink-100';
    $labelClass = 'mb-2 block text-sm font-black text-slate-700';
    $money = fn ($value) => 'R$ ' . number_format((float) $value, 2, ',', '.');
@endphp

<x-admin.layout title="Configuracoes admin">
    <section class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <p class="text-xs font-black uppercase tracking-[0.22em] text-pink-500">sistema</p>
            <h1 class="mt-2 text-3xl font-black tracking-tight text-slate-950">Configuracoes globais</h1>
            <p class="mt-2 max-w-2xl text-sm font-semibold leading-6 text-slate-500">
                Ajuste valores, limites e desconto anual dos planos sem precisar editar arquivo no servidor.
            </p>
        </div>

        <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center justify-center gap-2 rounded-2xl bg-slate-950 px-5 py-3 text-sm font-black text-white">
            <x-dashboard-icon name="trend" class="h-5 w-5" />
            Voltar ao dashboard
        </a>
    </section>

    <section class="mt-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        @foreach(['plus', 'pro', 'premium'] as $planKey)
            @php($plan = $plans[$planKey])

            <article class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-sm font-bold text-slate-500">Plano {{ $plan['name'] }}</p>
                <strong class="mt-2 block text-3xl font-black text-slate-950">{{ $money($plan['price']) }}</strong>
                <p class="mt-2 text-xs font-bold uppercase tracking-[0.12em] text-slate-400">
                    {{ $plan['limit'] }} produtos / anual {{ $money($plan['annual_price']) }}
                </p>
            </article>
        @endforeach

        <article class="rounded-3xl border border-slate-200 bg-slate-950 p-5 text-white shadow-sm">
            <p class="text-sm font-bold text-slate-300">Desconto anual</p>
            <strong class="mt-2 block text-3xl font-black">{{ number_format((float) ($plans['plus']['annual_discount_percent'] ?? 0), 0) }}%</strong>
            <p class="mt-2 text-xs font-bold uppercase tracking-[0.12em] text-slate-400">
                aplicado nos planos pagos
            </p>
        </article>
    </section>

    <form method="POST" action="{{ route('admin.settings.update') }}" class="mt-6 rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm">
        @csrf
        @method('PUT')

        <div class="flex items-center gap-3">
            <span class="flex h-11 w-11 items-center justify-center rounded-2xl bg-pink-100 text-pink-700">
                <x-dashboard-icon name="settings" class="h-5 w-5" />
            </span>
            <div>
                <h2 class="text-xl font-black text-slate-950">Planos comerciais</h2>
                <p class="text-sm font-semibold text-slate-500">Esses dados aparecem na landing, no wizard, na tela de planos e no checkout do Asaas.</p>
            </div>
        </div>

        <div class="mt-6 rounded-3xl border border-slate-100 bg-slate-50 p-5">
            <h3 class="text-sm font-black uppercase tracking-[0.18em] text-slate-400">Plano gratuito</h3>

            <div class="mt-4 max-w-xs">
                <label class="block">
                    <span class="{{ $labelClass }}">Limite de produtos gratis</span>
                    <input type="number" name="free_limit" min="0" max="1000" value="{{ old('free_limit', $settings['plans.free.limit']) }}" class="{{ $inputClass }}">
                    @error('free_limit') <span class="mt-1 block text-xs font-bold text-red-500">{{ $message }}</span> @enderror
                </label>
            </div>
        </div>

        <div class="mt-5 grid gap-5 lg:grid-cols-3">
            @foreach([
                'plus' => ['title' => 'Plus', 'limit' => 'plus_limit', 'price' => 'plus_price'],
                'pro' => ['title' => 'Pro', 'limit' => 'pro_limit', 'price' => 'pro_price'],
                'premium' => ['title' => 'Premium', 'limit' => 'premium_limit', 'price' => 'premium_price'],
            ] as $key => $fields)
                <article class="rounded-3xl border border-slate-100 bg-slate-50 p-5">
                    <h3 class="text-lg font-black text-slate-950">Plano {{ $fields['title'] }}</h3>
                    <p class="mt-1 text-sm font-semibold text-slate-500">{{ $plans[$key]['description'] }}</p>

                    <div class="mt-5 space-y-4">
                        <label class="block">
                            <span class="{{ $labelClass }}">Limite de produtos</span>
                            <input
                                type="number"
                                name="{{ $fields['limit'] }}"
                                min="1"
                                max="1000"
                                value="{{ old($fields['limit'], $settings['plans.' . $key . '.limit']) }}"
                                class="{{ $inputClass }}"
                            >
                            @error($fields['limit']) <span class="mt-1 block text-xs font-bold text-red-500">{{ $message }}</span> @enderror
                        </label>

                        <label class="block">
                            <span class="{{ $labelClass }}">Valor mensal</span>
                            <input
                                type="number"
                                step="0.01"
                                min="0"
                                name="{{ $fields['price'] }}"
                                value="{{ old($fields['price'], $settings['plans.' . $key . '.price']) }}"
                                class="{{ $inputClass }}"
                            >
                            @error($fields['price']) <span class="mt-1 block text-xs font-bold text-red-500">{{ $message }}</span> @enderror
                        </label>
                    </div>
                </article>
            @endforeach
        </div>

        <div class="mt-5 rounded-3xl border border-slate-100 bg-slate-950 p-5 text-white">
            <div class="grid gap-4 lg:grid-cols-[1fr_260px] lg:items-end">
                <div>
                    <h3 class="text-lg font-black">Pagamento anual</h3>
                    <p class="mt-1 text-sm font-semibold text-slate-300">
                        O sistema calcula o anual automaticamente: valor mensal x 12 menos o desconto escolhido.
                    </p>
                </div>

                <label class="block">
                    <span class="mb-2 block text-sm font-black text-white">Desconto anual (%)</span>
                    <input
                        type="number"
                        step="0.01"
                        min="0"
                        max="90"
                        name="annual_discount_percent"
                        value="{{ old('annual_discount_percent', $settings['plans.annual_discount_percent']) }}"
                        class="w-full rounded-2xl border border-white/10 bg-white px-4 py-3 text-sm font-black text-slate-950 outline-none transition focus:ring-4 focus:ring-pink-400/30"
                    >
                    @error('annual_discount_percent') <span class="mt-1 block text-xs font-bold text-red-200">{{ $message }}</span> @enderror
                </label>
            </div>
        </div>

        <div class="mt-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <p class="text-sm font-semibold text-slate-500">
                Alterar valores aqui afeta novas assinaturas. Assinaturas antigas continuam com o valor que ja foi enviado ao Asaas.
            </p>

            <button class="inline-flex items-center justify-center gap-2 rounded-2xl bg-pink-600 px-6 py-4 text-sm font-black text-white shadow-lg shadow-pink-600/20">
                <x-dashboard-icon name="settings" class="h-5 w-5" />
                Salvar configuracoes
            </button>
        </div>
    </form>
</x-admin.layout>
