@php
    $dashboardThemes = config('dashboard-themes');
    $storeThemes = config('store-themes');
    $selectedDashboardTheme = old('dashboard_theme', $user->dashboard_theme ?: 'blush');
    $theme = $store
        ? $store->dashboardTheme()
        : config('dashboard-themes.' . $selectedDashboardTheme, config('dashboard-themes.blush'));
    $currentStep = $store?->onboarding_completed_at ? 6 : $step;
    $steps = [
        1 => ['title' => 'Painel', 'hint' => 'Escolha o visual'],
        2 => ['title' => 'Plano', 'hint' => 'Escolha a versao'],
        3 => ['title' => 'Loja', 'hint' => 'Dados da vitrine'],
        4 => ['title' => 'Categorias', 'hint' => 'Opcional'],
        5 => ['title' => 'Produto', 'hint' => 'Opcional'],
        6 => ['title' => 'Pronto', 'hint' => 'Tudo certo'],
    ];
    $progressPercent = round(($currentStep / count($steps)) * 100);
    $paidPlanKeys = ['plus', 'pro', 'premium'];
    $wizardPlanKeys = ['free', 'plus', 'pro', 'premium'];
    $selectedBillingPeriod = old('billing_period', 'monthly');
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Primeiros passos - Shopla</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body
    class="font-sans antialiased"
    x-data="{
        selectedDashboardTheme: '{{ $selectedDashboardTheme }}',
        selectedStoreTheme: '{{ old('store_theme', 'candy') }}',
        selectedPlan: '{{ $selectedOnboardingPlan }}',
        selectedBillingPeriod: '{{ $selectedBillingPeriod }}',
        slug: @js(old('slug', '')),
        slugStatus: null,
        slugMessage: '',
        dashboardThemes: @js($dashboardThemes),
        get activeTheme() {
            return this.dashboardThemes[this.selectedDashboardTheme] || this.dashboardThemes.blush;
        },
        async checkSlug() {
            const clean = this.slug.trim();

            if (!clean) {
                this.slugStatus = null;
                this.slugMessage = 'Digite o link desejado.';
                return;
            }

            const response = await fetch(@js(route('onboarding.slug-check')) + '?slug=' + encodeURIComponent(clean));
            const data = await response.json();

            this.slug = data.slug;
            this.slugStatus = data.available ? 'available' : 'unavailable';
            this.slugMessage = data.message;
        }
    }"
    :style="`background: ${activeTheme.bg}; color: ${activeTheme.text};`"
    style="background: {{ $theme['bg'] }}; color: {{ $theme['text'] }};"
>
    <main class="min-h-screen px-4 py-6 md:py-10">
        <div class="max-w-6xl mx-auto">
            <div class="flex items-center justify-between gap-4 mb-8">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-2xl flex items-center justify-center text-white font-bold" :style="`background: ${activeTheme.primary}`">
                        S
                    </div>
                    <div>
                        <h1 class="font-bold text-xl">Shopla</h1>
                        <p class="text-sm" :style="`color: ${activeTheme.muted}`">Configuracao guiada</p>
                    </div>
                </div>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="px-4 py-2 rounded-2xl border text-sm font-semibold" :style="`border-color: ${activeTheme.border}; color: ${activeTheme.muted}`">
                        Sair
                    </button>
                </form>
            </div>

            <section class="rounded-3xl p-6 md:p-8 border shadow-sm relative overflow-hidden text-white mb-6" :style="`background: linear-gradient(135deg, ${activeTheme.primary}, ${activeTheme.secondary}); border-color: ${activeTheme.border}`">
                <div class="absolute -right-20 -top-20 w-64 h-64 rounded-full bg-white/20"></div>
                <div class="absolute left-1/2 -bottom-28 w-72 h-72 rounded-full bg-white/10"></div>

                <div class="relative z-10 max-w-2xl">
                    <p class="text-sm font-semibold tracking-widest uppercase text-white/80">
                        Primeiros passos
                    </p>

                    <h2 class="text-4xl md:text-5xl font-bold mt-2" style="font-family: serif;">
                        Vamos publicar sua loja
                    </h2>

                    <p class="mt-3 text-white/85">
                        Primeiro escolha como quer ver seu painel. Depois configuramos a vitrine para seus clientes.
                    </p>
                </div>
            </section>

            <section class="rounded-3xl border p-5 mb-6" :style="`background: ${activeTheme.card}; border-color: ${activeTheme.border}`">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-4">
                    <div>
                        <p class="text-xs font-semibold tracking-widest uppercase" :style="`color: ${activeTheme.muted}`">
                            Progresso
                        </p>
                        <div class="flex items-center gap-3 mt-3">
                            <div class="w-11 h-11 rounded-2xl flex items-center justify-center text-white font-bold" :style="`background: ${activeTheme.primary}`">
                                {{ $currentStep }}
                            </div>
                            <div>
                                <h3 class="font-bold text-lg">{{ $steps[$currentStep]['title'] }}</h3>
                                <p class="text-sm" :style="`color: ${activeTheme.muted}`">{{ $steps[$currentStep]['hint'] }}</p>
                            </div>
                        </div>
                    </div>

                    <p class="text-sm font-semibold" :style="`color: ${activeTheme.muted}`">
                        Passo {{ $currentStep }} de {{ count($steps) }}
                    </p>
                </div>

                <div class="h-3 rounded-full overflow-hidden" :style="`background: ${activeTheme.secondary}`">
                    <div class="h-full rounded-full transition-all" style="width: {{ $progressPercent }}%" :style="`background: ${activeTheme.primary}; width: {{ $progressPercent }}%;`"></div>
                </div>
            </section>

            <div>
                    @if(session('error'))
                        <div class="p-4 rounded-2xl border bg-red-50 text-red-600 border-red-100 mb-5">
                            {{ session('error') }}
                        </div>
                    @endif

                    @if(session('success'))
                        <div class="p-4 rounded-2xl border bg-green-50 text-green-700 border-green-100 mb-5">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if(!$user->dashboard_theme)
                        <section class="rounded-3xl p-6 md:p-8 border shadow-sm" :style="`background: ${activeTheme.card}; border-color: ${activeTheme.border}`">
                            <p class="text-sm font-semibold tracking-widest uppercase" :style="`color: ${activeTheme.muted}`">Passo 1</p>
                            <h2 class="text-3xl font-bold mt-2">Escolha o tema do painel</h2>
                            <p class="text-sm mt-2" :style="`color: ${activeTheme.muted}`">
                                Esse visual será usado nas telas internas onde você gerencia sua loja.
                            </p>

                            <form method="POST" action="{{ route('onboarding.panel') }}" class="mt-6 space-y-6">
                                @csrf

                                <div class="grid sm:grid-cols-2 xl:grid-cols-4 gap-4">
                                    @foreach($dashboardThemes as $key => $dashboardTheme)
                                        <label
                                            class="rounded-3xl border p-5 cursor-pointer transition"
                                            :style="selectedDashboardTheme === '{{ $key }}'
                                                ? `background: ${activeTheme.secondary}; border-color: ${activeTheme.primary}; box-shadow: 0 0 0 2px ${activeTheme.primary}33;`
                                                : `background: {{ $dashboardTheme['bg'] }}; border-color: {{ $dashboardTheme['border'] }}; color: {{ $dashboardTheme['text'] }};`"
                                        >
                                            <input type="radio" name="dashboard_theme" value="{{ $key }}" class="sr-only" x-model="selectedDashboardTheme">
                                            <div class="h-16 rounded-2xl mb-5" style="background: linear-gradient(135deg, {{ $dashboardTheme['primary'] }}, {{ $dashboardTheme['secondary'] }})"></div>
                                            <strong>{{ $dashboardTheme['name'] }}</strong>
                                            <div class="flex gap-2 mt-4">
                                                <span class="w-4 h-4 rounded-full border" style="background: {{ $dashboardTheme['primary'] }}"></span>
                                                <span class="w-4 h-4 rounded-full border" style="background: {{ $dashboardTheme['secondary'] }}"></span>
                                                <span class="w-4 h-4 rounded-full border" style="background: {{ $dashboardTheme['bg'] }}"></span>
                                                <span class="w-4 h-4 rounded-full border" style="background: {{ $dashboardTheme['text'] }}"></span>
                                            </div>
                                        </label>
                                    @endforeach
                                </div>

                                <x-input-error :messages="$errors->get('dashboard_theme')" class="mt-2" />

                                <button class="w-full md:w-auto text-white px-7 py-4 rounded-2xl font-bold" :style="`background: ${activeTheme.primary}`">
                                    Continuar para escolher o plano
                                </button>
                            </form>
                        </section>
                    @elseif(!$store && $currentStep === 2)
                        <section class="rounded-3xl p-6 md:p-8 border shadow-sm" :style="`background: ${activeTheme.card}; border-color: ${activeTheme.border}`">
                            <p class="text-sm font-semibold tracking-widest uppercase" :style="`color: ${activeTheme.muted}`">Passo 2</p>
                            <h2 class="text-3xl font-bold mt-2">Escolha como quer comecar</h2>
                            <p class="text-sm mt-2 max-w-2xl" :style="`color: ${activeTheme.muted}`">
                                O gratuito publica sua loja com link automatico. Nos planos pagos voce escolhe um link profissional e libera mais produtos.
                            </p>

                            @if($latestSubscription?->checkout_url && $latestSubscription->status === 'pending')
                                <div class="mt-6 rounded-3xl border p-5 flex flex-col md:flex-row md:items-center md:justify-between gap-4" :style="`background: ${activeTheme.secondary}; border-color: ${activeTheme.border}`">
                                    <div>
                                        <p class="font-bold">Pagamento em andamento</p>
                                        <p class="text-sm mt-1" :style="`color: ${activeTheme.muted}`">
                                            Continue o checkout do plano {{ $plans[$latestSubscription->plan]['name'] ?? $latestSubscription->plan }} para liberar a proxima etapa.
                                        </p>
                                    </div>

                                    <a href="{{ $latestSubscription->checkout_url }}" class="text-white px-6 py-3 rounded-2xl font-bold text-center" :style="`background: ${activeTheme.primary}`">
                                        Continuar pagamento
                                    </a>
                                </div>
                            @endif

                            <form method="POST" action="{{ route('onboarding.plan') }}" class="mt-6 space-y-6">
                                @csrf

                                <div class="grid md:grid-cols-2 xl:grid-cols-4 gap-4">
                                    @foreach($wizardPlanKeys as $planKey)
                                        @php($plan = $plans[$planKey])
                                        @php($isPaid = in_array($planKey, $paidPlanKeys, true))
                                        <label
                                            class="rounded-3xl border p-5 cursor-pointer transition relative flex flex-col"
                                            :style="selectedPlan === '{{ $planKey }}'
                                                ? `background: ${activeTheme.secondary}; border-color: ${activeTheme.primary}; box-shadow: 0 0 0 2px ${activeTheme.primary}33;`
                                                : `background: ${activeTheme.bg}; border-color: ${activeTheme.border};`"
                                        >
                                            <input type="radio" name="plan" value="{{ $planKey }}" class="sr-only" x-model="selectedPlan">

                                            <span
                                                x-show="selectedPlan === '{{ $planKey }}'"
                                                class="absolute right-4 top-4 w-8 h-8 rounded-full flex items-center justify-center text-white text-sm font-black"
                                                :style="`background: ${activeTheme.primary}`"
                                                style="display: none;"
                                            >
                                                ✓
                                            </span>

                                            <p class="text-xs font-black uppercase tracking-[0.2em]" :style="`color: ${activeTheme.muted}`">
                                                {{ $isPaid ? 'Pago' : 'Gratis' }}
                                            </p>
                                            <h3 class="text-2xl font-black mt-3">{{ $plan['name'] }}</h3>
                                            <p class="text-sm mt-2 min-h-[56px]" :style="`color: ${activeTheme.muted}`">{{ $plan['description'] }}</p>

                                            <div class="mt-5">
                                                @if($isPaid)
                                                    <div>
                                                        <strong class="text-3xl font-black">R$ {{ number_format($plan['price'], 2, ',', '.') }}</strong>
                                                        <span class="text-sm" :style="`color: ${activeTheme.muted}`">{{ $plan['period_label'] ?? 'por mes' }}</span>
                                                    </div>
                                                    <p class="text-xs font-bold mt-2" :style="`color: ${activeTheme.primary}`">
                                                        Anual R$ {{ number_format($plan['annual_price'], 2, ',', '.') }} com {{ number_format($plan['annual_discount_percent'] ?? 10, 0) }}% off
                                                    </p>
                                                @else
                                                    <strong class="text-3xl font-black">R$ 0</strong>
                                                    <p class="text-xs font-bold mt-2" :style="`color: ${activeTheme.primary}`">Sem cartao para comecar</p>
                                                @endif
                                            </div>

                                            <div class="mt-5 rounded-2xl px-4 py-3 text-sm font-bold" :style="`background: ${activeTheme.card}; color: ${activeTheme.text}`">
                                                Ate {{ $plan['limit'] }} produtos
                                            </div>
                                        </label>
                                    @endforeach
                                </div>

                                <x-input-error :messages="$errors->get('plan')" class="mt-2" />

                                <div
                                    x-show="selectedPlan !== 'free'"
                                    x-transition
                                    class="rounded-3xl border p-5 md:p-6"
                                    :style="`background: ${activeTheme.bg}; border-color: ${activeTheme.border}`"
                                    style="display: none;"
                                >
                                    <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-5">
                                        <div>
                                            <h3 class="text-2xl font-bold">Dados para ativar o plano</h3>
                                            <p class="text-sm mt-2 max-w-2xl" :style="`color: ${activeTheme.muted}`">
                                                Essas informacoes sao enviadas ao Asaas para criar o checkout. Depois do pagamento confirmado, seu plano e o link personalizado ficam liberados.
                                            </p>
                                        </div>

                                        <div class="grid grid-cols-2 gap-2 rounded-2xl border p-1" :style="`background: ${activeTheme.card}; border-color: ${activeTheme.border}`">
                                            <label class="rounded-xl px-4 py-3 text-center font-bold cursor-pointer" :style="selectedBillingPeriod === 'monthly' ? `background: ${activeTheme.primary}; color: white;` : `color: ${activeTheme.text};`">
                                                <input type="radio" name="billing_period" value="monthly" class="sr-only" x-model="selectedBillingPeriod">
                                                Mensal
                                            </label>
                                            <label class="rounded-xl px-4 py-3 text-center font-bold cursor-pointer" :style="selectedBillingPeriod === 'annual' ? `background: ${activeTheme.primary}; color: white;` : `color: ${activeTheme.text};`">
                                                <input type="radio" name="billing_period" value="annual" class="sr-only" x-model="selectedBillingPeriod">
                                                Anual -{{ number_format($plans['plus']['annual_discount_percent'] ?? 10, 0) }}%
                                            </label>
                                        </div>
                                    </div>

                                    <div class="grid md:grid-cols-2 gap-5 mt-6">
                                        <div>
                                            <label class="block text-sm font-semibold mb-2">Sobrenome</label>
                                            <input name="last_name" value="{{ old('last_name', $user->last_name) }}" class="w-full rounded-2xl border px-4 py-3" :style="`background: ${activeTheme.card}; border-color: ${activeTheme.border}`">
                                            <x-input-error :messages="$errors->get('last_name')" class="mt-2" />
                                        </div>

                                        <div>
                                            <label class="block text-sm font-semibold mb-2">WhatsApp *</label>
                                            <input name="phone" value="{{ old('phone', $user->phone) }}" placeholder="(00) 00000-0000" class="w-full rounded-2xl border px-4 py-3" :style="`background: ${activeTheme.card}; border-color: ${activeTheme.border}`">
                                            <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                                        </div>

                                        <div>
                                            <label class="block text-sm font-semibold mb-2">CPF ou CNPJ *</label>
                                            <input name="document" value="{{ old('document', $user->document) }}" placeholder="000.000.000-00" class="w-full rounded-2xl border px-4 py-3" :style="`background: ${activeTheme.card}; border-color: ${activeTheme.border}`">
                                            <x-input-error :messages="$errors->get('document')" class="mt-2" />
                                        </div>

                                        <div>
                                            <label class="block text-sm font-semibold mb-2">CEP *</label>
                                            <input name="zip_code" value="{{ old('zip_code', $user->zip_code) }}" placeholder="00000-000" class="w-full rounded-2xl border px-4 py-3" :style="`background: ${activeTheme.card}; border-color: ${activeTheme.border}`">
                                            <x-input-error :messages="$errors->get('zip_code')" class="mt-2" />
                                        </div>

                                        <div>
                                            <label class="block text-sm font-semibold mb-2">Rua / Logradouro *</label>
                                            <input name="address" value="{{ old('address', $user->address) }}" class="w-full rounded-2xl border px-4 py-3" :style="`background: ${activeTheme.card}; border-color: ${activeTheme.border}`">
                                            <x-input-error :messages="$errors->get('address')" class="mt-2" />
                                        </div>

                                        <div>
                                            <label class="block text-sm font-semibold mb-2">Numero *</label>
                                            <input name="address_number" value="{{ old('address_number', $user->address_number) }}" class="w-full rounded-2xl border px-4 py-3" :style="`background: ${activeTheme.card}; border-color: ${activeTheme.border}`">
                                            <x-input-error :messages="$errors->get('address_number')" class="mt-2" />
                                        </div>

                                        <div>
                                            <label class="block text-sm font-semibold mb-2">Complemento</label>
                                            <input name="address_complement" value="{{ old('address_complement', $user->address_complement) }}" placeholder="Opcional" class="w-full rounded-2xl border px-4 py-3" :style="`background: ${activeTheme.card}; border-color: ${activeTheme.border}`">
                                            <x-input-error :messages="$errors->get('address_complement')" class="mt-2" />
                                        </div>

                                        <div>
                                            <label class="block text-sm font-semibold mb-2">Bairro *</label>
                                            <input name="district" value="{{ old('district', $user->district) }}" class="w-full rounded-2xl border px-4 py-3" :style="`background: ${activeTheme.card}; border-color: ${activeTheme.border}`">
                                            <x-input-error :messages="$errors->get('district')" class="mt-2" />
                                        </div>

                                        <div>
                                            <label class="block text-sm font-semibold mb-2">Cidade *</label>
                                            <input name="city" value="{{ old('city', $user->city) }}" class="w-full rounded-2xl border px-4 py-3" :style="`background: ${activeTheme.card}; border-color: ${activeTheme.border}`">
                                            <x-input-error :messages="$errors->get('city')" class="mt-2" />
                                        </div>

                                        <div>
                                            <label class="block text-sm font-semibold mb-2">UF *</label>
                                            <input name="state" value="{{ old('state', $user->state) }}" maxlength="2" placeholder="SP" class="w-full rounded-2xl border px-4 py-3 uppercase" :style="`background: ${activeTheme.card}; border-color: ${activeTheme.border}`">
                                            <x-input-error :messages="$errors->get('state')" class="mt-2" />
                                        </div>
                                    </div>
                                </div>

                                <div class="flex flex-col sm:flex-row gap-3">
                                    <button
                                        class="w-full sm:w-auto text-white px-7 py-4 rounded-2xl font-bold"
                                        :style="`background: ${activeTheme.primary}`"
                                        @if(!$asaasReady) :disabled="selectedPlan !== 'free'" :class="selectedPlan !== 'free' ? 'opacity-60 cursor-not-allowed' : ''" @endif
                                    >
                                        <span x-text="selectedPlan === 'free' ? 'Continuar gratis' : 'Ir para pagamento'"></span>
                                    </button>

                                    <p class="text-sm self-center" :style="`color: ${activeTheme.muted}`">
                                        Acima de 200 produtos? Chame o suporte para um plano sob medida.
                                    </p>
                                </div>
                            </form>
                        </section>
                    @elseif(!$store)
                        <section class="rounded-3xl p-6 md:p-8 border shadow-sm" :style="`background: ${activeTheme.card}; border-color: ${activeTheme.border}`">
                            <p class="text-sm font-semibold tracking-widest uppercase" :style="`color: ${activeTheme.muted}`">Passo 3</p>
                            <h2 class="text-3xl font-bold mt-2">Configure sua loja</h2>
                            <p class="text-sm mt-2" :style="`color: ${activeTheme.muted}`">Essas informações aparecem na vitrine para seus clientes.</p>

                            <div class="mt-6 rounded-3xl border p-5 flex items-center gap-4" :style="`background: ${activeTheme.secondary}; border-color: ${activeTheme.border}`">
                                <div class="w-12 h-12 rounded-2xl flex items-center justify-center text-white font-bold" :style="`background: ${activeTheme.primary}`">
                                    {{ $canChooseCustomSlug ? '✓' : 'S' }}
                                </div>
                                <div>
                                    <p class="font-bold">
                                        {{ $canChooseCustomSlug ? 'Seu plano libera link personalizado' : 'Plano gratuito selecionado' }}
                                    </p>
                                    <p class="text-sm" :style="`color: ${activeTheme.muted}`">
                                        {{ $canChooseCustomSlug ? 'Escolha um endereco curto e facil de divulgar.' : 'O Shopla cria um link automatico para voce comecar sem travar.' }}
                                    </p>
                                </div>
                            </div>

                            @if(false)
                                <div class="mt-6 rounded-3xl border p-5" :style="`background: ${activeTheme.bg}; border-color: ${activeTheme.border}`">
                                    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-5">
                                        <div>
                                            <p class="text-sm font-bold" :style="`color: ${activeTheme.primary}`">Link personalizado</p>
                                            <h3 class="text-2xl font-bold mt-1">Quer escolher o endereco da sua loja?</h3>
                                            <p class="text-sm mt-2 max-w-2xl" :style="`color: ${activeTheme.muted}`">
                                                No plano gratuito o Shopla cria um link automatico. Nos planos pagos voce pode escolher um link curto e mais profissional.
                                            </p>
                                        </div>

                                        @if($latestSubscription?->checkout_url && $latestSubscription->status === 'pending')
                                            <a href="{{ $latestSubscription->checkout_url }}" class="text-white px-6 py-3 rounded-2xl font-bold text-center" :style="`background: ${activeTheme.primary}`">
                                                Continuar pagamento
                                            </a>
                                        @endif
                                    </div>

                                    <div class="grid md:grid-cols-3 gap-3 mt-5">
                                        @foreach($paidPlanKeys as $planKey)
                                            @php($plan = $plans[$planKey])
                                            <div class="rounded-2xl border p-4" :style="`background: ${activeTheme.card}; border-color: ${activeTheme.border}`">
                                                <div class="flex items-start justify-between gap-3">
                                                    <div>
                                                        <p class="font-bold">{{ $plan['name'] }}</p>
                                                        <p class="text-sm mt-1" :style="`color: ${activeTheme.muted}`">
                                                            {{ $plan['limit'] }} produtos
                                                        </p>
                                                    </div>
                                                    <div class="text-right">
                                                        <p class="font-black">R$ {{ number_format($plan['price'], 2, ',', '.') }}</p>
                                                        <p class="text-xs" :style="`color: ${activeTheme.muted}`">{{ $plan['period_label'] ?? 'por mes' }}</p>
                                                    </div>
                                                </div>

                                                <p class="text-xs font-semibold mt-3" :style="`color: ${activeTheme.muted}`">
                                                    Anual: R$ {{ number_format($plan['annual_price'], 2, ',', '.') }} com {{ number_format($plan['annual_discount_percent'] ?? 10, 0) }}% off
                                                </p>

                                                @if($asaasReady)
                                                    <div class="grid gap-2 mt-4">
                                                        <form method="POST" action="{{ route('plans.checkout', $planKey) }}">
                                                            @csrf
                                                            <input type="hidden" name="return_to" value="onboarding">
                                                            <input type="hidden" name="billing_period" value="monthly">
                                                            <button class="w-full rounded-2xl px-4 py-3 text-white font-bold" :style="`background: ${activeTheme.primary}`">
                                                                Mensal
                                                            </button>
                                                        </form>

                                                        <form method="POST" action="{{ route('plans.checkout', $planKey) }}">
                                                            @csrf
                                                            <input type="hidden" name="return_to" value="onboarding">
                                                            <input type="hidden" name="billing_period" value="annual">
                                                            <button class="w-full rounded-2xl px-4 py-3 border font-bold" :style="`border-color: ${activeTheme.primary}; color: ${activeTheme.primary}`">
                                                                Anual -{{ number_format($plan['annual_discount_percent'] ?? 10, 0) }}%
                                                            </button>
                                                        </form>
                                                    </div>
                                                @else
                                                    <button class="w-full mt-4 rounded-2xl px-4 py-3 text-white font-bold opacity-60" :style="`background: ${activeTheme.primary}`" disabled>
                                                        Pagamento em configuracao
                                                    </button>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @elseif(false)
                                <div class="mt-6 rounded-3xl border p-5 flex items-center gap-4" :style="`background: ${activeTheme.secondary}; border-color: ${activeTheme.border}`">
                                    <div class="w-12 h-12 rounded-2xl flex items-center justify-center text-white font-bold" :style="`background: ${activeTheme.primary}`">
                                        ✓
                                    </div>
                                    <div>
                                        <p class="font-bold">Seu plano libera link personalizado</p>
                                        <p class="text-sm" :style="`color: ${activeTheme.muted}`">Escolha um endereco curto e facil de divulgar.</p>
                                    </div>
                                </div>
                            @endif

                            <form method="POST" action="{{ route('onboarding.store') }}" class="mt-6 space-y-6">
                                @csrf

                                <div class="grid md:grid-cols-2 gap-5">
                                    <div>
                                        <label class="block text-sm font-semibold mb-2">Nome da loja</label>
                                        <input name="name" value="{{ old('name') }}" required placeholder="Ex: Eliana Bordados" class="w-full rounded-2xl border px-4 py-3" :style="`background: ${activeTheme.bg}; border-color: ${activeTheme.border}`">
                                        <p class="text-xs mt-2" :style="`color: ${activeTheme.muted}`">Esse nome aparece no topo da loja.</p>
                                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                                    </div>

                                    <div>
                                        <label class="block text-sm font-semibold mb-2">Link da loja</label>

                                        @if($canChooseCustomSlug)
                                            <input
                                                name="slug"
                                                x-model="slug"
                                                @input.debounce.500ms="checkSlug"
                                                placeholder="eliana-bordados"
                                                class="w-full rounded-2xl border px-4 py-3"
                                                :style="`background: ${activeTheme.bg}; border-color: ${activeTheme.border}`"
                                            >

                                            <p class="text-xs mt-2" :style="`color: ${activeTheme.muted}`">
                                                {{ url('/') }}/<span x-text="slug || 'sua-loja'"></span>
                                            </p>

                                            <p
                                                x-show="slugMessage"
                                                x-text="slugMessage"
                                                class="text-xs mt-2 font-semibold"
                                                :class="slugStatus === 'available' ? 'text-green-600' : (slugStatus === 'unavailable' ? 'text-red-500' : '')"
                                                style="display: none;"
                                            ></p>
                                        @else
                                            <div class="w-full rounded-2xl border px-4 py-3" :style="`background: ${activeTheme.bg}; border-color: ${activeTheme.border}`">
                                                <p class="font-semibold">Gerado automaticamente</p>
                                                <p class="text-xs mt-1" :style="`color: ${activeTheme.muted}`">
                                                    Exemplo: {{ url('/') }}/nome-da-sua-loja
                                                </p>
                                            </div>
                                        @endif

                                        <x-input-error :messages="$errors->get('slug')" class="mt-2" />
                                    </div>

                                    <div>
                                        <label class="block text-sm font-semibold mb-2">WhatsApp da loja</label>
                                        <input name="whatsapp" value="{{ old('whatsapp', $user->phone) }}" placeholder="(00) 00000-0000" class="w-full rounded-2xl border px-4 py-3" :style="`background: ${activeTheme.bg}; border-color: ${activeTheme.border}`">
                                        <p class="text-xs mt-2" :style="`color: ${activeTheme.muted}`">Será usado no botão de contato da vitrine.</p>
                                        <x-input-error :messages="$errors->get('whatsapp')" class="mt-2" />
                                    </div>

                                    <div>
                                        <label class="block text-sm font-semibold mb-2">Instagram</label>
                                        <input name="instagram" value="{{ old('instagram') }}" placeholder="@sualoja" class="w-full rounded-2xl border px-4 py-3" :style="`background: ${activeTheme.bg}; border-color: ${activeTheme.border}`">
                                        <p class="text-xs mt-2" :style="`color: ${activeTheme.muted}`">Opcional. Aparece abaixo do nome da loja.</p>
                                        <x-input-error :messages="$errors->get('instagram')" class="mt-2" />
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold mb-2">O que sua loja vende?</label>
                                    <textarea name="description" rows="3" placeholder="Ex: Produtos personalizados, bordados e presentes feitos sob encomenda." class="w-full rounded-2xl border px-4 py-3" :style="`background: ${activeTheme.bg}; border-color: ${activeTheme.border}`">{{ old('description') }}</textarea>
                                    <x-input-error :messages="$errors->get('description')" class="mt-2" />
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold mb-3">Escolha a aparência inicial da vitrine</label>
                                    <div class="grid sm:grid-cols-2 xl:grid-cols-4 gap-3">
                                        @foreach($storeThemes as $key => $storeTheme)
                                            <label
                                                class="rounded-2xl border p-4 cursor-pointer transition relative"
                                                :style="selectedStoreTheme === '{{ $key }}'
                                                    ? `background: {{ $storeTheme['background'] }}; border-color: {{ $storeTheme['primary'] }}; box-shadow: 0 0 0 2px {{ $storeTheme['primary'] }}33; color: {{ $storeTheme['text'] }};`
                                                    : `background: {{ $storeTheme['background'] }}; border-color: {{ $storeTheme['border'] }}; color: {{ $storeTheme['text'] }};`"
                                            >
                                                <input type="radio" name="store_theme" value="{{ $key }}" class="sr-only" x-model="selectedStoreTheme">
                                                <span
                                                    x-show="selectedStoreTheme === '{{ $key }}'"
                                                    class="absolute right-3 top-3 w-7 h-7 rounded-full flex items-center justify-center text-white text-sm font-bold"
                                                    style="background: {{ $storeTheme['primary'] }}"
                                                >
                                                    ✓
                                                </span>
                                                <div class="h-12 rounded-xl mb-3" style="background: linear-gradient(135deg, {{ $storeTheme['primary'] }}, {{ $storeTheme['secondary'] }})"></div>
                                                <strong>{{ $storeTheme['name'] }}</strong>
                                            </label>
                                        @endforeach
                                    </div>
                                    <x-input-error :messages="$errors->get('store_theme')" class="mt-2" />
                                </div>

                                <button class="w-full md:w-auto text-white px-7 py-4 rounded-2xl font-bold" :style="`background: ${activeTheme.primary}`">
                                    Criar loja e continuar
                                </button>
                            </form>
                        </section>
                    @elseif(!$store->onboarding_completed_at && $store->onboarding_step === 4)
                        <section class="rounded-3xl p-6 md:p-8 border shadow-sm" style="background: {{ $theme['card'] }}; border-color: {{ $theme['border'] }}">
                            <p class="text-sm font-semibold tracking-widest uppercase" style="color: {{ $theme['muted'] }}">Opcional</p>
                            <h2 class="text-3xl font-bold mt-2">Adicione categorias</h2>
                            <p class="text-sm mt-2" style="color: {{ $theme['muted'] }}">Categorias viram filtros na sua vitrine. Se ainda não souber, pule esta etapa.</p>

                            <form id="onboarding-categories-form" method="POST" action="{{ route('onboarding.categories') }}" class="mt-6">
                                @csrf
                                <div class="grid md:grid-cols-3 gap-4">
                                    @foreach([0, 1, 2, 3, 4, 5] as $index)
                                        <div>
                                            <label class="block text-sm font-semibold mb-2">Categoria {{ $index + 1 }}</label>
                                            <input name="categories[]" value="{{ old('categories.' . $index) }}" placeholder="{{ ['Roupas', 'Doces', 'Servicos', 'Artesanato', 'Bebe', 'Presentes'][$index] }}" class="w-full rounded-2xl border px-4 py-3" style="background: {{ $theme['bg'] }}; border-color: {{ $theme['border'] }}">
                                        </div>
                                    @endforeach
                                </div>
                            </form>

                            <div class="flex flex-col sm:flex-row gap-3 mt-6">
                                <button form="onboarding-categories-form" class="text-white px-7 py-4 rounded-2xl font-bold" style="background: {{ $theme['primary'] }}">
                                    Salvar e continuar
                                </button>

                                <form method="POST" action="{{ route('onboarding.categories.skip') }}">
                                    @csrf
                                    <button class="w-full px-7 py-4 rounded-2xl font-bold border" style="background: {{ $theme['card'] }}; border-color: {{ $theme['border'] }}; color: {{ $theme['muted'] }}">
                                        Pular por enquanto
                                    </button>
                                </form>
                            </div>
                        </section>
                    @elseif(!$store->onboarding_completed_at && $store->onboarding_step === 5)
                        <section class="rounded-3xl p-6 md:p-8 border shadow-sm" style="background: {{ $theme['card'] }}; border-color: {{ $theme['border'] }}">
                            <p class="text-sm font-semibold tracking-widest uppercase" style="color: {{ $theme['muted'] }}">Opcional</p>
                            <h2 class="text-3xl font-bold mt-2">Cadastre seu primeiro produto</h2>
                            <p class="text-sm mt-2" style="color: {{ $theme['muted'] }}">Um produto já deixa sua vitrine com cara de loja pronta. Você também pode fazer isso depois.</p>

                            <form id="onboarding-product-form" method="POST" action="{{ route('onboarding.product') }}" enctype="multipart/form-data" data-optimize-images class="mt-6 space-y-5">
                                @csrf
                                <div class="grid md:grid-cols-2 gap-5">
                                    <div>
                                        <label class="block text-sm font-semibold mb-2">Nome do produto</label>
                                        <input name="name" value="{{ old('name') }}" required placeholder="Ex: Toalha personalizada" class="w-full rounded-2xl border px-4 py-3" style="background: {{ $theme['bg'] }}; border-color: {{ $theme['border'] }}">
                                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                                    </div>

                                    <div>
                                        <label class="block text-sm font-semibold mb-2">Preço</label>
                                        <input name="price" type="number" step="0.01" min="0" value="{{ old('price') }}" required placeholder="35,00" class="w-full rounded-2xl border px-4 py-3" style="background: {{ $theme['bg'] }}; border-color: {{ $theme['border'] }}">
                                        <x-input-error :messages="$errors->get('price')" class="mt-2" />
                                    </div>

                                    <div>
                                        <label class="block text-sm font-semibold mb-2">Categorias</label>
                                        <div class="grid gap-2 rounded-2xl border p-3" style="background: {{ $theme['bg'] }}; border-color: {{ $theme['border'] }}">
                                            @forelse($categories as $category)
                                                <label class="flex items-center gap-3 rounded-xl px-3 py-2 border cursor-pointer" style="border-color: {{ $theme['border'] }}">
                                                    <input
                                                        type="checkbox"
                                                        name="category_ids[]"
                                                        value="{{ $category->id }}"
                                                        @checked(in_array((string) $category->id, old('category_ids', []), true) || old('category_id') == $category->id)
                                                    >
                                                    <span>{{ $category->name }}</span>
                                                </label>
                                            @empty
                                                <p class="text-sm" style="color: {{ $theme['muted'] }}">Voce pode pular e escolher depois.</p>
                                            @endforelse
                                        </div>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-semibold mb-2">Disponibilidade</label>
                                        <select name="availability_status" class="w-full rounded-2xl border px-4 py-3" style="background: {{ $theme['bg'] }}; border-color: {{ $theme['border'] }}">
                                            @foreach(\App\Models\Product::AVAILABILITY_STATUSES as $status => $label)
                                                <option value="{{ $status }}" @selected(old('availability_status', 'sob_encomenda') === $status)>{{ $label }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold mb-2">Descrição</label>
                                    <textarea name="description" rows="3" placeholder="Conte rapidamente o que é esse produto." class="w-full rounded-2xl border px-4 py-3" style="background: {{ $theme['bg'] }}; border-color: {{ $theme['border'] }}">{{ old('description') }}</textarea>
                                </div>

                                <div>
                                    <x-upload-dropzone
                                        name="image"
                                        label="Foto do produto"
                                        hint="Arraste uma imagem ou clique para escolher. Ela será otimizada automaticamente."
                                        :border="$theme['border']"
                                        :primary="$theme['primary']"
                                        :background="$theme['bg']"
                                        :muted="$theme['muted']"
                                    />
                                </div>
                            </form>

                            <div class="flex flex-col sm:flex-row gap-3 mt-6">
                                <button type="submit" form="onboarding-product-form" class="text-white px-7 py-4 rounded-2xl font-bold" style="background: {{ $theme['primary'] }}">
                                    Publicar produto
                                </button>

                                <form method="POST" action="{{ route('onboarding.product.skip') }}">
                                    @csrf
                                    <button class="w-full px-7 py-4 rounded-2xl font-bold border" style="background: {{ $theme['card'] }}; border-color: {{ $theme['border'] }}; color: {{ $theme['muted'] }}">
                                        Pular e ir para o painel
                                    </button>
                                </form>
                            </div>
                        </section>
                    @else
                        <section class="rounded-3xl p-6 md:p-8 border shadow-sm text-center" style="background: {{ $theme['card'] }}; border-color: {{ $theme['border'] }}">
                            <div class="w-16 h-16 rounded-3xl mx-auto flex items-center justify-center text-white text-2xl" style="background: {{ $theme['primary'] }}">
                                ✓
                            </div>

                            <h2 class="text-3xl font-bold mt-5">Sua loja está pronta</h2>
                            <p class="text-sm mt-2" style="color: {{ $theme['muted'] }}">Agora você pode abrir sua vitrine ou continuar ajustando tudo pelo painel.</p>

                            <div class="flex flex-col sm:flex-row justify-center gap-3 mt-7">
                                <a href="{{ route('store.public', $store->slug) }}" target="_blank" class="text-white px-7 py-4 rounded-2xl font-bold" style="background: {{ $theme['primary'] }}">
                                    Acessar loja
                                </a>

                                <form method="POST" action="{{ route('onboarding.finish') }}">
                                    @csrf
                                    <button class="w-full px-7 py-4 rounded-2xl font-bold border" style="background: {{ $theme['card'] }}; border-color: {{ $theme['border'] }}; color: {{ $theme['text'] }}">
                                        Ir para o painel
                                    </button>
                                </form>
                            </div>
                        </section>
                    @endif
            </div>
        </div>
    </main>

    <x-image-upload-optimizer />
</body>
</html>
