@php
    $store = auth()->user()->store;
    $theme = $store->dashboardTheme();

    $statusFilter = $statusFilter ?? request('status', 'todos');

    $allProducts = $store->products()->get();

    $totalProducts = $allProducts->count();
    $activeProducts = $allProducts->where('is_active', true)->count();
    $inactiveProducts = $allProducts->where('is_active', false)->count();
    $featuredProducts = $allProducts->where('is_featured', true)->count();
    $withoutCategoryProducts = $store->products()->whereDoesntHave('categories')->count();
    $user = auth()->user();
    $productLimit = $productLimit ?? $user->productLimit();
    $canCreateProduct = $canCreateProduct ?? $user->canCreateProductForStore();
    $limitPercent = $productLimit ? min(100, round(($totalProducts / $productLimit) * 100)) : 0;
@endphp

<x-app-layout>
    <div
        x-data="{
        createModal: {{ $errors->any() ? 'true' : 'false' }},
        editModal: false,
        editAction: '',
        editName: '',
        editDescription: '',
        editPrice: '',
        editCategoryId: '',
        editCategoryIds: [],
        editAvailabilityStatus: 'sob_encomenda',
        editStockQuantity: '',
        editActive: true,
        editFeatured: false
        }"
        class="min-h-screen pb-24"
        style="background: {{ $theme['bg'] }}; color: {{ $theme['text'] }};"
    >
        <div class="max-w-6xl mx-auto px-4 py-8">

            <div class="flex items-start justify-between gap-4 mb-8">
                <div>
                    <p class="text-sm font-semibold tracking-widest" style="color: {{ $theme['muted'] }}">
                        CATÁLOGO
                    </p>

                    <h1 class="text-4xl font-bold mt-1" style="font-family: serif;">
                        Seus produtos ✨
                    </h1>

                    <p class="mt-2" style="color: {{ $theme['muted'] }}">
                        Gerencie os itens que aparecem na sua vitrine.
                    </p>
                </div>

                @if($canCreateProduct)
                    <button
                        type="button"
                        @click="createModal = true"
                        class="text-white px-5 py-3 rounded-2xl font-semibold shadow whitespace-nowrap"
                        style="background: {{ $theme['primary'] }}"
                    >
                        + Novo
                    </button>
                @else
                    <a
                        href="{{ route('plans.index') }}"
                        class="text-white px-5 py-3 rounded-2xl font-semibold shadow whitespace-nowrap"
                        style="background: {{ $theme['primary'] }}"
                    >
                        Ver planos
                    </a>
                @endif
            </div>

            <div class="rounded-3xl p-5 border shadow-sm mb-6" style="background: {{ $theme['card'] }}; border-color: {{ $theme['border'] }}">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div>
                        <p class="text-sm font-semibold tracking-widest" style="color: {{ $theme['muted'] }}">
                            PLANO {{ mb_strtoupper($user->planName(), 'UTF-8') }}
                        </p>
                        <h2 class="text-xl font-bold mt-1">
                            {{ $totalProducts }} de {{ $user->productLimitLabel() }} produto(s)
                        </h2>
                        <p class="text-sm mt-1" style="color: {{ $theme['muted'] }}">
                            @if($productLimit)
                                Você ainda pode cadastrar {{ max(0, $productLimit - $totalProducts) }} produto(s).
                            @else
                                Seu plano não tem limite de produtos.
                            @endif
                        </p>
                    </div>

                    <a href="{{ route('plans.index') }}" class="px-5 py-3 rounded-2xl font-semibold border" style="background: {{ $theme['secondary'] }}; color: {{ $theme['primary'] }}; border-color: {{ $theme['border'] }}">
                        Planos
                    </a>
                </div>

                @if($productLimit)
                    <div class="mt-4 h-3 rounded-full overflow-hidden" style="background: {{ $theme['secondary'] }}">
                        <div class="h-full rounded-full" style="width: {{ $limitPercent }}%; background: {{ $theme['primary'] }}"></div>
                    </div>
                @endif
            </div>

            @if(session('success'))
                <div
                    class="p-4 rounded-2xl mb-6 border"
                    style="background: {{ $theme['secondary'] }}; color: {{ $theme['primary'] }}; border-color: {{ $theme['border'] }}"
                >
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div
                    class="p-4 rounded-2xl mb-6 border bg-red-50 text-red-600 border-red-100"
                >
                    {{ session('error') }}
                </div>
            @endif

            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
                <div class="rounded-3xl p-5 border shadow-sm" style="background: {{ $theme['card'] }}; border-color: {{ $theme['border'] }}">
                    <p class="text-sm font-semibold" style="color: {{ $theme['muted'] }}">PRODUTOS</p>
                    <h2 class="text-3xl font-bold mt-2">{{ $totalProducts }}</h2>
                </div>

                <div class="rounded-3xl p-5 border shadow-sm" style="background: {{ $theme['card'] }}; border-color: {{ $theme['border'] }}">
                    <p class="text-sm font-semibold" style="color: {{ $theme['muted'] }}">ATIVOS</p>
                    <h2 class="text-3xl font-bold mt-2">{{ $activeProducts }}</h2>
                </div>

                <div class="rounded-3xl p-5 border shadow-sm" style="background: {{ $theme['card'] }}; border-color: {{ $theme['border'] }}">
                    <p class="text-sm font-semibold" style="color: {{ $theme['muted'] }}">DESTAQUES</p>
                    <h2 class="text-3xl font-bold mt-2">{{ $featuredProducts }}</h2>
                </div>

                <div class="rounded-3xl p-5 border shadow-sm" style="background: {{ $theme['card'] }}; border-color: {{ $theme['border'] }}">
                    <p class="text-sm font-semibold" style="color: {{ $theme['muted'] }}">CATEGORIAS</p>
                    <h2 class="text-3xl font-bold mt-2">{{ $store->categories()->count() }}</h2>
                </div>
            </div>

            <div class="flex flex-wrap gap-3 mb-8">
                <a
                    href="{{ route('products.index') }}"
                    class="px-5 py-3 rounded-full font-semibold border"
                    style="{{ $statusFilter === 'todos'
                        ? 'background: '.$theme['primary'].'; color: white; border-color: '.$theme['primary']
                        : 'background: '.$theme['card'].'; color: '.$theme['muted'].'; border-color: '.$theme['border'] }}"
                >
                    Todos {{ $totalProducts }}
                </a>

                <a
                    href="{{ route('products.index', ['status' => 'ativos']) }}"
                    class="px-5 py-3 rounded-full font-semibold border"
                    style="{{ $statusFilter === 'ativos'
                        ? 'background: '.$theme['primary'].'; color: white; border-color: '.$theme['primary']
                        : 'background: '.$theme['card'].'; color: '.$theme['muted'].'; border-color: '.$theme['border'] }}"
                >
                    Ativos {{ $activeProducts }}
                </a>

                <a
                    href="{{ route('products.index', ['status' => 'inativos']) }}"
                    class="px-5 py-3 rounded-full font-semibold border"
                    style="{{ $statusFilter === 'inativos'
                        ? 'background: '.$theme['primary'].'; color: white; border-color: '.$theme['primary']
                        : 'background: '.$theme['card'].'; color: '.$theme['muted'].'; border-color: '.$theme['border'] }}"
                >
                    Inativos {{ $inactiveProducts }}
                </a>

                <a
                    href="{{ route('products.index', ['status' => 'destaques']) }}"
                    class="px-5 py-3 rounded-full font-semibold border"
                    style="{{ $statusFilter === 'destaques'
                        ? 'background: '.$theme['primary'].'; color: white; border-color: '.$theme['primary']
                        : 'background: '.$theme['card'].'; color: '.$theme['muted'].'; border-color: '.$theme['border'] }}"
                >
                    Destaques {{ $featuredProducts }}
                </a>

                <a
                    href="{{ route('products.index', ['status' => 'sem-categoria']) }}"
                    class="px-5 py-3 rounded-full font-semibold border"
                    style="{{ $statusFilter === 'sem-categoria'
                        ? 'background: '.$theme['primary'].'; color: white; border-color: '.$theme['primary']
                        : 'background: '.$theme['card'].'; color: '.$theme['muted'].'; border-color: '.$theme['border'] }}"
                >
                    Sem categoria {{ $withoutCategoryProducts }}
                </a>
            </div>

            <section>
                <div class="flex items-center justify-between mb-3">
                    <h2 class="font-bold tracking-widest">
                        LISTA DE PRODUTOS
                    </h2>

                    <a href="{{ route('categories.index') }}" class="font-medium" style="color: {{ $theme['primary'] }}">
                        Categorias ›
                    </a>
                </div>

                @php
                    $productImagePosition = function (?string $position) {
                        return match ($position) {
                            'top' => '50% 0%',
                            'bottom' => '50% 100%',
                            'center', null, '' => '50% 50%',
                            default => preg_match('/^\d{1,3}% \d{1,3}%$/', $position) ? $position : '50% 50%',
                        };
                    };
                @endphp

                <div class="space-y-4">
                    @forelse($products as $product)
                        <div
                            class="rounded-3xl p-4 md:p-5 border shadow-sm"
                            style="background: {{ $theme['card'] }}; border-color: {{ $theme['border'] }}"
                        >
                            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-5">
                                <div class="flex items-center gap-4">
                                    @if($product->image || $product->image_2 || $product->image_3)
                                        <div class="flex -space-x-3">
                                            @if($product->image)
                                                <img
                                                    src="{{ asset('storage/' . $product->image) }}"
                                                    class="w-20 h-20 md:w-24 md:h-24 object-cover rounded-2xl border-4"
                                                    style="border-color: {{ $theme['card'] }}; object-position: {{ $productImagePosition($product->image_position) }}"
                                                >
                                            @endif

                                            @if($product->image_2)
                                                <img
                                                    src="{{ asset('storage/' . $product->image_2) }}"
                                                    class="w-20 h-20 md:w-24 md:h-24 object-cover rounded-2xl border-4"
                                                    style="border-color: {{ $theme['card'] }}; object-position: {{ $productImagePosition($product->image_2_position) }}"
                                                >
                                            @endif

                                            @if($product->image_3)
                                                <img
                                                    src="{{ asset('storage/' . $product->image_3) }}"
                                                    class="w-20 h-20 md:w-24 md:h-24 object-cover rounded-2xl border-4"
                                                    style="border-color: {{ $theme['card'] }}; object-position: {{ $productImagePosition($product->image_3_position) }}"
                                                >
                                            @endif
                                        </div>
                                    @else
                                        <div
                                            class="w-20 h-20 md:w-24 md:h-24 rounded-2xl flex items-center justify-center text-3xl"
                                            style="background: {{ $theme['secondary'] }}"
                                        >
                                            📦
                                        </div>
                                    @endif

                                    <div>
                                        <div class="flex items-center gap-2 flex-wrap">
                                            <h3 class="font-bold text-lg">
                                                {{ $product->name }}
                                            </h3>

                                            @if($product->is_active)
                                                <span
                                                    class="text-xs px-3 py-1 rounded-full"
                                                    style="background: {{ $theme['secondary'] }}; color: {{ $theme['primary'] }}"
                                                >
                                                    Ativo
                                                </span>
                                            @else
                                                <span class="text-xs px-3 py-1 rounded-full bg-gray-100 text-gray-500">
                                                    Inativo
                                                </span>
                                            @endif

                                            @if($product->is_featured)
                                                <span
                                                    class="text-xs px-3 py-1 rounded-full"
                                                    style="background: {{ $theme['primary'] }}; color: white;"
                                                >
                                                    Destaque
                                                </span>
                                            @endif
                                        </div>

                                        <p class="text-sm mt-1" style="color: {{ $theme['muted'] }}">
                                            {{ $product->categories->isNotEmpty() ? $product->categories->pluck('name')->join(', ') : ($product->category?->name ?? 'Sem categoria') }}
                                        </p>

                                        <p class="font-bold text-xl mt-2" style="color: {{ $theme['primary'] }}">
                                            R$ {{ number_format($product->price, 2, ',', '.') }}
                                        </p>

                                        <p class="text-sm mt-1" style="color: {{ $theme['muted'] }}">
                                            Estoque: {{ $product->track_stock ? ($product->stock_quantity ?? 0) : 'Livre' }}
                                        </p>
                                    </div>
                                </div>

                                <div class="flex md:flex-col gap-2 md:min-w-[130px]">
                                    <button
                                        type="button"
                                        @click="
                                            editModal = true;
                                            editAction = @js(route('products.update', $product, absolute: false));
                                            editName = '{{ addslashes($product->name) }}';
                                            editDescription = '{{ addslashes($product->description ?? '') }}';
                                            editPrice = '{{ $product->price }}';
                                            editCategoryId = '{{ $product->category_id }}';
                                            editCategoryIds = @js($product->categories->pluck('id')->map(fn ($id) => (string) $id)->values());
                                            editAvailabilityStatus = '{{ $product->availability_status ?? 'sob_encomenda' }}';
                                            editStockQuantity = '{{ $product->track_stock ? ($product->stock_quantity ?? 0) : '' }}';
                                            editActive = {{ $product->is_active ? 'true' : 'false' }};
                                            editFeatured = {{ $product->is_featured ? 'true' : 'false' }};
                                            window.dispatchEvent(new CustomEvent('product-images:load', {
                                                detail: {
                                                    images: @js([
                                                        $product->image ? asset('storage/' . $product->image) : null,
                                                        $product->image_2 ? asset('storage/' . $product->image_2) : null,
                                                        $product->image_3 ? asset('storage/' . $product->image_3) : null,
                                                    ]),
                                                    positions: @js([
                                                        $product->image_position,
                                                        $product->image_2_position,
                                                        $product->image_3_position,
                                                    ]),
                                                },
                                            }));
                                        "
                                        class="text-center px-4 py-3 rounded-2xl font-semibold border"
                                        style="background: {{ $theme['secondary'] }}; color: {{ $theme['primary'] }}; border-color: {{ $theme['border'] }}"
                                    >
                                        Editar
                                    </button>

                                    <form method="POST" action="{{ route('products.destroy', $product) }}">
                                        @csrf
                                        @method('DELETE')

                                        <button
                                            onclick="return confirm('Deseja excluir este produto?')"
                                            class="w-full text-center px-4 py-3 rounded-2xl font-semibold text-red-500 bg-red-50"
                                        >
                                            Excluir
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div
                            class="rounded-3xl p-10 text-center border"
                            style="background: {{ $theme['card'] }}; border-color: {{ $theme['border'] }}; color: {{ $theme['muted'] }}"
                        >
                            <div class="text-5xl mb-3">📦</div>

                            <p class="text-lg font-semibold">
                                Nenhum produto encontrado.
                            </p>

                            @if($canCreateProduct)
                                <button
                                    type="button"
                                    @click="createModal = true"
                                    class="inline-block mt-5 text-white px-6 py-3 rounded-2xl font-semibold"
                                    style="background: {{ $theme['primary'] }}"
                                >
                                    Criar produto
                                </button>
                            @else
                                <a
                                    href="{{ route('plans.index') }}"
                                    class="inline-block mt-5 text-white px-6 py-3 rounded-2xl font-semibold"
                                    style="background: {{ $theme['primary'] }}"
                                >
                                    Ver planos
                                </a>
                            @endif
                        </div>
                    @endforelse
                </div>
            </section>

        </div>
        {{-- MODAL CRIAR PRODUTO --}}
        <div
            x-show="createModal"
            x-transition
            class="fixed inset-0 z-50 flex items-center justify-center px-4"
            style="display:none;"
        >
            <div class="absolute inset-0 bg-black/60" @click="createModal = false"></div>

            <div
                class="shopla-scrollbar relative w-full max-w-2xl max-h-[90vh] overflow-y-auto rounded-3xl p-6 shadow-2xl"
                style="--shopla-scrollbar-thumb: {{ $theme['primary'] }}; background: {{ $theme['card'] }}; color: {{ $theme['text'] }}"
            >
                <div
                    class="sticky -top-6 z-20 -mx-6 -mt-6 mb-6 px-6 py-5 border-b backdrop-blur"
                    style="background: color-mix(in srgb, {{ $theme['card'] }} 92%, transparent); border-color: {{ $theme['border'] }}"
                >
                    <div class="flex items-center justify-between gap-4">
                        <div class="flex items-center gap-4 min-w-0">
                            <span
                                class="h-14 w-14 shrink-0 rounded-2xl flex items-center justify-center shadow-sm"
                                style="background: {{ $theme['secondary'] }}; color: {{ $theme['primary'] }}"
                            >
                                <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                                    <path d="M6 3h9l3 3v15H6z"></path>
                                    <path d="M14 3v4h4"></path>
                                    <path d="M9 13h6"></path>
                                    <path d="M12 10v6"></path>
                                </svg>
                            </span>

                            <div class="min-w-0">
                                <p class="text-xs font-bold tracking-widest uppercase" style="color: {{ $theme['muted'] }}">
                                    Cadastro
                                </p>
                                <h2 class="text-2xl md:text-3xl font-black leading-tight truncate">Novo produto</h2>
                                <p class="text-sm mt-1" style="color: {{ $theme['muted'] }}">
                                    Preencha os dados, fotos e disponibilidade do item.
                                </p>
                            </div>
                        </div>

                        <button
                            type="button"
                            @click="createModal = false"
                            class="h-11 w-11 shrink-0 rounded-2xl flex items-center justify-center transition hover:scale-105"
                            style="background: {{ $theme['secondary'] }}; color: {{ $theme['text'] }}"
                            aria-label="Fechar"
                        >
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                                <path d="M18 6 6 18"></path>
                                <path d="m6 6 12 12"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                <form method="POST" action="{{ route('products.store') }}" enctype="multipart/form-data" data-optimize-images class="space-y-4">
                    @csrf

                    @if($errors->any())
                        <div class="rounded-2xl border bg-red-50 p-4 text-sm text-red-600 border-red-100">
                            <strong>Confira os dados do produto.</strong>
                            <ul class="mt-2 list-disc pl-5">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div>
                        <label class="block mb-2 font-semibold">Nome</label>
                        <input type="text" name="name" value="{{ old('name') }}" class="w-full border rounded-2xl p-4" style="border-color: {{ $theme['border'] }}" required>
                    </div>

                    <div>
                        <label class="block mb-2 font-semibold">Categorias</label>
                        <div class="grid sm:grid-cols-2 gap-2 rounded-2xl border p-3" style="border-color: {{ $theme['border'] }}">
                            @forelse($store->categories as $category)
                                <label class="flex items-center gap-3 rounded-xl px-3 py-2 border cursor-pointer" style="border-color: {{ $theme['border'] }}">
                                    <input
                                        type="checkbox"
                                        name="category_ids[]"
                                        value="{{ $category->id }}"
                                        @checked(in_array((string) $category->id, old('category_ids', []), true) || (string) old('category_id') === (string) $category->id)
                                    >
                                    <span>{{ $category->name }}</span>
                                </label>
                            @empty
                                <p class="text-sm" style="color: {{ $theme['muted'] }}">Cadastre categorias para organizar esse produto.</p>
                            @endforelse
                        </div>
                        <p class="text-sm mt-2" style="color: {{ $theme['muted'] }}">Pode escolher mais de uma. A primeira marcada sera a principal.</p>
                    </div>

                    <div>
                        <label class="block mb-2 font-semibold">Preço</label>
                        <input type="text" inputmode="decimal" name="price" value="{{ old('price') }}" placeholder="Ex: 19,90" class="w-full border rounded-2xl p-4" style="border-color: {{ $theme['border'] }}" required>
                    </div>

                    <div>
                        <label class="block mb-2 font-semibold">Descrição</label>
                        <textarea name="description" rows="4" class="w-full border rounded-2xl p-4" style="border-color: {{ $theme['border'] }}">{{ old('description') }}</textarea>
                    </div>

                    <div>
                        <label class="block mb-2 font-semibold">Disponibilidade</label>
                        <select name="availability_status" class="w-full border rounded-2xl p-4" style="border-color: {{ $theme['border'] }}" required>
                            @foreach(\App\Models\Product::AVAILABILITY_STATUSES as $status => $label)
                                <option value="{{ $status }}" @selected(old('availability_status', 'sob_encomenda') === $status)>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block mb-2 font-semibold">Quantidade em estoque</label>
                        <input type="number" min="0" name="stock_quantity" value="{{ old('stock_quantity') }}" placeholder="Deixe em branco para vender sem limite" class="w-full border rounded-2xl p-4" style="border-color: {{ $theme['border'] }}">
                        <p class="text-sm mt-2" style="color: {{ $theme['muted'] }}">Se informar uma quantidade, a venda será limitada. Se marcar como esgotado, o estoque será zero.</p>
                    </div>

                    <x-product-images-upload
                        :border="$theme['border']"
                        :primary="$theme['primary']"
                        :background="$theme['bg']"
                        :card="$theme['card']"
                        :muted="$theme['muted']"
                    />

                    <div class="grid md:grid-cols-2 gap-3">
                        <label class="flex items-center gap-3 rounded-2xl p-4 border" style="border-color: {{ $theme['border'] }}">
                            <input type="checkbox" name="is_active" value="1" checked>
                            <span>Produto ativo</span>
                        </label>

                        <label class="flex items-center gap-3 rounded-2xl p-4 border" style="border-color: {{ $theme['border'] }}">
                            <input type="checkbox" name="is_featured" value="1">
                            <span>Produto em destaque</span>
                        </label>
                    </div>

                    <button
                        type="submit"
                        class="w-full text-white py-4 rounded-2xl font-semibold"
                        style="background: {{ $theme['primary'] }}"
                    >
                        Criar produto
                    </button>
                </form>
            </div>
        </div>

        {{-- MODAL EDITAR PRODUTO --}}
        <div
            x-show="editModal"
            x-transition
            class="fixed inset-0 z-50 flex items-center justify-center px-4"
            style="display:none;"
        >
            <div class="absolute inset-0 bg-black/60" @click="editModal = false"></div>

            <div
                class="shopla-scrollbar relative w-full max-w-2xl max-h-[90vh] overflow-y-auto rounded-3xl p-6 shadow-2xl"
                style="--shopla-scrollbar-thumb: {{ $theme['primary'] }}; background: {{ $theme['card'] }}; color: {{ $theme['text'] }}"
            >
                <div
                    class="sticky -top-6 z-20 -mx-6 -mt-6 mb-6 px-6 py-5 border-b backdrop-blur"
                    style="background: color-mix(in srgb, {{ $theme['card'] }} 92%, transparent); border-color: {{ $theme['border'] }}"
                >
                    <div class="flex items-center justify-between gap-4">
                        <div class="flex items-center gap-4 min-w-0">
                            <span
                                class="h-14 w-14 shrink-0 rounded-2xl flex items-center justify-center shadow-sm"
                                style="background: {{ $theme['secondary'] }}; color: {{ $theme['primary'] }}"
                            >
                                <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                                    <path d="M12 20h9"></path>
                                    <path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4z"></path>
                                </svg>
                            </span>

                            <div class="min-w-0">
                                <p class="text-xs font-bold tracking-widest uppercase" style="color: {{ $theme['muted'] }}">
                                    Atualizacao
                                </p>
                                <h2 class="text-2xl md:text-3xl font-black leading-tight truncate">Editar produto</h2>
                                <p class="text-sm mt-1" style="color: {{ $theme['muted'] }}">
                                    Ajuste informacoes, estoque, fotos e destaque.
                                </p>
                            </div>
                        </div>

                        <button
                            type="button"
                            @click="editModal = false"
                            class="h-11 w-11 shrink-0 rounded-2xl flex items-center justify-center transition hover:scale-105"
                            style="background: {{ $theme['secondary'] }}; color: {{ $theme['text'] }}"
                            aria-label="Fechar"
                        >
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                                <path d="M18 6 6 18"></path>
                                <path d="m6 6 12 12"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                <form :action="editAction" method="POST" enctype="multipart/form-data" data-optimize-images class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="block mb-2 font-semibold">Nome</label>
                        <input x-model="editName" type="text" name="name" class="w-full border rounded-2xl p-4" style="border-color: {{ $theme['border'] }}" required>
                    </div>

                    <div>
                        <label class="block mb-2 font-semibold">Categorias</label>
                        <div class="grid sm:grid-cols-2 gap-2 rounded-2xl border p-3" style="border-color: {{ $theme['border'] }}">
                            @forelse($store->categories as $category)
                                <label class="flex items-center gap-3 rounded-xl px-3 py-2 border cursor-pointer" style="border-color: {{ $theme['border'] }}">
                                    <input
                                        type="checkbox"
                                        name="category_ids[]"
                                        value="{{ $category->id }}"
                                        x-model="editCategoryIds"
                                    >
                                    <span>{{ $category->name }}</span>
                                </label>
                            @empty
                                <p class="text-sm" style="color: {{ $theme['muted'] }}">Cadastre categorias para organizar esse produto.</p>
                            @endforelse
                        </div>
                        <p class="text-sm mt-2" style="color: {{ $theme['muted'] }}">Pode escolher mais de uma. A primeira marcada sera a principal.</p>
                    </div>

                    <div>
                        <label class="block mb-2 font-semibold">Preço</label>
                        <input x-model="editPrice" type="number" step="0.01" name="price" class="w-full border rounded-2xl p-4" style="border-color: {{ $theme['border'] }}" required>
                    </div>

                    <div>
                        <label class="block mb-2 font-semibold">Descrição</label>
                        <textarea x-model="editDescription" name="description" rows="4" class="w-full border rounded-2xl p-4" style="border-color: {{ $theme['border'] }}"></textarea>
                    </div>

                    <div>
                        <label class="block mb-2 font-semibold">Disponibilidade</label>
                        <select x-model="editAvailabilityStatus" name="availability_status" class="w-full border rounded-2xl p-4" style="border-color: {{ $theme['border'] }}" required>
                            @foreach(\App\Models\Product::AVAILABILITY_STATUSES as $status => $label)
                                <option value="{{ $status }}">
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block mb-2 font-semibold">Quantidade em estoque</label>
                        <input x-model="editStockQuantity" type="number" min="0" name="stock_quantity" placeholder="Deixe em branco para vender sem limite" class="w-full border rounded-2xl p-4" style="border-color: {{ $theme['border'] }}">
                        <p class="text-sm mt-2" style="color: {{ $theme['muted'] }}">Se informar uma quantidade, a venda será limitada. Se marcar como esgotado, o estoque será zero.</p>
                    </div>

                    <x-product-images-upload
                        description="Arraste uma nova foto ou clique para trocar."
                        primary-label="Trocar imagem principal"
                        secondary-label="Trocar segunda imagem"
                        primary-hint="Arraste uma nova foto ou clique para escolher."
                        secondary-hint="Opcional. Ela aparece no carrossel do produto."
                        :listen="true"
                        :border="$theme['border']"
                        :primary="$theme['primary']"
                        :background="$theme['bg']"
                        :card="$theme['card']"
                        :muted="$theme['muted']"
                    />

                    <div class="grid md:grid-cols-2 gap-3">
                        <label class="flex items-center gap-3 rounded-2xl p-4 border" style="border-color: {{ $theme['border'] }}">
                            <input type="checkbox" name="is_active" value="1" x-model="editActive">
                            <span>Produto ativo</span>
                        </label>

                        <label class="flex items-center gap-3 rounded-2xl p-4 border" style="border-color: {{ $theme['border'] }}">
                            <input type="checkbox" name="is_featured" value="1" x-model="editFeatured">
                            <span>Produto em destaque</span>
                        </label>
                    </div>

                    <button
                        type="submit"
                        class="w-full text-white py-4 rounded-2xl font-semibold"
                        style="background: {{ $theme['primary'] }}"
                    >
                        Salvar alterações
                    </button>
                </form>
            </div>
        </div>
    </div>

    <x-image-upload-optimizer />
</x-app-layout>
