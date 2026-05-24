@php
    $theme = auth()->user()->store?->dashboardTheme() ?? config('dashboard-themes.blush');
@endphp

<x-app-layout>
    <div class="max-w-4xl mx-auto py-10 px-4">

        <div class="mb-8">
            <h1 class="text-3xl font-bold">
                Editar produto
            </h1>

            <p class="text-gray-500 mt-2">
                Atualize as informações, fotos e disponibilidade do produto.
            </p>
        </div>

        @if(session('success'))
            <div class="bg-green-100 text-green-700 p-4 rounded-xl mb-6">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="bg-red-100 text-red-700 p-4 rounded-xl mb-6">
                <ul class="space-y-1">
                    @foreach($errors->all() as $error)
                        <li>
                            {{ $error }}
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="bg-white rounded-3xl shadow-sm border p-5 md:p-7" style="border-color: {{ $theme['border'] ?? '#e5e7eb' }}">
            <form
                method="POST"
                action="{{ route('products.update', $product) }}"
                enctype="multipart/form-data"
                data-optimize-images
                class="space-y-6"
            >
                @csrf
                @method('PUT')

                <div class="flex items-center gap-3 border-b pb-5" style="border-color: {{ $theme['border'] ?? '#e5e7eb' }}">
                    <span class="h-11 w-11 rounded-2xl flex items-center justify-center bg-purple-50 text-purple-600">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M6 2h9l5 5v15H6z"></path>
                            <path d="M14 2v6h6"></path>
                        </svg>
                    </span>
                    <div>
                        <h2 class="font-bold text-xl">Dados do produto</h2>
                        <p class="text-sm text-gray-500">Mantenha as informações da vitrine sempre atualizadas.</p>
                    </div>
                </div>

                <div>
                    <label class="block font-medium mb-2">
                        Nome do produto
                    </label>

                    <input
                        type="text"
                        name="name"
                        value="{{ old('name', $product->name) }}"
                        class="w-full border rounded-xl p-4"
                        required
                    >
                </div>

                <div>
                    <label class="block font-medium mb-2">
                        Categorias
                    </label>

                    @php
                        $selectedCategoryIds = old('category_ids', $product->categories->pluck('id')->map(fn ($id) => (string) $id)->all());
                    @endphp

                    <div class="grid gap-2 rounded-xl border p-3">
                        @forelse($categories as $category)
                            <label class="flex items-center gap-3 rounded-xl px-3 py-2 border cursor-pointer">
                                <input
                                    type="checkbox"
                                    name="category_ids[]"
                                    value="{{ $category->id }}"
                                    @checked(in_array((string) $category->id, $selectedCategoryIds, true) || ((empty($selectedCategoryIds)) && $product->category_id == $category->id))
                                >
                                <span>{{ $category->name }}</span>
                            </label>
                        @empty
                            <p class="text-sm text-gray-500">Cadastre categorias para organizar esse produto.</p>
                        @endforelse
                    </div>

                    <p class="text-sm text-gray-500 mt-2">
                        Pode escolher mais de uma. A primeira marcada sera a principal.
                    </p>
                </div>

                <div>
                    <label class="block font-medium mb-2">
                        Descrição
                    </label>

                    <textarea
                        name="description"
                        rows="4"
                        class="w-full border rounded-xl p-4"
                    >{{ old('description', $product->description) }}</textarea>
                </div>

                <div>
                    <label class="block font-medium mb-2">
                        Preço
                    </label>

                    <input
                        type="number"
                        step="0.01"
                        name="price"
                        value="{{ old('price', $product->price) }}"
                        class="w-full border rounded-xl p-4"
                        required
                    >
                </div>

                <div>
                    <label class="block font-medium mb-2">
                        Disponibilidade
                    </label>

                    <select
                        name="availability_status"
                        class="w-full border rounded-xl p-4"
                        required
                    >
                        @foreach(\App\Models\Product::AVAILABILITY_STATUSES as $status => $label)
                            <option
                                value="{{ $status }}"
                                @selected(old('availability_status', $product->availability_status ?? 'sob_encomenda') === $status)
                            >
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block font-medium mb-2">
                        Quantidade em estoque
                    </label>

                    <input
                        type="number"
                        min="0"
                        name="stock_quantity"
                        value="{{ old('stock_quantity', $product->track_stock ? ($product->stock_quantity ?? 0) : '') }}"
                        placeholder="Deixe em branco para vender sem limite"
                        class="w-full border rounded-xl p-4"
                    >

                    <p class="text-sm text-gray-500 mt-2">
                        Se informar uma quantidade, a venda será limitada. Se marcar como esgotado, o estoque será zero.
                    </p>
                </div>

                <div class="border-t pt-6" style="border-color: {{ $theme['border'] ?? '#e5e7eb' }}">
                    <x-product-images-upload
                        description="Arraste uma nova foto ou clique para trocar."
                        primary-hint="Troque a foto principal da vitrine."
                        secondary-hint="Opcional. Ela aparece no carrossel do produto."
                        :current-primary="$product->image ? asset('storage/' . $product->image) : null"
                        :current-secondary="$product->image_2 ? asset('storage/' . $product->image_2) : null"
                        :current-third="$product->image_3 ? asset('storage/' . $product->image_3) : null"
                        :primary-position="$product->image_position"
                        :secondary-position="$product->image_2_position"
                        :third-position="$product->image_3_position"
                        border="#e5e7eb"
                        primary="#9333ea"
                        background="#fafafa"
                        card="#ffffff"
                    />
                </div>

                <div class="grid md:grid-cols-2 gap-4">

                    <label class="flex items-center gap-3 bg-gray-50 p-4 rounded-xl">
                        <input
                            type="checkbox"
                            name="is_active"
                            value="1"
                            @checked($product->is_active)
                        >

                        <span>
                            Produto ativo
                        </span>
                    </label>

                    <label class="flex items-center gap-3 bg-gray-50 p-4 rounded-xl">
                        <input
                            type="checkbox"
                            name="is_featured"
                            value="1"
                            @checked($product->is_featured)
                        >

                        <span>
                            Produto em destaque
                        </span>
                    </label>

                </div>

                <div class="flex gap-4">
                    <button
                        type="submit"
                        class="bg-purple-600 text-white px-8 py-4 rounded-xl font-semibold"
                    >
                        <span class="inline-flex items-center gap-2">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2Z"></path>
                                <path d="M17 21v-8H7v8"></path>
                                <path d="M7 3v5h8"></path>
                            </svg>
                            Salvar alterações
                        </span>
                    </button>

                    <a
                        href="{{ route('products.index') }}"
                        class="border px-8 py-4 rounded-xl font-semibold"
                    >
                        Cancelar
                    </a>
                </div>

            </form>
        </div>

    </div>

    <x-image-upload-optimizer />
</x-app-layout>
