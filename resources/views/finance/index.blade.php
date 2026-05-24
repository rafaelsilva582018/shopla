@php
    $theme = $store->dashboardTheme();

    $ordersCount = $orders->count();
    $averageTicket = $ordersCount > 0 ? $totalRevenue / $ordersCount : 0;
    $receivedPercent = $totalRevenue > 0 ? round(($received / $totalRevenue) * 100) : 0;

    $pendingOrders = $orders->whereIn('status', ['pendente', 'novo', 'em andamento']);
    $paidOrders = $orders->whereIn('status', ['concluído', 'pago', 'confirmado']);
@endphp

<x-app-layout>
    <div class="min-h-screen pb-24" style="background: {{ $theme['bg'] }}; color: {{ $theme['text'] }};">
        <div class="max-w-6xl mx-auto px-4 py-8">

            <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4 mb-8">
                <div>
                    <p class="text-sm font-semibold tracking-widest" style="color: {{ $theme['muted'] }}">
                        FINANCEIRO
                    </p>

                    <h1 class="text-4xl font-bold mt-1" style="font-family: serif;">
                        Controle financeiro 💰
                    </h1>

                    <p class="mt-2" style="color: {{ $theme['muted'] }}">
                        Acompanhe faturamento, recebimentos e pedidos em aberto.
                    </p>
                </div>

                <button
                    type="button"
                    onclick="window.print()"
                    class="px-5 py-3 rounded-2xl font-semibold border"
                    style="background: {{ $theme['card'] }}; color: {{ $theme['primary'] }}; border-color: {{ $theme['border'] }}"
                >
                    Exportar / imprimir
                </button>
            </div>

            <div
                class="rounded-[2rem] p-6 md:p-8 shadow-xl text-white mb-8 relative overflow-hidden"
                style="background: linear-gradient(135deg, {{ $theme['primary'] }}, #b58cff);"
            >
                <div class="absolute -right-16 -top-16 w-52 h-52 rounded-full bg-white/20"></div>
                <div class="absolute right-14 bottom-8 w-32 h-32 rounded-full bg-white/10"></div>

                <div class="relative z-10">
                    <div class="flex items-center justify-between mb-6">
                        <p class="font-semibold opacity-90">RESUMO GERAL</p>
                        <span class="text-sm bg-white/20 px-3 py-1 rounded-full">
                            {{ now()->format('d/m/Y') }}
                        </span>
                    </div>

                    <div class="grid md:grid-cols-3 gap-4">
                        <div class="rounded-2xl border border-white/30 bg-white/10 p-5">
                            <p class="text-white/80 text-sm">Faturamento</p>
                            <h3 class="text-3xl font-bold mt-2">
                                R$ {{ number_format($totalRevenue, 2, ',', '.') }}
                            </h3>
                            <p class="text-white/70 text-sm mt-2">{{ $ordersCount }} pedido(s)</p>
                        </div>

                        <div class="rounded-2xl border border-white/30 bg-white/10 p-5">
                            <p class="text-white/80 text-sm">Recebido</p>
                            <h3 class="text-3xl font-bold mt-2">
                                R$ {{ number_format($received, 2, ',', '.') }}
                            </h3>
                            <p class="text-white/70 text-sm mt-2">{{ $receivedPercent }}% do total</p>
                        </div>

                        <div class="rounded-2xl border border-white/30 bg-white/10 p-5">
                            <p class="text-white/80 text-sm">A receber</p>
                            <h3 class="text-3xl font-bold mt-2">
                                R$ {{ number_format($toReceive, 2, ',', '.') }}
                            </h3>
                            <p class="text-white/70 text-sm mt-2">{{ $pendingOrders->count() }} em aberto</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid md:grid-cols-3 gap-4 mb-8">
                <div class="rounded-3xl p-5 border shadow-sm" style="background: {{ $theme['card'] }}; border-color: {{ $theme['border'] }}">
                    <p class="text-sm font-semibold" style="color: {{ $theme['muted'] }}">TICKET MÉDIO</p>
                    <h2 class="text-3xl font-bold mt-2">
                        R$ {{ number_format($averageTicket, 2, ',', '.') }}
                    </h2>
                </div>

                <div class="rounded-3xl p-5 border shadow-sm" style="background: {{ $theme['card'] }}; border-color: {{ $theme['border'] }}">
                    <p class="text-sm font-semibold" style="color: {{ $theme['muted'] }}">PEDIDOS PAGOS</p>
                    <h2 class="text-3xl font-bold mt-2">
                        {{ $paidOrders->count() }}
                    </h2>
                </div>

                <div class="rounded-3xl p-5 border shadow-sm" style="background: {{ $theme['card'] }}; border-color: {{ $theme['border'] }}">
                    <p class="text-sm font-semibold" style="color: {{ $theme['muted'] }}">PEDIDOS EM ABERTO</p>
                    <h2 class="text-3xl font-bold mt-2">
                        {{ $pendingOrders->count() }}
                    </h2>
                </div>
            </div>

            <section class="mb-8">
                <div class="flex items-center justify-between mb-3">
                    <h2 class="font-bold tracking-widest">SAÚDE FINANCEIRA</h2>
                    <span style="color: {{ $theme['muted'] }}" class="text-sm">
                        Recebido x total vendido
                    </span>
                </div>

                <div class="rounded-3xl p-6 border shadow-sm" style="background: {{ $theme['card'] }}; border-color: {{ $theme['border'] }}">
                    <div class="flex justify-between mb-3">
                        <span class="font-semibold">Recebido</span>
                        <span class="font-bold" style="color: {{ $theme['primary'] }}">{{ $receivedPercent }}%</span>
                    </div>

                    <div class="w-full h-4 rounded-full overflow-hidden" style="background: {{ $theme['secondary'] }}">
                        <div
                            class="h-full rounded-full"
                            style="width: {{ $receivedPercent }}%; background: {{ $theme['primary'] }}"
                        ></div>
                    </div>
                </div>
            </section>

            <section>
                <div class="flex items-center justify-between mb-3">
                    <h2 class="font-bold tracking-widest">MOVIMENTAÇÕES</h2>
                    <span class="text-sm" style="color: {{ $theme['muted'] }}">
                        {{ $ordersCount }} registro(s)
                    </span>
                </div>

                <div class="space-y-3">
                    @forelse($orders as $order)
                        <div
                            class="rounded-3xl p-5 border shadow-sm"
                            style="background: {{ $theme['card'] }}; border-color: {{ $theme['border'] }}"
                        >
                            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                                <div>
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <h3 class="font-bold text-lg">
                                            {{ $order->customer_name }}
                                        </h3>

                                        <span
                                            class="text-xs px-3 py-1 rounded-full font-semibold"
                                            style="background: {{ $theme['secondary'] }}; color: {{ $theme['primary'] }}"
                                        >
                                            {{ ucfirst($order->status) }}
                                        </span>
                                    </div>

                                    <p class="text-sm mt-1" style="color: {{ $theme['muted'] }}">
                                        {{ $order->created_at->format('d/m/Y H:i') }}
                                    </p>
                                </div>

                                <div class="text-left md:text-right">
                                    <p class="text-sm" style="color: {{ $theme['muted'] }}">Valor do pedido</p>
                                    <strong class="text-xl" style="color: {{ $theme['primary'] }}">
                                        R$ {{ number_format($order->total, 2, ',', '.') }}
                                    </strong>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div
                            class="rounded-3xl p-10 text-center border"
                            style="background: {{ $theme['card'] }}; border-color: {{ $theme['border'] }}; color: {{ $theme['muted'] }}"
                        >
                            <div class="text-5xl mb-3">💰</div>
                            <p class="text-lg font-semibold">
                                Nenhuma movimentação ainda.
                            </p>
                        </div>
                    @endforelse
                </div>
            </section>

        </div>
    </div>
</x-app-layout>
