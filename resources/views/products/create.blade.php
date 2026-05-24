@php
    $theme = auth()->user()->store?->dashboardTheme() ?? config('dashboard-themes.blush');
@endphp

<x-app-layout>
    <div class="max-w-5xl mx-auto px-4 py-8">
        <div class="mb-8">
            <p class="text-sm font-semibold tracking-widest uppercase" style="color: {{ $theme['muted'] }}">
                Catálogo
            </p>

            <h1 class="text-4xl font-bold mt-1" style="color: {{ $theme['text'] }}">
                Novo produto
            </h1>

            <p class="mt-2" style="color: {{ $theme['muted'] }}">
                Cadastre as informações, adicione fotos e deixe o produto pronto para aparecer na vitrine.
            </p>
        </div>

        <form
            method="POST"
            action="{{ route('products.store') }}"
            enctype="multipart/form-data"
            data-optimize-images
            class="rounded-3xl border shadow-sm p-5 md:p-7 space-y-7"
            style="background: {{ $theme['card'] }}; border-color: {{ $theme['border'] }}"
        >
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

            <section>
                <div class="flex items-center gap-3 mb-5">
                    <span class="h-11 w-11 rounded-2xl flex items-center justify-center" style="background: {{ $theme['secondary'] }}; color: {{ $theme['primary'] }}">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M6 2h9l5 5v15H6z"></path>
                            <path d="M14 2v6h6"></path>
                            <path d="M9 13h6"></path>
                            <path d="M9 17h4"></path>
                        </svg>
                    </span>
                    <div>
                        <h2 class="font-bold text-xl">Dados do produto</h2>
                        <p class="text-sm" style="color: {{ $theme['muted'] }}">Nome, preço, categoria e disponibilidade.</p>
                    </div>
                </div>

                <div class="grid md:grid-cols-2 gap-5">
                    <div>
                        <label class="block mb-2 font-semibold">Nome do produto</label>
                        <input type="text" name="name" value="{{ old('name') }}" placeholder="Ex: Toalha personalizada" class="w-full border rounded-2xl p-4" style="border-color: {{ $theme['border'] }}" required>
                    </div>

                    <div>
                        <label class="block mb-2 font-semibold">Preço</label>
                        <input type="text" inputmode="decimal" name="price" value="{{ old('price') }}" placeholder="Ex: 19,90" class="w-full border rounded-2xl p-4" style="border-color: {{ $theme['border'] }}" required>
                    </div>

                    <div>
                        <label class="block mb-2 font-semibold">Categorias</label>
                        <div class="grid gap-2 rounded-2xl border p-3" style="border-color: {{ $theme['border'] }}">
                            @forelse($categories as $category)
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
                        <label class="block mb-2 font-semibold">Disponibilidade</label>
                        <select name="availability_status" class="w-full border rounded-2xl p-4" style="border-color: {{ $theme['border'] }}" required>
                            @foreach(\App\Models\Product::AVAILABILITY_STATUSES as $status => $label)
                                <option value="{{ $status }}" @selected(old('availability_status', 'sob_encomenda') === $status)>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block mb-2 font-semibold">Descrição</label>
                        <textarea name="description" rows="4" placeholder="Conte rapidamente o que é esse produto." class="w-full border rounded-2xl p-4" style="border-color: {{ $theme['border'] }}">{{ old('description') }}</textarea>
                    </div>

                    <div>
                        <label class="block mb-2 font-semibold">Quantidade em estoque</label>
                        <input type="number" min="0" name="stock_quantity" value="{{ old('stock_quantity') }}" placeholder="Deixe em branco para vender sem limite" class="w-full border rounded-2xl p-4" style="border-color: {{ $theme['border'] }}">
                        <p class="text-sm mt-2" style="color: {{ $theme['muted'] }}">Se informar uma quantidade, a venda será limitada. Se marcar como esgotado, o estoque será zero.</p>
                    </div>
                </div>
            </section>

            <section class="border-t pt-7" style="border-color: {{ $theme['border'] }}">
                <x-product-images-upload
                    :border="$theme['border']"
                    :primary="$theme['primary']"
                    :background="$theme['bg']"
                    :card="$theme['card']"
                    :muted="$theme['muted']"
                />
            </section>

            <section class="grid md:grid-cols-2 gap-4 border-t pt-7" style="border-color: {{ $theme['border'] }}">
                <label class="flex items-center gap-3 rounded-2xl p-4 border cursor-pointer" style="border-color: {{ $theme['border'] }}">
                    <input type="checkbox" name="is_active" value="1" class="rounded" checked>
                    <span class="font-semibold">Produto ativo</span>
                </label>

                <label class="flex items-center gap-3 rounded-2xl p-4 border cursor-pointer" style="border-color: {{ $theme['border'] }}">
                    <input type="checkbox" name="is_featured" value="1" class="rounded">
                    <span class="font-semibold">Produto em destaque</span>
                </label>
            </section>

            <div class="flex flex-col sm:flex-row gap-3">
                <button type="submit" class="text-white px-7 py-4 rounded-2xl font-bold inline-flex items-center justify-center gap-2" style="background: {{ $theme['primary'] }}">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2Z"></path>
                        <path d="M17 21v-8H7v8"></path>
                        <path d="M7 3v5h8"></path>
                    </svg>
                    Salvar produto
                </button>

                <a href="{{ route('products.index') }}" class="px-7 py-4 rounded-2xl font-bold border text-center" style="border-color: {{ $theme['border'] }}; color: {{ $theme['text'] }}">
                    Cancelar
                </a>
            </div>
        </form>
    </div>

    <x-image-upload-optimizer />
</x-app-layout>
