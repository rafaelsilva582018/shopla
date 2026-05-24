@php
    $theme = $store->dashboardTheme();
@endphp

<x-app-layout>
    <div class="min-h-screen pb-24" style="background: {{ $theme['bg'] }}; color: {{ $theme['text'] }};">
        <div class="max-w-6xl mx-auto px-4 py-8 space-y-8">

            <header class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-5">
                <div>
                    <p class="text-sm font-semibold tracking-widest uppercase" style="color: {{ $theme['muted'] }}">
                        Ranking
                    </p>

                    <h1 class="text-4xl md:text-5xl font-bold mt-2" style="font-family: serif;">
                        Mais vendidos
                    </h1>

                    <p class="mt-3 max-w-2xl" style="color: {{ $theme['muted'] }}">
                        Veja quais produtos mais venderam e quais mais puxaram faturamento.
                    </p>
                </div>

                <form method="GET" action="{{ route('rankings.index') }}">
                    <select
                        name="period"
                        onchange="this.form.submit()"
                        class="rounded-2xl border px-5 py-3 font-semibold outline-none"
                        style="background: {{ $theme['card'] }}; border-color: {{ $theme['border'] }}; color: {{ $theme['text'] }}"
                    >
                        <option value="month" @selected($period === 'month')>Este mês</option>
                        <option value="week" @selected($period === 'week')>Esta semana</option>
                        <option value="today" @selected($period === 'today')>Hoje</option>
                        <option value="all" @selected($period === 'all')>Todos</option>
                    </select>
                </form>
            </header>

            <section class="grid lg:grid-cols-[1.2fr_.8fr] gap-5">
                <div
                    class="rounded-3xl p-6 md:p-8 shadow-sm border relative overflow-hidden text-white"
                    style="background: linear-gradient(135deg, {{ $theme['primary'] }}, {{ $theme['secondary'] }}); border-color: {{ $theme['border'] }};"
                >
                    <div class="absolute -right-16 -top-16 w-56 h-56 rounded-full bg-white/20"></div>
                    <div class="absolute left-1/2 -bottom-28 w-72 h-72 rounded-full bg-white/10"></div>

                    <div class="relative z-10">
                        <p class="font-semibold text-white/80 uppercase tracking-widest text-sm">
                            Campeão de vendas - {{ $periodLabel }}
                        </p>

                        @if($topProduct)
                            <div class="flex flex-col sm:flex-row sm:items-center gap-5 mt-6">
                                @if($topProduct->image)
                                    <img
                                        src="{{ asset('storage/' . $topProduct->image) }}"
                                        class="w-24 h-24 rounded-3xl object-cover border border-white/30"
                                        alt="{{ $topProduct->name }}"
                                    >
                                @else
                                    <div class="w-24 h-24 rounded-3xl bg-white/20 border border-white/30 flex items-center justify-center">
                                        <x-dashboard-icon name="package" class="w-10 h-10" />
                                    </div>
                                @endif

                                <div>
                                    <h2 class="text-3xl md:text-4xl font-bold">
                                        {{ $topProduct->name }}
                                    </h2>

                                    <p class="text-white/80 mt-2">
                                        {{ (int) $topProduct->sold_quantity }} unidade(s) vendida(s)
                                    </p>
                                </div>
                            </div>
                        @else
                            <h2 class="text-3xl md:text-4xl font-bold mt-6">
                                Nenhuma venda ainda
                            </h2>

                            <p class="text-white/80 mt-2">
                                Assim que os pedidos entrarem, o ranking aparece aqui.
                            </p>
                        @endif
                    </div>
                </div>

                <div class="grid sm:grid-cols-2 lg:grid-cols-1 gap-4">
                    <div class="rounded-3xl p-6 border shadow-sm" style="background: {{ $theme['card'] }}; border-color: {{ $theme['border'] }}">
                        <p class="text-xs font-semibold tracking-widest uppercase" style="color: {{ $theme['muted'] }}">
                            Itens vendidos
                        </p>
                        <h3 class="text-4xl font-bold mt-3">{{ $totalSold }}</h3>
                        <p class="text-sm mt-2" style="color: {{ $theme['muted'] }}">{{ $periodLabel }}</p>
                    </div>

                    <div class="rounded-3xl p-6 border shadow-sm" style="background: {{ $theme['card'] }}; border-color: {{ $theme['border'] }}">
                        <p class="text-xs font-semibold tracking-widest uppercase" style="color: {{ $theme['muted'] }}">
                            Faturamento
                        </p>
                        <h3 class="text-4xl font-bold mt-3">
                            R$ {{ number_format($totalRevenue, 2, ',', '.') }}
                        </h3>
                        <p class="text-sm mt-2" style="color: {{ $theme['muted'] }}">Sem pedidos cancelados</p>
                    </div>
                </div>
            </section>

            <section>
                <div class="flex items-center justify-between mb-4">
                    <h2 class="font-bold tracking-widest uppercase">Lista dos produtos</h2>
                    <a href="{{ route('products.index') }}" class="font-semibold" style="color: {{ $theme['primary'] }}">
                        Ver catálogo
                    </a>
                </div>

                <div class="space-y-3">
                    @forelse($ranking as $index => $item)
                        <div
                            class="rounded-3xl p-4 md:p-5 border shadow-sm grid md:grid-cols-[auto_1fr_auto_auto] gap-4 md:items-center"
                            style="background: {{ $theme['card'] }}; border-color: {{ $theme['border'] }}"
                        >
                            <div
                                class="w-12 h-12 rounded-2xl flex items-center justify-center font-bold"
                                style="background: {{ $index === 0 ? $theme['primary'] : $theme['secondary'] }}; color: {{ $index === 0 ? '#ffffff' : $theme['primary'] }}"
                            >
                                #{{ $index + 1 }}
                            </div>

                            <div class="flex items-center gap-4 min-w-0">
                                @if($item->image)
                                    <img
                                        src="{{ asset('storage/' . $item->image) }}"
                                        class="w-16 h-16 rounded-2xl object-cover"
                                        alt="{{ $item->name }}"
                                    >
                                @else
                                    <div class="w-16 h-16 rounded-2xl flex items-center justify-center" style="background: {{ $theme['secondary'] }}; color: {{ $theme['primary'] }}">
                                        <x-dashboard-icon name="package" class="w-7 h-7" />
                                    </div>
                                @endif

                                <div class="min-w-0">
                                    <h3 class="font-bold text-lg truncate">{{ $item->name }}</h3>
                                    <p class="text-sm mt-1" style="color: {{ $theme['muted'] }}">
                                        Apareceu em {{ $item->orders_count }} pedido(s)
                                    </p>
                                </div>
                            </div>

                            <div class="md:text-right">
                                <p class="text-xs font-semibold tracking-widest uppercase" style="color: {{ $theme['muted'] }}">
                                    Vendidos
                                </p>
                                <p class="text-2xl font-bold">{{ (int) $item->sold_quantity }}</p>
                            </div>

                            <div class="md:text-right">
                                <p class="text-xs font-semibold tracking-widest uppercase" style="color: {{ $theme['muted'] }}">
                                    Total
                                </p>
                                <p class="text-2xl font-bold">
                                    R$ {{ number_format($item->total_revenue, 2, ',', '.') }}
                                </p>
                            </div>
                        </div>
                    @empty
                        <div class="rounded-3xl p-10 text-center border" style="background: {{ $theme['card'] }}; border-color: {{ $theme['border'] }}; color: {{ $theme['muted'] }}">
                            Nenhum produto vendido nesse período.
                        </div>
                    @endforelse
                </div>
            </section>

        </div>
    </div>
</x-app-layout>
