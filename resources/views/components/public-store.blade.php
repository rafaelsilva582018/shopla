<div
    x-data="{
        filtersOpen: false,
        viewMode: 'grid',
        selectedProduct: null,
        selectedImage: 0,
        shareProduct(product) {
            const text = `${product.name} - ${product.formattedPrice}`;

            if (navigator.share) {
                navigator.share({ title: product.name, text, url: product.url });
                return;
            }

            navigator.clipboard.writeText(product.url);
            alert('Link do produto copiado!');
        }
    }"
>
    @php
        $sf = $storefrontTheme;
        $hasFilters = $search || $selectedCategory || $availabilityStatus || $minPrice !== '' || $maxPrice !== '' || $sortBy !== 'best_sellers';
    @endphp

    <form method="GET" action="{{ url()->current() }}" class="mb-5">
        <div class="flex gap-3">
        <input
            type="text"
            name="busca"
            value="{{ $search }}"
            placeholder="Buscar produto, categoria..."
            class="min-w-0 flex-1 rounded-2xl border px-5 py-4 text-base shadow-sm outline-none focus:ring-2"
            style="background: {{ $sf['card'] }}; border-color: {{ $sf['border'] }}; color: {{ $sf['text'] }}; --tw-ring-color: {{ $sf['primary'] }}"
        >

        <button
            type="button"
            @click="filtersOpen = ! filtersOpen"
            class="h-14 w-14 shrink-0 rounded-2xl border shadow-sm flex items-center justify-center"
            :class="filtersOpen ? 'ring-2' : ''"
            style="background: {{ $hasFilters ? $sf['primary'] : $sf['card'] }}; border-color: {{ $sf['border'] }}; --tw-ring-color: {{ $sf['primary'] }}; color: {{ $hasFilters ? '#ffffff' : $sf['primary'] }}"
            aria-label="Abrir filtros"
        >
            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M4 7h4" stroke-linecap="round"/>
                <path d="M14 7h6" stroke-linecap="round"/>
                <path d="M10 5v4" stroke-linecap="round"/>
                <path d="M4 17h6" stroke-linecap="round"/>
                <path d="M16 17h4" stroke-linecap="round"/>
                <path d="M14 15v4" stroke-linecap="round"/>
            </svg>
        </button>

        <button
            type="button"
            @click="viewMode = viewMode === 'grid' ? 'list' : 'grid'"
            class="h-14 w-14 shrink-0 rounded-2xl border shadow-sm flex items-center justify-center"
            style="background: {{ $sf['card'] }}; border-color: {{ $sf['border'] }}; color: {{ $sf['primary'] }}"
            aria-label="Alternar visualizacao"
        >
            <svg x-show="viewMode === 'grid'" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M8 6h12" stroke-linecap="round"/>
                <path d="M8 12h12" stroke-linecap="round"/>
                <path d="M8 18h12" stroke-linecap="round"/>
                <path d="M4 6h.01" stroke-linecap="round"/>
                <path d="M4 12h.01" stroke-linecap="round"/>
                <path d="M4 18h.01" stroke-linecap="round"/>
            </svg>

            <svg x-show="viewMode === 'list'" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display:none;">
                <path d="M4 4h7v7H4z"/>
                <path d="M13 4h7v7h-7z"/>
                <path d="M4 13h7v7H4z"/>
                <path d="M13 13h7v7h-7z"/>
            </svg>
        </button>
        </div>

        <div
            x-show="filtersOpen"
            x-transition
            class="rounded-2xl shadow-sm border p-4 mt-4"
            style="display:none; background: {{ $sf['card'] }}; border-color: {{ $sf['border'] }}"
        >
            <div class="grid grid-cols-1 md:grid-cols-5 gap-3">
                <select
                    name="categoria"
                    class="border rounded-xl p-3"
                    style="border-color: {{ $sf['border'] }}; color: {{ $sf['text'] }}"
                >
                    <option value="">Todas categorias</option>
                    @foreach($store->categories as $category)
                        <option value="{{ $category->id }}" @selected((string) $selectedCategory === (string) $category->id)>{{ $category->name }}</option>
                    @endforeach
                </select>

                <select
                    name="disponibilidade"
                    class="border rounded-xl p-3"
                    style="border-color: {{ $sf['border'] }}; color: {{ $sf['text'] }}"
                >
                    <option value="">Disponibilidade</option>
                    <option value="pronta_entrega" @selected($availabilityStatus === 'pronta_entrega')>Pronta entrega</option>
                    <option value="sob_encomenda" @selected($availabilityStatus === 'sob_encomenda')>Sob encomenda</option>
                    <option value="esgotado" @selected($availabilityStatus === 'esgotado')>Esgotado</option>
                </select>

                <input
                    type="number"
                    min="0"
                    step="0.01"
                    name="preco_min"
                    value="{{ $minPrice }}"
                    placeholder="Preço mínimo"
                    class="border rounded-xl p-3"
                    style="border-color: {{ $sf['border'] }}; color: {{ $sf['text'] }}"
                >

                <input
                    type="number"
                    min="0"
                    step="0.01"
                    name="preco_max"
                    value="{{ $maxPrice }}"
                    placeholder="Preço máximo"
                    class="border rounded-xl p-3"
                    style="border-color: {{ $sf['border'] }}; color: {{ $sf['text'] }}"
                >

                <select
                    name="ordem"
                    class="border rounded-xl p-3"
                    style="border-color: {{ $sf['border'] }}; color: {{ $sf['text'] }}"
                >
                    <option value="best_sellers" @selected($sortBy === 'best_sellers')>Mais vendidos</option>
                    <option value="recent" @selected($sortBy === 'recent')>Mais recentes</option>
                    <option value="name" @selected($sortBy === 'name')>Nome A-Z</option>
                    <option value="price_asc" @selected($sortBy === 'price_asc')>Menor preco</option>
                    <option value="price_desc" @selected($sortBy === 'price_desc')>Maior preco</option>
                </select>
            </div>

            <div class="mt-3 flex flex-col sm:flex-row justify-end gap-3">
                <a
                    href="{{ url()->current() }}"
                    class="rounded-xl border px-5 py-3 font-semibold text-center"
                    style="border-color: {{ $sf['border'] }}; color: {{ $sf['primary'] }}"
                >
                    Limpar filtros
                </a>

                <button
                    type="submit"
                    class="rounded-xl px-5 py-3 font-semibold text-white"
                    style="background: {{ $sf['primary'] }}"
                >
                    Aplicar filtros
                </button>
            </div>
        </div>
    </form>

    @if($hasFilters)
        <div class="mb-5 flex flex-wrap items-center gap-2 text-sm" style="color: {{ $sf['muted'] }}">
            <span>Filtros aplicados</span>

            <a
                href="{{ url()->current() }}"
                class="rounded-full px-3 py-1 font-semibold"
                style="background: {{ $sf['secondary'] }}; color: {{ $sf['primary'] }}"
            >
                Limpar
            </a>
        </div>
    @endif

    <div class="flex gap-2 flex-wrap mb-7">
        <a
            href="{{ url()->current() }}"
            class="px-4 py-2 rounded-full text-sm font-semibold {{ $selectedCategory === '' ? 'text-white' : 'border' }}"
            style="{{ $selectedCategory === '' ? 'background: ' . $sf['primary'] : 'background: ' . $sf['card'] . '; color: ' . $sf['primary'] . '; border-color: ' . $sf['border'] }}"
        >
            Todos
        </a>

        @foreach($store->categories as $category)
            <a
                href="{{ url()->current() . '?' . http_build_query(array_filter([
                    'busca' => $search ?: null,
                    'categoria' => $category->id,
                    'disponibilidade' => $availabilityStatus ?: null,
                    'preco_min' => $minPrice !== '' ? $minPrice : null,
                    'preco_max' => $maxPrice !== '' ? $maxPrice : null,
                    'ordem' => $sortBy !== 'best_sellers' ? $sortBy : null,
                ], fn ($value) => $value !== null && $value !== '')) }}"
                class="px-4 py-2 rounded-full text-sm font-semibold {{ (string) $selectedCategory === (string) $category->id ? 'text-white' : 'border' }}"
                style="{{ (string) $selectedCategory === (string) $category->id ? 'background: ' . $sf['primary'] : 'background: ' . $sf['card'] . '; color: ' . $sf['primary'] . '; border-color: ' . $sf['border'] }}"
            >
                {{ $category->name }}
            </a>
        @endforeach
    </div>

    <div
        :class="viewMode === 'grid'
            ? 'grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6'
            : 'space-y-4'"
    >
        @forelse($products as $product)
            @php
                $positionToCss = function (?string $position) {
                    return match ($position) {
                        'top' => '50% 0%',
                        'bottom' => '50% 100%',
                        'center', null, '' => '50% 50%',
                        default => preg_match('/^\d{1,3}% \d{1,3}%$/', $position) ? $position : '50% 50%',
                    };
                };

                $productImages = collect([
                    ['url' => $product->image, 'position' => $product->image_position ?? 'center'],
                    ['url' => $product->image_2, 'position' => $product->image_2_position ?? 'center'],
                    ['url' => $product->image_3, 'position' => $product->image_3_position ?? 'center'],
                ])
                    ->filter(fn ($image) => filled($image['url']))
                    ->map(fn ($image) => [
                        'url' => asset('storage/' . $image['url']),
                        'position' => $positionToCss($image['position']),
                    ])
                    ->values();

                $productCategoryNames = $product->categories->pluck('name');
                if ($productCategoryNames->isEmpty() && $product->category) {
                    $productCategoryNames = collect([$product->category->name]);
                }
                $productCategoryLabel = $productCategoryNames->isNotEmpty()
                    ? $productCategoryNames->join(', ')
                    : 'Sem categoria';

                $productPayload = [
                    'id' => $product->id,
                    'name' => $product->name,
                    'category' => $productCategoryLabel,
                    'description' => $product->description,
                    'formattedPrice' => 'R$ ' . number_format($product->price, 2, ',', '.'),
                    'availabilityLabel' => $product->availability_label,
                    'featured' => (bool) $product->is_featured,
                    'images' => $productImages->all(),
                    'url' => url()->current() . '#produto-' . $product->id,
                ];
            @endphp

            <article
                id="produto-{{ $product->id }}"
                class="group shadow-sm border overflow-hidden cursor-pointer transition hover:shadow-md"
                :class="viewMode === 'list' ? 'flex gap-4 p-4 md:p-5 rounded-3xl' : 'rounded-3xl'"
                style="background: {{ $sf['card'] }}; border-color: {{ $sf['border'] }}"
                @click="selectedProduct = @js($productPayload); selectedImage = 0"
            >
                @if($productImages->isNotEmpty())
                    <div
                        x-data="{
                            current: 0,
                            startX: null,
                            total: {{ $productImages->count() }},
                            goTo(index) { this.current = index },
                            next() { this.current = Math.min(this.current + 1, this.total - 1) },
                            previous() { this.current = Math.max(this.current - 1, 0) },
                            start(event) { this.startX = event.clientX },
                            finish(event) {
                                if (this.startX === null) return;
                                const distance = event.clientX - this.startX;
                                if (Math.abs(distance) > 40) distance < 0 ? this.next() : this.previous();
                                this.startX = null;
                            }
                        }"
                        class="relative bg-gray-100 overflow-hidden select-none touch-pan-y"
                        :class="viewMode === 'grid'
                            ? 'w-full aspect-square'
                            : 'w-24 h-24 md:w-28 md:h-28 shrink-0 rounded-2xl'"
                        @click.stop="selectedProduct = @js($productPayload); selectedImage = current"
                        @pointerdown="start($event)"
                        @pointerup="finish($event)"
                        @pointercancel="startX = null"
                        @pointerleave="startX = null"
                    >
                        <div
                            class="flex h-full transition-transform duration-300 ease-out"
                            :style="`transform: translateX(-${current * 100}%);`"
                        >
                            @foreach($productImages as $image)
                                <img
                                    src="{{ $image['url'] }}"
                                    class="w-full h-full shrink-0 object-cover"
                                    style="object-position: {{ $image['position'] }}"
                                    alt="{{ $product->name }}"
                                    draggable="false"
                                >
                            @endforeach
                        </div>

                        @if($productImages->count() > 1)
                            <div class="absolute bottom-3 left-0 right-0 flex justify-center gap-2">
                                @foreach($productImages as $index => $image)
                                    <button
                                        type="button"
                                        class="h-2 rounded-full bg-white shadow transition-all"
                                        :class="current === {{ $index }} ? 'w-5 opacity-100' : 'w-2 opacity-75'"
                                        @click.stop="goTo({{ $index }})"
                                        aria-label="Mostrar imagem {{ $index + 1 }}"
                                    ></button>
                                @endforeach
                            </div>
                        @endif

                        @if($product->is_featured)
                            <span
                                class="absolute left-3 top-3 rounded-full px-3 py-1 text-xs font-black text-white shadow-lg"
                                style="background: {{ $sf['primary'] }}"
                            >
                                Destaque
                            </span>
                        @endif
                    </div>
                @else
                    <div
                        class="relative bg-gray-100"
                        :class="viewMode === 'grid'
                            ? 'w-full aspect-square'
                            : 'w-24 h-24 md:w-28 md:h-28 shrink-0 rounded-2xl'"
                    >
                        @if($product->is_featured)
                            <span
                                class="absolute left-3 top-3 rounded-full px-3 py-1 text-xs font-black text-white shadow-lg"
                                style="background: {{ $sf['primary'] }}"
                            >
                                Destaque
                            </span>
                        @endif
                    </div>
                @endif

                <div class="flex flex-col min-w-0" :class="viewMode === 'grid' ? 'p-5' : 'flex-1 py-0 pr-1'">
                    <div class="flex items-start gap-2">
                        <h2 class="font-bold text-lg min-w-0 flex-1" style="color: {{ $sf['text'] }}">
                            {{ $product->name }}
                        </h2>

                        @if($product->is_featured)
                            <span class="rounded-full px-2.5 py-1 text-[11px] font-black text-white shrink-0" style="background: {{ $sf['primary'] }}">
                                Destaque
                            </span>
                        @endif
                    </div>

                    <p class="text-sm mt-1" style="color: {{ $sf['primary'] }}">
                        {{ $productCategoryLabel }}
                    </p>

                    @if($product->description)
                        <p class="text-sm mt-2 line-clamp-2" style="color: {{ $sf['muted'] }}">
                            {{ $product->description }}
                        </p>
                    @endif

                    <div class="flex items-center gap-3 flex-wrap" :class="viewMode === 'grid' ? 'mt-4' : 'mt-2'">
                        <strong class="text-xl" style="color: {{ $sf['primary'] }}">
                            R$ {{ number_format($product->price, 2, ',', '.') }}
                        </strong>

                        <span class="text-xs px-3 py-1 rounded-full font-semibold" style="background: {{ $sf['badge'] }}; color: {{ $sf['badge_text'] }}">
                            {{ $product->availability_label }}
                        </span>

                        <button
                            type="button"
                            class="w-8 h-8 rounded-full flex items-center justify-center"
                            style="background: {{ $sf['secondary'] }}; color: {{ $sf['primary'] }}"
                            @click.stop="shareProduct(@js($productPayload))"
                            aria-label="Compartilhar produto"
                        >
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="18" cy="5" r="3"/>
                                <circle cx="6" cy="12" r="3"/>
                                <circle cx="18" cy="19" r="3"/>
                                <path d="m8.6 13.5 6.8 4"/>
                                <path d="m15.4 6.5-6.8 4"/>
                            </svg>
                        </button>
                    </div>

                    <button
                        type="button"
                        x-on:click.stop
                        wire:click="addProductToCart({{ $product->id }})"
                        class="mt-4 text-white py-3 rounded-xl font-semibold"
                        :class="viewMode === 'grid' ? 'w-full' : 'w-max px-5 py-2 rounded-full text-sm'"
                        style="background: {{ $sf['primary'] }}"
                    >
                        Escolher opcao
                    </button>
                </div>
            </article>
        @empty
            <div class="col-span-full border rounded-2xl p-8 text-center" style="background: {{ $sf['card'] }}; border-color: {{ $sf['border'] }}; color: {{ $sf['muted'] }}">
                Nenhum produto encontrado.
            </div>
        @endforelse
    </div>

    <div
        x-show="selectedProduct"
        x-transition.opacity
        class="shopla-scrollbar fixed inset-0 z-50 bg-black/55 px-4 py-8 overflow-y-auto"
        style="display:none; --shopla-scrollbar-thumb: {{ $sf['primary'] }}"
        @click.self="selectedProduct = null"
    >
        <div class="mx-auto max-w-xl rounded-3xl shadow-2xl overflow-hidden" style="background: {{ $sf['card'] }}">
            <div class="relative" style="background: {{ $sf['secondary'] }}">
                <button
                    type="button"
                    class="absolute right-4 top-4 z-10 w-11 h-11 rounded-full bg-black/35 text-white text-2xl"
                    @click="selectedProduct = null"
                >
                    &times;
                </button>

                <template x-if="selectedProduct && selectedProduct.images.length">
                    <div class="relative h-80 md:h-[420px] overflow-hidden">
                        <template x-for="(image, index) in selectedProduct.images" :key="image.url">
                            <img
                                x-show="selectedImage === index"
                                :src="image.url"
                                class="w-full h-full object-cover"
                                :style="`object-position: ${image.position};`"
                                alt=""
                            >
                        </template>

                        <button
                            type="button"
                            class="absolute left-4 top-1/2 -translate-y-1/2 w-10 h-10 rounded-full bg-black/25 text-white"
                            x-show="selectedProduct.images.length > 1"
                            @click="selectedImage = Math.max(selectedImage - 1, 0)"
                        >
                            ‹
                        </button>

                        <button
                            type="button"
                            class="absolute right-4 top-1/2 -translate-y-1/2 w-10 h-10 rounded-full bg-black/25 text-white"
                            x-show="selectedProduct.images.length > 1"
                            @click="selectedImage = Math.min(selectedImage + 1, selectedProduct.images.length - 1)"
                        >
                            ›
                        </button>

                        <div class="absolute bottom-5 left-0 right-0 flex justify-center gap-2" x-show="selectedProduct.images.length > 1">
                            <template x-for="(image, index) in selectedProduct.images" :key="index">
                                <button
                                    type="button"
                                    class="h-2.5 rounded-full bg-white shadow"
                                    :class="selectedImage === index ? 'w-6' : 'w-2.5 opacity-70'"
                                    @click="selectedImage = index"
                                ></button>
                            </template>
                        </div>
                    </div>
                </template>
            </div>

            <div class="p-6">
                <div class="flex items-center gap-2 flex-wrap">
                    <p class="font-semibold text-sm" style="color: {{ $sf['primary'] }}" x-text="selectedProduct?.category"></p>

                    <span
                        x-show="selectedProduct?.featured"
                        class="rounded-full px-3 py-1 text-xs font-black text-white"
                        style="background: {{ $sf['primary'] }}"
                    >
                        Destaque
                    </span>
                </div>
                <h3 class="text-2xl font-bold mt-2" style="color: {{ $sf['text'] }}" x-text="selectedProduct?.name"></h3>
                <p class="mt-3" style="color: {{ $sf['muted'] }}" x-text="selectedProduct?.description"></p>

                <div class="flex items-center justify-between gap-4 mt-6">
                    <strong class="text-3xl" style="color: {{ $sf['primary'] }}" x-text="selectedProduct?.formattedPrice"></strong>

                    <span
                        class="text-xs px-3 py-1 rounded-full font-semibold"
                        style="background: {{ $sf['badge'] }}; color: {{ $sf['badge_text'] }}"
                        x-text="selectedProduct?.availabilityLabel"
                    ></span>

                    <button
                        type="button"
                        class="px-4 py-3 rounded-2xl border font-semibold flex items-center gap-2"
                        style="color: {{ $sf['primary'] }}; border-color: {{ $sf['border'] }}"
                        @click="shareProduct(selectedProduct)"
                    >
                        Compartilhar
                    </button>
                </div>

                <button
                    type="button"
                    class="w-full mt-5 text-white py-4 rounded-2xl font-bold"
                    style="background: {{ $sf['primary'] }}"
                    @click="$wire.addProductToCart(selectedProduct.id); selectedProduct = null"
                >
                    Escolher opcao
                </button>
            </div>
        </div>
    </div>
</div>
