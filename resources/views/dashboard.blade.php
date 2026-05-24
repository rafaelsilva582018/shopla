@php
    $theme = $store->dashboardTheme();
    $storeUrl = route('store.public', $store->slug);
    $attentionTotal = $pendingOrders + $lowStockProducts + $outOfStockProducts;
    $user = auth()->user();
    $hour = now()->hour;

    if ($hour < 12) {
        $greeting = 'Bom dia';
        $greetingIcon = '☀️';
    } elseif ($hour < 18) {
        $greeting = 'Boa tarde';
        $greetingIcon = '🌤️';
    } else {
        $greeting = 'Boa noite';
        $greetingIcon = '🌙';
    }
@endphp

<x-app-layout>
    <div class="min-h-screen pb-24" style="background: {{ $theme['bg'] }}; color: {{ $theme['text'] }};">
        <div class="max-w-6xl mx-auto px-4 py-8 space-y-8">

            <header class="flex flex-col xl:flex-row xl:items-end xl:justify-between gap-5">
                <div x-data="dashboardLocation(@js($user->name), @js($user->city), @js($user->state), @js($greeting), @js($greetingIcon))" x-init="load()">
                    <p class="text-sm font-semibold tracking-widest uppercase" style="color: {{ $theme['muted'] }}">
                        Inicio
                    </p>

                    <h1 class="text-4xl md:text-5xl font-bold mt-2" style="font-family: serif;">
                        <span x-text="greeting"></span>, {{ $user->name }}
                        <span class="text-2xl align-middle" x-text="greetingIcon"></span>
                    </h1>

                    <p class="mt-3 max-w-2xl" style="color: {{ $theme['muted'] }}">
                        Aqui está o resumo da sua loja e o que precisa de atenção agora.
                    </p>

                    <div class="mt-5 flex flex-col sm:flex-row gap-3">
                        <div
                            class="inline-flex items-center gap-3 rounded-2xl border px-4 py-3"
                            style="background: {{ $theme['card'] }}; border-color: {{ $theme['border'] }}"
                        >
                            <span class="w-10 h-10 rounded-2xl flex items-center justify-center" style="background: {{ $theme['secondary'] }}; color: {{ $theme['primary'] }}">
                                <span x-text="weatherIcon"></span>
                            </span>

                            <div>
                                <p class="text-sm font-semibold" x-text="weatherTitle"></p>
                                <p class="text-xs" style="color: {{ $theme['muted'] }}" x-text="locationText"></p>
                            </div>
                        </div>

                        <div
                            class="inline-flex items-center gap-3 rounded-2xl border px-4 py-3"
                            style="background: {{ $theme['card'] }}; border-color: {{ $theme['border'] }}"
                        >
                            <span class="w-10 h-10 rounded-2xl flex items-center justify-center" style="background: {{ $theme['secondary'] }}; color: {{ $theme['primary'] }}">
                                🕒
                            </span>

                            <div>
                                <p class="text-sm font-semibold" x-text="clockText"></p>
                                <p class="text-xs" style="color: {{ $theme['muted'] }}" x-text="clockSubtitle"></p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="w-full xl:w-auto space-y-3" x-data="{ notificationsOpen: false }">
                    <div class="flex justify-end relative">
                        <button
                            type="button"
                            @click="notificationsOpen = !notificationsOpen"
                            class="relative w-12 h-12 rounded-2xl border shadow-sm flex items-center justify-center"
                            style="background: {{ $theme['card'] }}; border-color: {{ $theme['border'] }}; color: {{ $theme['text'] }}"
                            aria-label="Abrir notificacoes"
                        >
                            <x-dashboard-icon name="bell" class="w-5 h-5" />

                            @if(count($dashboardNotifications) > 0)
                                <span class="absolute -right-1 -top-1 min-w-6 h-6 px-1 rounded-full text-xs font-black text-white flex items-center justify-center" style="background: {{ $theme['primary'] }}">
                                    {{ count($dashboardNotifications) }}
                                </span>
                            @endif
                        </button>

                        <div
                            x-cloak
                            x-show="notificationsOpen"
                            x-transition
                            @click.outside="notificationsOpen = false"
                            class="absolute right-0 top-14 z-20 w-[min(92vw,380px)] rounded-3xl border p-3 shadow-2xl"
                            style="background: {{ $theme['card'] }}; border-color: {{ $theme['border'] }}"
                        >
                            <div class="px-3 py-2 flex items-center justify-between gap-3">
                                <div>
                                    <p class="text-xs font-bold uppercase tracking-widest" style="color: {{ $theme['muted'] }}">Notificacoes</p>
                                    <h3 class="font-bold">Ultimos avisos</h3>
                                </div>

                                <div class="flex items-center gap-2">
                                    @if(count($dashboardNotifications) > 0)
                                        <form method="POST" action="{{ route('notifications.clear') }}">
                                            @csrf
                                            @foreach($dashboardNotifications as $notification)
                                                <input type="hidden" name="notification_keys[]" value="{{ $notification['key'] }}">
                                            @endforeach

                                            <button
                                                type="submit"
                                                class="h-10 rounded-2xl px-3 text-xs font-black border"
                                                style="border-color: {{ $theme['border'] }}; color: {{ $theme['primary'] }}"
                                            >
                                                Limpar
                                            </button>
                                        </form>
                                    @endif

                                    <span class="w-10 h-10 rounded-2xl flex items-center justify-center" style="background: {{ $theme['secondary'] }}; color: {{ $theme['primary'] }}">
                                        <x-dashboard-icon name="bell" class="w-5 h-5" />
                                    </span>
                                </div>
                            </div>

                            <div class="mt-2 space-y-2">
                                @forelse($dashboardNotifications as $notification)
                                    <div class="flex gap-2 rounded-2xl border p-3 transition hover:-translate-y-0.5" style="border-color: {{ $theme['border'] }}">
                                        <a href="{{ $notification['href'] }}" class="flex min-w-0 flex-1 gap-3">
                                            <span class="w-10 h-10 rounded-2xl flex items-center justify-center shrink-0" style="background: {{ $theme['secondary'] }}; color: {{ $theme['primary'] }}">
                                                <x-dashboard-icon :name="$notification['icon']" class="w-5 h-5" />
                                            </span>

                                            <span class="min-w-0">
                                                <strong class="block text-sm">{{ $notification['title'] }}</strong>
                                                <span class="block text-xs mt-1" style="color: {{ $theme['muted'] }}">{{ $notification['text'] }}</span>
                                            </span>
                                        </a>

                                        <form method="POST" action="{{ route('notifications.dismiss') }}" class="shrink-0">
                                            @csrf
                                            @method('DELETE')
                                            <input type="hidden" name="notification_key" value="{{ $notification['key'] }}">

                                            <button
                                                type="submit"
                                                class="w-8 h-8 rounded-xl flex items-center justify-center text-sm font-black"
                                                style="background: {{ $theme['secondary'] }}; color: {{ $theme['primary'] }}"
                                                aria-label="Excluir notificacao"
                                            >
                                                &times;
                                            </button>
                                        </form>
                                    </div>
                                @empty
                                    <div class="rounded-2xl border p-4 text-sm" style="border-color: {{ $theme['border'] }}; color: {{ $theme['muted'] }}">
                                        Tudo certo por aqui. Nenhuma notificacao importante agora.
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    <div
                    class="w-full xl:w-auto rounded-3xl border p-2"
                    style="background: {{ $theme['card'] }}; border-color: {{ $theme['border'] }}"
                >
                    <div class="px-3 pt-2 pb-3">
                        <p class="text-xs font-semibold uppercase tracking-widest" style="color: {{ $theme['muted'] }}">
                            Endereço da loja
                        </p>
                        <p class="text-sm break-all mt-1" style="color: {{ $theme['text'] }}">
                            {{ $storeUrl }}
                        </p>
                    </div>

                    <div class="flex flex-col sm:flex-row gap-2">
                        <button
                            type="button"
                            onclick="navigator.clipboard.writeText('{{ $storeUrl }}'); const label = this.querySelector('[data-copy-label]'); label.innerText = 'Link copiado'; setTimeout(() => label.innerText = 'Copiar link da loja', 1800)"
                            class="px-5 py-3 rounded-2xl font-semibold border inline-flex items-center justify-center gap-2"
                            style="background: {{ $theme['card'] }}; color: {{ $theme['text'] }}; border-color: {{ $theme['border'] }}"
                        >
                            <x-dashboard-icon name="copy" class="w-5 h-5" />
                            <span data-copy-label>Copiar link da loja</span>
                        </button>

                        <a
                            href="{{ $storeUrl }}"
                            target="_blank"
                            class="px-5 py-3 rounded-2xl font-semibold text-white inline-flex items-center justify-center gap-2"
                            style="background: {{ $theme['text'] }}"
                        >
                            <x-dashboard-icon name="external" class="w-5 h-5" />
                            Acessar sua loja
                        </a>
                    </div>
                    </div>
                </div>
            </header>

            <section class="grid xl:grid-cols-[1.35fr_.65fr] gap-5">
                <div
                    class="rounded-3xl p-6 md:p-8 shadow-sm border relative overflow-hidden"
                    style="background: linear-gradient(135deg, {{ $theme['primary'] }}, {{ $theme['secondary'] }}); border-color: {{ $theme['border'] }}; color: white;"
                >
                    <div class="absolute -right-20 -top-20 w-64 h-64 rounded-full bg-white/20"></div>
                    <div class="absolute right-20 bottom-8 w-36 h-36 rounded-full bg-white/10"></div>
                    <div class="absolute left-1/2 -bottom-28 w-72 h-72 rounded-full bg-white/10"></div>

                    <div class="relative z-10 flex flex-col md:flex-row md:items-start md:justify-between gap-6">
                        <div>
                            <p class="font-semibold text-white/80 uppercase tracking-widest text-sm">
                                {{ $periodLabel }}
                            </p>

                            <h2 class="text-4xl md:text-5xl font-bold mt-3">
                                R$ {{ number_format($totalRevenue, 2, ',', '.') }}
                            </h2>

                            <p class="text-white/80 mt-2">
                                {{ $confirmedOrders }} pedido(s) pago(s) no período
                            </p>
                        </div>

                        <form method="GET" action="{{ route('dashboard') }}">
                            <select
                                name="period"
                                onchange="this.form.submit()"
                                class="rounded-2xl border border-white/30 bg-white/20 text-white px-4 py-3 text-sm font-semibold outline-none"
                            >
                                <option class="text-black" value="month" @selected($period === 'month')>
                                    Este mês
                                </option>

                                <option class="text-black" value="week" @selected($period === 'week')>
                                    Esta semana
                                </option>

                                <option class="text-black" value="today" @selected($period === 'today')>
                                    Hoje
                                </option>
                            </select>
                        </form>
                    </div>

                    <div class="relative z-10 grid sm:grid-cols-3 gap-3 mt-7">
                        <div class="rounded-2xl border border-white/25 bg-white/15 p-4">
                            <p class="text-white/75 text-sm">Pedidos</p>
                            <h3 class="text-3xl font-bold mt-1">{{ $totalOrders }}</h3>
                        </div>

                        <div class="rounded-2xl border border-white/25 bg-white/15 p-4">
                            <p class="text-white/75 text-sm">Ticket médio</p>
                            <h3 class="text-3xl font-bold mt-1">
                                R$ {{ number_format($averageTicket, 2, ',', '.') }}
                            </h3>
                        </div>

                        <div class="rounded-2xl border border-white/25 bg-white/15 p-4">
                            <p class="text-white/75 text-sm">Pendentes</p>
                            <h3 class="text-3xl font-bold mt-1">{{ $pendingOrders }}</h3>
                        </div>
                    </div>
                </div>

                <div
                    class="rounded-3xl p-6 shadow-sm border"
                    style="background: {{ $theme['card'] }}; border-color: {{ $theme['border'] }}"
                >
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <p class="text-sm font-semibold tracking-widest uppercase" style="color: {{ $theme['muted'] }}">
                                Atenção
                            </p>
                            <h2 class="text-4xl font-bold mt-2">{{ $attentionTotal }}</h2>
                        </div>

                        <span
                            class="w-14 h-14 rounded-2xl flex items-center justify-center font-bold"
                            style="background: {{ $theme['secondary'] }}; color: {{ $theme['primary'] }}"
                        >
                            <x-dashboard-icon name="alert" class="w-7 h-7" />
                        </span>
                    </div>

                    <div class="space-y-3 mt-6">
                        <a href="{{ route('orders.index', ['status' => 'pendentes']) }}" class="flex items-center justify-between rounded-2xl px-4 py-3 border" style="border-color: {{ $theme['border'] }}">
                            <span class="flex items-center gap-3">
                                <x-dashboard-icon name="receipt" class="w-5 h-5" />
                                Pedidos pendentes
                            </span>
                            <strong>{{ $pendingOrders }}</strong>
                        </a>

                        <a href="{{ route('stock.index', ['status' => 'baixo']) }}" class="flex items-center justify-between rounded-2xl px-4 py-3 border" style="border-color: {{ $theme['border'] }}">
                            <span class="flex items-center gap-3">
                                <x-dashboard-icon name="stock" class="w-5 h-5" />
                                Baixo estoque
                            </span>
                            <strong>{{ $lowStockProducts }}</strong>
                        </a>

                        <a href="{{ route('stock.index', ['status' => 'esgotado']) }}" class="flex items-center justify-between rounded-2xl px-4 py-3 border" style="border-color: {{ $theme['border'] }}">
                            <span class="flex items-center gap-3">
                                <x-dashboard-icon name="alert" class="w-5 h-5" />
                                Esgotados
                            </span>
                            <strong>{{ $outOfStockProducts }}</strong>
                        </a>
                    </div>
                </div>
            </section>

            <section>
                <div class="grid sm:grid-cols-2 xl:grid-cols-4 gap-4">
                    <a href="{{ route('products.create') }}" class="rounded-3xl p-5 border shadow-sm hover:-translate-y-0.5 transition" style="background: {{ $theme['card'] }}; border-color: {{ $theme['border'] }}">
                        <span class="w-12 h-12 rounded-2xl flex items-center justify-center font-bold mb-4" style="background: {{ $theme['secondary'] }}; color: {{ $theme['primary'] }}">
                            <x-dashboard-icon name="plus" class="w-6 h-6" />
                        </span>
                        <h3 class="font-bold text-lg">Novo produto</h3>
                        <p class="text-sm mt-1" style="color: {{ $theme['muted'] }}">Cadastre item no catálogo.</p>
                    </a>

                    <a href="{{ route('orders.index') }}" class="rounded-3xl p-5 border shadow-sm hover:-translate-y-0.5 transition" style="background: {{ $theme['card'] }}; border-color: {{ $theme['border'] }}">
                        <span class="w-12 h-12 rounded-2xl flex items-center justify-center font-bold mb-4" style="background: {{ $theme['secondary'] }}; color: {{ $theme['primary'] }}">
                            <x-dashboard-icon name="orders" class="w-6 h-6" />
                        </span>
                        <h3 class="font-bold text-lg">Ver pedidos</h3>
                        <p class="text-sm mt-1" style="color: {{ $theme['muted'] }}">Acompanhe vendas recentes.</p>
                    </a>

                    <a href="{{ route('stock.index') }}" class="rounded-3xl p-5 border shadow-sm hover:-translate-y-0.5 transition" style="background: {{ $theme['card'] }}; border-color: {{ $theme['border'] }}">
                        <span class="w-12 h-12 rounded-2xl flex items-center justify-center font-bold mb-4" style="background: {{ $theme['secondary'] }}; color: {{ $theme['primary'] }}">
                            <x-dashboard-icon name="stock" class="w-6 h-6" />
                        </span>
                        <h3 class="font-bold text-lg">Estoque</h3>
                        <p class="text-sm mt-1" style="color: {{ $theme['muted'] }}">Ajuste limites e disponibilidade.</p>
                    </a>

                    <a href="{{ route('store.edit') }}" class="rounded-3xl p-5 border shadow-sm hover:-translate-y-0.5 transition" style="background: {{ $theme['card'] }}; border-color: {{ $theme['border'] }}">
                        <span class="w-12 h-12 rounded-2xl flex items-center justify-center font-bold mb-4" style="background: {{ $theme['secondary'] }}; color: {{ $theme['primary'] }}">
                            <x-dashboard-icon name="store" class="w-6 h-6" />
                        </span>
                        <h3 class="font-bold text-lg">Minha loja</h3>
                        <p class="text-sm mt-1" style="color: {{ $theme['muted'] }}">Edite visual, contato e temas.</p>
                    </a>
                </div>
            </section>

            <section>
                <div class="flex items-center justify-between mb-4">
                    <h2 class="font-bold tracking-widest uppercase">Operação</h2>
                    <a href="{{ route('products.index') }}" class="font-semibold" style="color: {{ $theme['primary'] }}">Ver catálogo</a>
                </div>

                <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                    @foreach([
                        ['label' => 'Produtos', 'value' => $totalProducts, 'hint' => $activeProducts . ' ativo(s)', 'icon' => 'package'],
                        ['label' => 'Categorias', 'value' => $totalCategories, 'hint' => 'Organização da vitrine', 'icon' => 'category'],
                        ['label' => 'Pedidos', 'value' => $totalOrders, 'hint' => 'No período', 'icon' => 'receipt'],
                        ['label' => 'Cancelados', 'value' => $cancelledOrders, 'hint' => 'No período', 'icon' => 'cancel'],
                    ] as $metric)
                        <div class="rounded-3xl p-5 border shadow-sm" style="background: {{ $theme['card'] }}; border-color: {{ $theme['border'] }}">
                            <div class="flex items-start justify-between gap-4">
                                <p class="text-xs font-semibold tracking-widest uppercase" style="color: {{ $theme['muted'] }}">
                                    {{ $metric['label'] }}
                                </p>

                                <span class="w-10 h-10 rounded-2xl flex items-center justify-center" style="background: {{ $theme['secondary'] }}; color: {{ $theme['primary'] }}">
                                    <x-dashboard-icon :name="$metric['icon']" class="w-5 h-5" />
                                </span>
                            </div>
                            <h3 class="text-4xl font-bold mt-3">{{ $metric['value'] }}</h3>
                            <p class="text-sm mt-2" style="color: {{ $theme['muted'] }}">
                                {{ $metric['hint'] }}
                            </p>
                        </div>
                    @endforeach
                </div>
            </section>

            <section class="grid xl:grid-cols-[.95fr_1.05fr] gap-6">
                <div
                    class="rounded-3xl p-6 shadow-sm border"
                    style="background: {{ $theme['card'] }}; border-color: {{ $theme['border'] }}"
                >
                    <div class="flex items-center justify-between mb-5">
                        <h2 class="font-bold tracking-widest uppercase">Pedidos recentes</h2>
                        <a href="{{ route('orders.index') }}" class="font-semibold" style="color: {{ $theme['primary'] }}">Ver tudo</a>
                    </div>

                    <div class="space-y-3">
                        @forelse($latestOrders as $order)
                            <div class="rounded-2xl p-4 border flex items-center justify-between gap-4" style="border-color: {{ $theme['border'] }}">
                                <div class="min-w-0">
                                    <h3 class="font-bold truncate">{{ $order->customer_name }}</h3>
                                    <p class="text-sm mt-1" style="color: {{ $theme['muted'] }}">
                                        {{ $order->items->sum('quantity') }} item(ns) · {{ $order->created_at->format('d/m/Y H:i') }}
                                    </p>
                                </div>

                                <div class="text-right shrink-0">
                                    <span class="text-xs px-3 py-1 rounded-full font-semibold" style="background: {{ $theme['secondary'] }}; color: {{ $theme['primary'] }}">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                    <p class="font-bold mt-2">
                                        R$ {{ number_format($order->total, 2, ',', '.') }}
                                    </p>
                                </div>
                            </div>
                        @empty
                            <div class="rounded-2xl p-8 text-center border" style="border-color: {{ $theme['border'] }}; color: {{ $theme['muted'] }}">
                                Nenhum pedido ainda.
                            </div>
                        @endforelse
                    </div>
                </div>

                <div
                    class="rounded-3xl p-6 shadow-sm border"
                    style="background: {{ $theme['card'] }}; border-color: {{ $theme['border'] }}"
                >
                    <div class="flex items-center justify-between mb-5">
                        <h2 class="font-bold tracking-widest uppercase">Faturamento</h2>
                        <span class="text-sm" style="color: {{ $theme['muted'] }}">{{ $periodLabel }}</span>
                    </div>

                    <canvas id="revenueLineChart" height="140"></canvas>
                </div>
            </section>

            <section class="grid xl:grid-cols-2 gap-6">
                <div
                    class="rounded-3xl p-6 shadow-sm border"
                    style="background: {{ $theme['card'] }}; border-color: {{ $theme['border'] }}"
                >
                    <h2 class="font-bold tracking-widest uppercase mb-5">Pedidos por status</h2>

                    <div class="max-w-xs mx-auto">
                        <canvas id="ordersStatusChart"></canvas>
                    </div>
                </div>

                <div
                    class="rounded-3xl p-6 shadow-sm border"
                    style="background: {{ $theme['card'] }}; border-color: {{ $theme['border'] }}"
                >
                    <h2 class="font-bold tracking-widest uppercase mb-5">Link da vitrine</h2>

                    <p class="text-sm break-all rounded-2xl p-4 border" style="color: {{ $theme['muted'] }}; border-color: {{ $theme['border'] }}">
                        {{ $storeUrl }}
                    </p>

                    <div class="grid sm:grid-cols-2 gap-3 mt-4">
                        <button
                            type="button"
                            onclick="navigator.clipboard.writeText('{{ $storeUrl }}'); this.innerText = 'Copiado'; setTimeout(() => this.innerText = 'Copiar link', 1800)"
                            class="px-5 py-3 rounded-2xl font-semibold border"
                            style="background: {{ $theme['card'] }}; color: {{ $theme['primary'] }}; border-color: {{ $theme['border'] }}"
                        >
                            Copiar link
                        </button>

                        <a
                            href="{{ $storeUrl }}"
                            target="_blank"
                            class="text-center px-5 py-3 rounded-2xl font-semibold text-white"
                            style="background: {{ $theme['primary'] }}"
                        >
                            Abrir vitrine
                        </a>
                    </div>
                </div>
            </section>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        function weatherWidget(city, state) {
            return {
                icon: '📍',
                title: city ? 'Carregando clima...' : 'Clima da sua cidade',
                subtitle: city ? `${city}${state ? ' - ' + state : ''}` : 'Adicione cidade e UF no perfil',
                states: {
                    AC: 'Acre',
                    AL: 'Alagoas',
                    AP: 'Amapá',
                    AM: 'Amazonas',
                    BA: 'Bahia',
                    CE: 'Ceará',
                    DF: 'Distrito Federal',
                    ES: 'Espírito Santo',
                    GO: 'Goiás',
                    MA: 'Maranhão',
                    MT: 'Mato Grosso',
                    MS: 'Mato Grosso do Sul',
                    MG: 'Minas Gerais',
                    PA: 'Pará',
                    PB: 'Paraíba',
                    PR: 'Paraná',
                    PE: 'Pernambuco',
                    PI: 'Piauí',
                    RJ: 'Rio de Janeiro',
                    RN: 'Rio Grande do Norte',
                    RS: 'Rio Grande do Sul',
                    RO: 'Rondônia',
                    RR: 'Roraima',
                    SC: 'Santa Catarina',
                    SP: 'São Paulo',
                    SE: 'Sergipe',
                    TO: 'Tocantins',
                },
                async load() {
                    if (!city) {
                        return;
                    }

                    try {
                        const place = encodeURIComponent(city);
                        const geoResponse = await fetch(`https://geocoding-api.open-meteo.com/v1/search?name=${place}&count=10&language=pt&format=json&country_code=BR`);
                        const geoData = await geoResponse.json();
                        const expectedState = state ? this.states[String(state).toUpperCase()] : null;
                        const location = expectedState
                            ? geoData.results?.find((result) => result.admin1 === expectedState) ?? geoData.results?.[0]
                            : geoData.results?.[0];

                        if (!location) {
                            this.icon = '📍';
                            this.title = 'Cidade não encontrada';
                            this.subtitle = 'Confira sua cidade no perfil';
                            return;
                        }

                        const weatherResponse = await fetch(`https://api.open-meteo.com/v1/forecast?latitude=${location.latitude}&longitude=${location.longitude}&current=temperature_2m,weather_code&timezone=auto`);
                        const weatherData = await weatherResponse.json();
                        const current = weatherData.current;
                        const code = current?.weather_code;

                        this.icon = [0, 1].includes(code) ? '☀️' : [2, 3].includes(code) ? '⛅' : [45, 48].includes(code) ? '🌫️' : code >= 51 && code <= 67 ? '🌧️' : code >= 80 && code <= 82 ? '🌦️' : '🌤️';
                        this.title = `${Math.round(current.temperature_2m)}°C agora`;
                        this.subtitle = `${city}${state ? ' - ' + state : ''}`;
                    } catch (error) {
                        this.icon = '🌤️';
                        this.title = 'Clima indisponível';
                        this.subtitle = 'Tente novamente mais tarde';
                    }
                }
            }
        }

        function dashboardLocation(userName, city, state, fallbackGreeting, fallbackIcon) {
            return {
                greeting: fallbackGreeting,
                greetingIcon: fallbackIcon,
                weatherIcon: '📍',
                weatherTitle: city ? 'Carregando clima...' : 'Clima da sua cidade',
                locationText: city ? `${city}${state ? ' - ' + state : ''}` : 'Adicione cidade e UF no perfil',
                clockText: '--:--',
                clockSubtitle: city ? 'Hora local' : 'Relógio da cidade',
                timezone: 'America/Sao_Paulo',
                states: {
                    AC: 'Acre',
                    AL: 'Alagoas',
                    AP: 'Amapá',
                    AM: 'Amazonas',
                    BA: 'Bahia',
                    CE: 'Ceará',
                    DF: 'Distrito Federal',
                    ES: 'Espírito Santo',
                    GO: 'Goiás',
                    MA: 'Maranhão',
                    MT: 'Mato Grosso',
                    MS: 'Mato Grosso do Sul',
                    MG: 'Minas Gerais',
                    PA: 'Pará',
                    PB: 'Paraíba',
                    PR: 'Paraná',
                    PE: 'Pernambuco',
                    PI: 'Piauí',
                    RJ: 'Rio de Janeiro',
                    RN: 'Rio Grande do Norte',
                    RS: 'Rio Grande do Sul',
                    RO: 'Rondônia',
                    RR: 'Roraima',
                    SC: 'Santa Catarina',
                    SP: 'São Paulo',
                    SE: 'Sergipe',
                    TO: 'Tocantins',
                },
                setGreetingByHour(hour) {
                    if (hour < 12) {
                        this.greeting = 'Bom dia';
                        this.greetingIcon = '☀️';
                    } else if (hour < 18) {
                        this.greeting = 'Boa tarde';
                        this.greetingIcon = '🌤️';
                    } else {
                        this.greeting = 'Boa noite';
                        this.greetingIcon = '🌙';
                    }
                },
                updateClock() {
                    const now = new Date();
                    this.clockText = new Intl.DateTimeFormat('pt-BR', {
                        hour: '2-digit',
                        minute: '2-digit',
                        timeZone: this.timezone,
                    }).format(now);

                    const hour = Number(new Intl.DateTimeFormat('pt-BR', {
                        hour: '2-digit',
                        hour12: false,
                        timeZone: this.timezone,
                    }).format(now));

                    this.setGreetingByHour(hour);
                    this.clockSubtitle = city ? `${city}${state ? ' - ' + state : ''}` : 'Relógio da cidade';
                },
                async load() {
                    this.updateClock();

                    if (!city) {
                        return;
                    }

                    try {
                        const place = encodeURIComponent(city);
                        const geoResponse = await fetch(`https://geocoding-api.open-meteo.com/v1/search?name=${place}&count=10&language=pt&format=json&country_code=BR`);
                        const geoData = await geoResponse.json();
                        const expectedState = state ? this.states[String(state).toUpperCase()] : null;
                        const location = expectedState
                            ? geoData.results?.find((result) => result.admin1 === expectedState) ?? geoData.results?.[0]
                            : geoData.results?.[0];

                        if (!location) {
                            this.weatherIcon = '📍';
                            this.weatherTitle = 'Cidade não encontrada';
                            this.locationText = 'Confira sua cidade no perfil';
                            return;
                        }

                        this.timezone = location.timezone || this.timezone;
                        this.updateClock();
                        setInterval(() => this.updateClock(), 30000);

                        const weatherResponse = await fetch(`https://api.open-meteo.com/v1/forecast?latitude=${location.latitude}&longitude=${location.longitude}&current=temperature_2m,weather_code&timezone=auto`);
                        const weatherData = await weatherResponse.json();
                        const current = weatherData.current;
                        const code = current?.weather_code;

                        this.timezone = weatherData.timezone || this.timezone;
                        this.updateClock();
                        this.weatherIcon = [0, 1].includes(code) ? '☀️' : [2, 3].includes(code) ? '⛅' : [45, 48].includes(code) ? '🌫️' : code >= 51 && code <= 67 ? '🌧️' : code >= 80 && code <= 82 ? '🌦️' : '🌤️';
                        this.weatherTitle = `${Math.round(current.temperature_2m)}°C agora`;
                        this.locationText = `${city}${state ? ' - ' + state : ''}`;
                    } catch (error) {
                        this.weatherIcon = '🌤️';
                        this.weatherTitle = 'Clima indisponível';
                        this.locationText = 'Tente novamente mais tarde';
                    }
                }
            }
        }

        const ordersStatusCtx = document.getElementById('ordersStatusChart');

        if (ordersStatusCtx) {
            new Chart(ordersStatusCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Pendentes', 'Pagos', 'Cancelados'],
                    datasets: [{
                        data: [
                            {{ $pendingOrders }},
                            {{ $confirmedOrders }},
                            {{ $cancelledOrders }}
                        ],
                        backgroundColor: [
                            '{{ $theme['primary'] }}',
                            '#22c55e',
                            '#ef4444'
                        ],
                        borderWidth: 0
                    }]
                },
                options: {
                    cutout: '72%',
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });
        }

        const revenueLineCtx = document.getElementById('revenueLineChart');

        if (revenueLineCtx) {
            new Chart(revenueLineCtx, {
                type: 'line',
                data: {
                    labels: @json($chartLabels),
                    datasets: [{
                        label: 'Faturamento',
                        data: @json($chartRevenue),
                        tension: 0.35,
                        fill: true,
                        borderColor: '{{ $theme['primary'] }}',
                        backgroundColor: '{{ $theme['secondary'] }}',
                        pointBackgroundColor: '{{ $theme['primary'] }}',
                        pointBorderColor: '{{ $theme['primary'] }}',
                        pointRadius: 4
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        }
    </script>
</x-app-layout>
