@php
    $store = auth()->user()->store;
    $theme = $store->dashboardTheme();

    $statusFilter = $statusFilter ?? request('status', 'todos');

    $allOrders = $store->orders()->get();

    $totalOrders = $allOrders->count();
    $pendingOrders = $allOrders->whereIn('status', ['pendente', 'novo'])->count();
    $andamentoOrders = $allOrders->where('status', 'em andamento')->count();
    $finishedOrders = $allOrders->whereIn('status', ['concluído', 'pago', 'confirmado'])->count();
    $cancelledOrders = $allOrders->where('status', 'cancelado')->count();
@endphp

<x-app-layout>
    <div
        class="min-h-screen pb-24"
        style="background: {{ $theme['bg'] }}; color: {{ $theme['text'] }};"
    >
        <div class="max-w-6xl mx-auto px-4 py-8">

            <div class="mb-8">
                <p class="text-sm font-semibold tracking-widest" style="color: {{ $theme['muted'] }}">
                    PEDIDOS
                </p>

                <h1 class="text-4xl font-bold mt-1" style="font-family: serif;">
                    Suas vendas 🛍️
                </h1>

                <p class="mt-2" style="color: {{ $theme['muted'] }}">
                    Acompanhe os pedidos recebidos pela vitrine.
                </p>
            </div>

            @if(session('success'))
                <div
                    class="p-4 rounded-2xl mb-6 border"
                    style="background: {{ $theme['secondary'] }}; color: {{ $theme['primary'] }}; border-color: {{ $theme['border'] }}"
                >
                    {{ session('success') }}
                </div>
            @endif

            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
                <div class="rounded-3xl p-5 border shadow-sm" style="background: {{ $theme['card'] }}; border-color: {{ $theme['border'] }}">
                    <p class="text-sm font-semibold" style="color: {{ $theme['muted'] }}">TOTAL</p>
                    <h2 class="text-3xl font-bold mt-2">{{ $totalOrders }}</h2>
                </div>

                <div class="rounded-3xl p-5 border shadow-sm" style="background: {{ $theme['card'] }}; border-color: {{ $theme['border'] }}">
                    <p class="text-sm font-semibold" style="color: {{ $theme['muted'] }}">PENDENTES</p>
                    <h2 class="text-3xl font-bold mt-2">{{ $pendingOrders }}</h2>
                </div>

                <div class="rounded-3xl p-5 border shadow-sm" style="background: {{ $theme['card'] }}; border-color: {{ $theme['border'] }}">
                    <p class="text-sm font-semibold" style="color: {{ $theme['muted'] }}">CONCLUÍDOS</p>
                    <h2 class="text-3xl font-bold mt-2">{{ $finishedOrders }}</h2>
                </div>

                <div class="rounded-3xl p-5 border shadow-sm" style="background: {{ $theme['card'] }}; border-color: {{ $theme['border'] }}">
                    <p class="text-sm font-semibold" style="color: {{ $theme['muted'] }}">CANCELADOS</p>
                    <h2 class="text-3xl font-bold mt-2">{{ $cancelledOrders }}</h2>
                </div>
            </div>

            <div class="flex flex-wrap gap-3 mb-8">
                <a href="{{ route('orders.index') }}" class="px-5 py-3 rounded-full font-semibold border"
                style="{{ $statusFilter === 'todos' ? 'background: '.$theme['primary'].'; color: white; border-color: '.$theme['primary'] : 'background: '.$theme['card'].'; color: '.$theme['muted'].'; border-color: '.$theme['border'] }}">
                    Todos {{ $totalOrders }}
                </a>

                <a href="{{ route('orders.index', ['status' => 'pendentes']) }}" class="px-5 py-3 rounded-full font-semibold border"
                style="{{ $statusFilter === 'pendentes' ? 'background: '.$theme['primary'].'; color: white; border-color: '.$theme['primary'] : 'background: '.$theme['card'].'; color: '.$theme['muted'].'; border-color: '.$theme['border'] }}">
                    Pendentes {{ $pendingOrders }}
                </a>

                <a href="{{ route('orders.index', ['status' => 'andamento']) }}" class="px-5 py-3 rounded-full font-semibold border"
                style="{{ $statusFilter === 'andamento' ? 'background: '.$theme['primary'].'; color: white; border-color: '.$theme['primary'] : 'background: '.$theme['card'].'; color: '.$theme['muted'].'; border-color: '.$theme['border'] }}">
                    Em andamento {{ $andamentoOrders }}
                </a>

                <a href="{{ route('orders.index', ['status' => 'concluidos']) }}" class="px-5 py-3 rounded-full font-semibold border"
                style="{{ $statusFilter === 'concluidos' ? 'background: '.$theme['primary'].'; color: white; border-color: '.$theme['primary'] : 'background: '.$theme['card'].'; color: '.$theme['muted'].'; border-color: '.$theme['border'] }}">
                    Concluídos {{ $finishedOrders }}
                </a>

                <a href="{{ route('orders.index', ['status' => 'cancelados']) }}" class="px-5 py-3 rounded-full font-semibold border"
                style="{{ $statusFilter === 'cancelados' ? 'background: '.$theme['primary'].'; color: white; border-color: '.$theme['primary'] : 'background: '.$theme['card'].'; color: '.$theme['muted'].'; border-color: '.$theme['border'] }}">
                    Cancelados {{ $cancelledOrders }}
                </a>
            </div>

            <section>
                <div class="flex items-center justify-between mb-3">
                    <h2 class="font-bold tracking-widest">
                        LISTA DE PEDIDOS
                    </h2>

                    <a href="{{ route('store.public', $store->slug) }}" target="_blank" class="font-medium" style="color: {{ $theme['primary'] }}">
                        Ver vitrine ›
                    </a>
                </div>

                <div class="space-y-4">
                    @forelse($orders as $order)
                        <div
                            class="rounded-3xl p-5 border shadow-sm"
                            style="background: {{ $theme['card'] }}; border-color: {{ $theme['border'] }}"
                        >
                            <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-5">
                                <div class="flex-1">
                                    <div class="flex items-center gap-2 flex-wrap mb-3">
                                        <h3 class="font-bold text-xl">
                                            {{ $order->customer_name }}
                                        </h3>

                                        <span
                                            class="text-xs px-3 py-1 rounded-full font-semibold"
                                            style="background: {{ $theme['secondary'] }}; color: {{ $theme['primary'] }}"
                                        >
                                            {{ ucfirst($order->status) }}
                                        </span>
                                    </div>

                                    <div class="grid md:grid-cols-2 gap-2 text-sm" style="color: {{ $theme['muted'] }}">
                                        <p>📱 {{ $order->customer_whatsapp ?: 'Sem WhatsApp' }}</p>
                                        <p>📍 {{ $order->customer_address ?: 'Sem endereço' }}</p>
                                        <p>🕒 {{ $order->created_at->format('d/m/Y H:i') }}</p>
                                        <p class="font-semibold" style="color: {{ $theme['primary'] }}">
                                            Total: R$ {{ number_format($order->total, 2, ',', '.') }}
                                        </p>
                                    </div>
                                </div>

                                <form
                                    method="POST"
                                    action="{{ route('orders.status', $order) }}"
                                    class="lg:w-64 space-y-3"
                                >
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="status_filter" value="{{ $statusFilter }}">

                                    <select
                                        name="status"
                                        class="w-full border rounded-2xl p-3"
                                        style="background: {{ $theme['card'] }}; border-color: {{ $theme['border'] }}; color: {{ $theme['text'] }}"
                                    >
                                        <option value="pendente" @selected($order->status === 'pendente')>Pendente</option>
                                        <option value="em andamento" @selected($order->status === 'em andamento')>Em andamento</option>
                                        <option value="concluído" @selected($order->status === 'concluído')>Concluído</option>
                                        <option value="cancelado" @selected($order->status === 'cancelado')>Cancelado</option>
                                    </select>

                                    <button
                                        class="w-full text-white py-3 rounded-2xl font-semibold"
                                        style="background: {{ $theme['primary'] }}"
                                    >
                                        Atualizar
                                    </button>
                                </form>
                            </div>

                            <div
                                class="mt-5 rounded-2xl p-4 border"
                                style="background: {{ $theme['secondary'] }}; border-color: {{ $theme['border'] }}"
                            >
                                <h4 class="font-semibold mb-3">
                                    Itens do pedido
                                </h4>

                                <div class="space-y-2">
                                    @foreach($order->items as $item)
                                        <div class="flex justify-between gap-4 text-sm border-b pb-2" style="border-color: {{ $theme['border'] }}">
                                            <span>
                                                {{ $item->product_name }} x{{ $item->quantity }}
                                            </span>

                                            <span class="font-semibold">
                                                R$ {{ number_format($item->subtotal, 2, ',', '.') }}
                                            </span>
                                        </div>
                                    @endforeach
                                </div>

                                @if($order->notes)
                                    <div class="mt-4 text-sm" style="color: {{ $theme['muted'] }}">
                                        <strong>Observações:</strong>
                                        {{ $order->notes }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div
                            class="rounded-3xl p-10 text-center border"
                            style="background: {{ $theme['card'] }}; border-color: {{ $theme['border'] }}; color: {{ $theme['muted'] }}"
                        >
                            <div class="text-5xl mb-3">🛍️</div>
                            <p class="text-lg font-semibold">
                                Nenhum pedido ainda.
                            </p>

                            <a
                                href="{{ route('store.public', $store->slug) }}"
                                target="_blank"
                                class="inline-block mt-5 text-white px-6 py-3 rounded-2xl font-semibold"
                                style="background: {{ $theme['primary'] }}"
                            >
                                Abrir vitrine
                            </a>
                        </div>
                    @endforelse
                </div>
            </section>

        </div>
    </div>
</x-app-layout>
