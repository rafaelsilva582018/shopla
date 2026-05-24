@php
    $theme = $store->dashboardTheme();
@endphp

<x-app-layout>
    <div class="min-h-screen pb-24" style="background: {{ $theme['bg'] }}; color: {{ $theme['text'] }};">
        <div class="max-w-6xl mx-auto px-4 py-8">
            <div class="mb-8 flex flex-col md:flex-row md:items-end md:justify-between gap-4">
                <div>
                    <p class="text-sm font-semibold tracking-widest" style="color: {{ $theme['muted'] }}">
                        ESTOQUE
                    </p>

                    <h1 class="text-4xl font-bold mt-1" style="font-family: serif;">
                        Controle de estoque
                    </h1>

                    <p class="mt-2" style="color: {{ $theme['muted'] }}">
                        Acompanhe limites, produtos baixos e itens esgotados.
                    </p>
                </div>

                <a
                    href="{{ route('products.index') }}"
                    class="w-max px-5 py-3 rounded-2xl font-semibold border"
                    style="background: {{ $theme['card'] }}; color: {{ $theme['primary'] }}; border-color: {{ $theme['border'] }}"
                >
                    Ver catalogo
                </a>
            </div>

            @if(session('success'))
                <div
                    class="p-4 rounded-2xl mb-6 border"
                    style="background: {{ $theme['secondary'] }}; color: {{ $theme['primary'] }}; border-color: {{ $theme['border'] }}"
                >
                    {{ session('success') }}
                </div>
            @endif

            <div class="grid grid-cols-2 lg:grid-cols-5 gap-4 mb-8">
                <div class="rounded-3xl p-5 border shadow-sm" style="background: {{ $theme['card'] }}; border-color: {{ $theme['border'] }}">
                    <p class="text-xs font-semibold tracking-widest" style="color: {{ $theme['muted'] }}">PRODUTOS</p>
                    <h2 class="text-3xl font-bold mt-2">{{ $totalProducts }}</h2>
                </div>

                <div class="rounded-3xl p-5 border shadow-sm" style="background: {{ $theme['card'] }}; border-color: {{ $theme['border'] }}">
                    <p class="text-xs font-semibold tracking-widest" style="color: {{ $theme['muted'] }}">CONTROLADOS</p>
                    <h2 class="text-3xl font-bold mt-2">{{ $trackedProducts }}</h2>
                </div>

                <div class="rounded-3xl p-5 border shadow-sm" style="background: {{ $theme['card'] }}; border-color: {{ $theme['border'] }}">
                    <p class="text-xs font-semibold tracking-widest" style="color: {{ $theme['muted'] }}">VENDA LIVRE</p>
                    <h2 class="text-3xl font-bold mt-2">{{ $freeProducts }}</h2>
                </div>

                <div class="rounded-3xl p-5 border shadow-sm" style="background: {{ $theme['card'] }}; border-color: {{ $theme['border'] }}">
                    <p class="text-xs font-semibold tracking-widest text-amber-600">BAIXO ESTOQUE</p>
                    <h2 class="text-3xl font-bold mt-2">{{ $lowStockProducts }}</h2>
                </div>

                <div class="rounded-3xl p-5 border shadow-sm" style="background: {{ $theme['card'] }}; border-color: {{ $theme['border'] }}">
                    <p class="text-xs font-semibold tracking-widest text-red-500">ESGOTADOS</p>
                    <h2 class="text-3xl font-bold mt-2">{{ $outOfStockProducts }}</h2>
                </div>
            </div>

            <div class="flex flex-wrap gap-3 mb-6">
                @foreach([
                    'todos' => ['label' => 'Todos', 'count' => $totalProducts],
                    'baixo' => ['label' => 'Baixo estoque', 'count' => $lowStockProducts],
                    'esgotado' => ['label' => 'Esgotados', 'count' => $outOfStockProducts],
                    'controlado' => ['label' => 'Controlados', 'count' => $trackedProducts],
                    'livre' => ['label' => 'Venda livre', 'count' => $freeProducts],
                ] as $key => $filter)
                    <a
                        href="{{ route('stock.index', $key === 'todos' ? [] : ['status' => $key]) }}"
                        class="px-5 py-3 rounded-full font-semibold border"
                        style="{{ $statusFilter === $key
                            ? 'background: '.$theme['primary'].'; color: white; border-color: '.$theme['primary']
                            : 'background: '.$theme['card'].'; color: '.$theme['muted'].'; border-color: '.$theme['border'] }}"
                    >
                        {{ $filter['label'] }} {{ $filter['count'] }}
                    </a>
                @endforeach
            </div>

            <div class="space-y-4">
                @forelse($products as $product)
                    @php
                        $isOut = $product->availability_status === 'esgotado' || ($product->track_stock && (int) $product->stock_quantity === 0);
                        $isLow = $product->track_stock && (int) $product->stock_quantity > 0 && (int) $product->stock_quantity <= 5;
                    @endphp

                    <form
                        method="POST"
                        action="{{ route('stock.update', $product) }}"
                        x-data="{ status: '{{ old('availability_status', $product->availability_status ?? 'sob_encomenda') }}' }"
                        class="rounded-3xl p-5 border shadow-sm"
                        style="background: {{ $theme['card'] }}; border-color: {{ $theme['border'] }}"
                    >
                        @csrf
                        @method('PUT')

                        <div class="grid lg:grid-cols-[1fr_260px_210px_auto] gap-4 lg:items-start">
                            <div class="flex items-center gap-4 min-w-0">
                                @if($product->image)
                                    <img
                                        src="{{ asset('storage/' . $product->image) }}"
                                        class="w-16 h-16 rounded-2xl object-cover"
                                    >
                                @else
                                    <div class="w-16 h-16 rounded-2xl flex items-center justify-center text-sm" style="background: {{ $theme['secondary'] }}">
                                        Produto
                                    </div>
                                @endif

                                <div class="min-w-0">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <h2 class="font-bold text-lg truncate">
                                            {{ $product->name }}
                                        </h2>

                                        @if($isOut)
                                            <span class="text-xs px-3 py-1 rounded-full bg-red-100 text-red-600 font-semibold">
                                                Esgotado
                                            </span>
                                        @elseif($isLow)
                                            <span class="text-xs px-3 py-1 rounded-full bg-amber-100 text-amber-700 font-semibold">
                                                Baixo estoque
                                            </span>
                                        @elseif(!$product->track_stock)
                                            <span class="text-xs px-3 py-1 rounded-full" style="background: {{ $theme['secondary'] }}; color: {{ $theme['primary'] }}">
                                                Venda livre
                                            </span>
                                        @else
                                            <span class="text-xs px-3 py-1 rounded-full bg-green-100 text-green-700 font-semibold">
                                                Em estoque
                                            </span>
                                        @endif
                                    </div>

                                    <p class="text-sm mt-1" style="color: {{ $theme['muted'] }}">
                                        {{ $product->categories->isNotEmpty() ? $product->categories->pluck('name')->join(', ') : ($product->category?->name ?? 'Sem categoria') }}
                                    </p>

                                    <p class="text-sm mt-1" style="color: {{ $theme['muted'] }}">
                                        Estoque atual: {{ $product->track_stock ? ($product->stock_quantity ?? 0) : 'Venda sem limite' }}
                                    </p>
                                </div>
                            </div>

                            <div class="min-h-[96px]">
                                <label class="block text-sm font-semibold mb-2">
                                    Disponibilidade
                                </label>

                                <select
                                    name="availability_status"
                                    x-model="status"
                                    class="w-full border rounded-2xl p-3"
                                    style="border-color: {{ $theme['border'] }}"
                                >
                                    @foreach(\App\Models\Product::AVAILABILITY_STATUSES as $status => $label)
                                        <option value="{{ $status }}">
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>

                                <p x-show="status === 'esgotado'" class="text-xs text-red-500 mt-2">
                                    Ao salvar como esgotado, o estoque será zerado.
                                </p>
                            </div>

                            <div class="min-h-[96px]">
                                <label class="block text-sm font-semibold mb-2">
                                    Quantidade
                                </label>

                                <input
                                    type="number"
                                    name="stock_quantity"
                                    min="0"
                                    value="{{ old('stock_quantity', $product->track_stock ? ($product->stock_quantity ?? 0) : '') }}"
                                    placeholder="Livre"
                                    class="w-full border rounded-2xl p-3"
                                    style="border-color: {{ $theme['border'] }}"
                                    :disabled="status === 'esgotado'"
                                    :value="status === 'esgotado' ? 0 : $el.value"
                                >

                                <p x-show="status !== 'esgotado'" class="text-xs mt-2" style="color: {{ $theme['muted'] }}">
                                    Em branco = venda sem limite.
                                </p>
                            </div>

                            <button
                                class="text-white px-7 py-4 rounded-2xl font-semibold lg:self-start lg:mt-6"
                                style="background: {{ $theme['primary'] }}"
                            >
                                Salvar
                            </button>
                        </div>
                    </form>
                @empty
                    <div class="rounded-3xl p-10 text-center border" style="background: {{ $theme['card'] }}; border-color: {{ $theme['border'] }}; color: {{ $theme['muted'] }}">
                        Nenhum produto encontrado para este filtro.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>
