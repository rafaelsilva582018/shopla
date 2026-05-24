@php
    $theme = $store ? $store->dashboardTheme() : config('dashboard-themes.blush');
    $currentPlan = $user->plan ?: 'free';
    $asaasReady = filled(config('services.asaas.access_token'));
@endphp

<x-app-layout>
    <div class="min-h-screen pb-24" style="background: {{ $theme['bg'] }}; color: {{ $theme['text'] }};">
        <div class="max-w-7xl mx-auto px-4 py-8 space-y-8">
            <header class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-5">
                <div>
                    <p class="text-sm font-semibold tracking-widest uppercase" style="color: {{ $theme['muted'] }}">
                        Planos
                    </p>

                    <h1 class="text-4xl md:text-5xl font-bold mt-2" style="font-family: serif;">
                        Escolha o tamanho da sua loja
                    </h1>

                    <p class="mt-3 max-w-2xl" style="color: {{ $theme['muted'] }}">
                        Assine pelo checkout seguro do Asaas. Quando o pagamento for confirmado, o limite do plano entra automaticamente.
                    </p>
                </div>

                <div class="rounded-3xl px-5 py-4 border shadow-sm" style="background: {{ $theme['card'] }}; border-color: {{ $theme['border'] }}">
                    <p class="text-xs font-bold uppercase tracking-widest" style="color: {{ $theme['muted'] }}">Plano atual</p>
                    <p class="text-2xl font-black mt-1">{{ $user->planName() }}</p>
                    <p class="text-sm" style="color: {{ $theme['muted'] }}">Até {{ $user->productLimitLabel() }} produtos</p>
                </div>
            </header>

            @if(session('success'))
                <div class="rounded-3xl p-5 border font-semibold" style="background: {{ $theme['secondary'] }}; border-color: {{ $theme['border'] }}; color: {{ $theme['primary'] }}">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="rounded-3xl p-5 border font-semibold bg-red-50 text-red-600 border-red-100 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <span>{{ session('error') }}</span>

                    @if(str_contains(session('error'), 'Perfil'))
                        <a href="{{ route('profile.edit') }}" class="inline-flex items-center justify-center rounded-2xl px-4 py-2 bg-white border border-red-100 text-red-600">
                            Completar perfil
                        </a>
                    @endif
                </div>
            @endif

            @if($errors->any())
                <div class="rounded-3xl p-5 border font-semibold bg-red-50 text-red-600 border-red-100">
                    {{ $errors->first() }}
                </div>
            @endif

            @if(!$asaasReady)
                <div class="rounded-3xl p-5 border shadow-sm" style="background: {{ $theme['card'] }}; border-color: {{ $theme['border'] }}">
                    <p class="font-black">Asaas ainda nao configurado</p>
                    <p class="text-sm mt-1" style="color: {{ $theme['muted'] }}">
                        Preencha `ASAAS_ACCESS_TOKEN` e `ASAAS_WEBHOOK_TOKEN` no .env para liberar os botões de assinatura.
                    </p>
                </div>
            @endif

            @if($latestSubscription && $latestSubscription->status !== 'active')
                <div class="rounded-3xl p-5 border shadow-sm flex flex-col md:flex-row md:items-center md:justify-between gap-4" style="background: {{ $theme['card'] }}; border-color: {{ $theme['border'] }}">
                    <div>
                        <p class="font-black">Última tentativa de assinatura</p>
                        <p class="text-sm mt-1" style="color: {{ $theme['muted'] }}">
                            Plano {{ $plans[$latestSubscription->plan]['name'] ?? $latestSubscription->plan }} -
                            status {{ str_replace('_', ' ', $latestSubscription->status) }}.
                        </p>
                    </div>

                    @if($latestSubscription->checkout_url && $latestSubscription->status === 'pending')
                        <a href="{{ $latestSubscription->checkout_url }}" class="inline-flex items-center justify-center rounded-2xl px-5 py-3 font-bold text-white" style="background: {{ $theme['primary'] }}">
                            Continuar pagamento
                        </a>
                    @endif
                </div>
            @endif

            <section class="grid md:grid-cols-2 xl:grid-cols-5 gap-5">
                @foreach($plans as $key => $plan)
                    @php
                        $isCurrent = $currentPlan === $key;
                        $isContact = $key === 'enterprise';
                        $isPaid = isset($plan['price']);
                    @endphp

                    <div
                        class="rounded-3xl p-6 border shadow-sm flex flex-col min-h-[430px]"
                        style="background: {{ $isCurrent ? $theme['secondary'] : $theme['card'] }}; border-color: {{ $isCurrent ? $theme['primary'] : $theme['border'] }}"
                    >
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <h2 class="text-2xl font-bold">{{ $plan['name'] }}</h2>
                                <p class="text-sm mt-2" style="color: {{ $theme['muted'] }}">
                                    {{ $plan['description'] }}
                                </p>
                            </div>

                            @if($isCurrent)
                                <span class="text-xs px-3 py-1 rounded-full text-white font-semibold" style="background: {{ $theme['primary'] }}">
                                    Atual
                                </span>
                            @endif
                        </div>

                        <div class="mt-8">
                            @if($isPaid)
                                <p class="text-4xl font-black">
                                    R$ {{ number_format($plan['price'], 2, ',', '.') }}
                                </p>
                                <p class="text-sm font-semibold mt-1" style="color: {{ $theme['muted'] }}">{{ $plan['period_label'] ?? 'por mes' }}</p>
                                @if(isset($plan['annual_price']))
                                    <p class="text-xs font-semibold mt-2" style="color: {{ $theme['muted'] }}">
                                        Anual: R$ {{ number_format($plan['annual_price'], 2, ',', '.') }} {{ $plan['annual_label'] ?? 'por ano' }}
                                        @if(($plan['annual_discount_percent'] ?? 0) > 0)
                                            <span class="font-black" style="color: {{ $theme['primary'] }}">-{{ number_format($plan['annual_discount_percent'], 0) }}%</span>
                                        @endif
                                    </p>
                                @endif
                            @else
                                <p class="text-4xl font-black">
                                    {{ $key === 'free' ? 'R$ 0' : '200+' }}
                                </p>
                                <p class="text-sm font-semibold mt-1" style="color: {{ $theme['muted'] }}">
                                    {{ $key === 'free' ? 'para começar' : 'produtos sob consulta' }}
                                </p>
                            @endif
                        </div>

                        <div class="mt-7 rounded-2xl p-4 border" style="border-color: {{ $theme['border'] }}">
                            <p class="text-sm font-bold" style="color: {{ $theme['muted'] }}">Produtos</p>
                            <p class="text-3xl font-black mt-1">
                                {{ $plan['limit'] ? $plan['limit'] : '200+' }}
                            </p>
                        </div>

                        <div class="mt-6 space-y-3 text-sm" style="color: {{ $theme['muted'] }}">
                            <p>✓ Catálogo público</p>
                            <p>✓ Carrinho com pedidos</p>
                            <p>✓ Controle de estoque</p>
                            <p>✓ Ranking de vendidos</p>
                            <p>✓ {{ ($plan['custom_slug'] ?? false) ? 'Link personalizado' : 'Link automatico' }}</p>
                        </div>

                        <div class="mt-auto pt-8">
                            @if($isCurrent)
                                <button class="w-full rounded-2xl px-5 py-3 font-semibold border" style="border-color: {{ $theme['primary'] }}; color: {{ $theme['primary'] }}" disabled>
                                    Plano atual
                                </button>
                            @elseif($isContact)
                                <a href="{{ $store ? route('store.edit') : route('profile.edit') }}" class="block text-center w-full rounded-2xl px-5 py-3 font-semibold text-white" style="background: {{ $theme['primary'] }}">
                                    Entrar em contato
                                </a>
                            @elseif($isPaid && $asaasReady)
                                <div class="space-y-2">
                                    <form method="POST" action="{{ route('plans.checkout', $key) }}">
                                        @csrf
                                        <input type="hidden" name="billing_period" value="monthly">
                                        <button type="submit" class="w-full rounded-2xl px-5 py-3 font-semibold text-white shadow-sm hover:-translate-y-0.5 transition" style="background: {{ $theme['primary'] }}">
                                            Assinar mensal
                                        </button>
                                    </form>

                                    <form method="POST" action="{{ route('plans.checkout', $key) }}">
                                        @csrf
                                        <input type="hidden" name="billing_period" value="annual">
                                        <button type="submit" class="w-full rounded-2xl px-5 py-3 font-semibold border" style="border-color: {{ $theme['primary'] }}; color: {{ $theme['primary'] }}">
                                            Anual com {{ number_format($plan['annual_discount_percent'] ?? 10, 0) }}% off
                                        </button>
                                    </form>
                                </div>
                            @else
                                <button class="w-full rounded-2xl px-5 py-3 font-semibold text-white opacity-60" style="background: {{ $theme['primary'] }}" disabled>
                                    Configurar Asaas
                                </button>
                            @endif
                        </div>
                    </div>
                @endforeach
            </section>
        </div>
    </div>
</x-app-layout>
