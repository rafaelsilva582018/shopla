@php
    $store = auth()->user()->store;
    $theme = $store->dashboardTheme();
@endphp

<x-app-layout>
    <div
        x-data="{
            createModal: false,
            editModal: false,
            editCategoryId: null,
            editCategoryName: '',
            editAction: ''
        }"
        class="min-h-screen pb-24"
        style="background: {{ $theme['bg'] }}; color: {{ $theme['text'] }};"
    >
        <div class="max-w-6xl mx-auto px-4 py-8">

            <div class="mb-8 flex items-start justify-between gap-4">
                <div>
                    <p class="text-sm font-semibold tracking-widest" style="color: {{ $theme['muted'] }}">
                        CATÁLOGO
                    </p>

                    <h1 class="text-4xl font-bold mt-1" style="font-family: serif;">
                        Categorias 🗂️
                    </h1>

                    <p class="mt-2" style="color: {{ $theme['muted'] }}">
                        Organize seus produtos para facilitar a navegação da vitrine.
                    </p>
                </div>

                <button
                    @click="createModal = true"
                    class="text-white px-6 py-4 rounded-2xl font-semibold shadow"
                    style="background: {{ $theme['primary'] }}"
                >
                    + Nova categoria
                </button>
            </div>

            @if(session('success'))
                <div
                    class="p-4 rounded-2xl mb-6 border"
                    style="background: {{ $theme['secondary'] }}; color: {{ $theme['primary'] }}; border-color: {{ $theme['border'] }}"
                >
                    {{ session('success') }}
                </div>
            @endif

            <div class="grid grid-cols-2 md:grid-cols-3 gap-4 mb-8">
                <div
                    class="rounded-3xl p-5 border shadow-sm"
                    style="background: {{ $theme['card'] }}; border-color: {{ $theme['border'] }}"
                >
                    <p class="text-sm font-semibold" style="color: {{ $theme['muted'] }}">TOTAL</p>
                    <h2 class="text-3xl font-bold mt-2">{{ $categories->count() }}</h2>
                </div>

                <div
                    class="rounded-3xl p-5 border shadow-sm"
                    style="background: {{ $theme['card'] }}; border-color: {{ $theme['border'] }}"
                >
                    <p class="text-sm font-semibold" style="color: {{ $theme['muted'] }}">PRODUTOS</p>
                    <h2 class="text-3xl font-bold mt-2">{{ $store->products()->count() }}</h2>
                </div>

                <div
                    class="rounded-3xl p-5 border shadow-sm"
                    style="background: {{ $theme['card'] }}; border-color: {{ $theme['border'] }}"
                >
                    <p class="text-sm font-semibold" style="color: {{ $theme['muted'] }}">VITRINE</p>

                    <a
                        href="{{ route('store.public', $store->slug) }}"
                        target="_blank"
                        class="inline-block font-bold mt-2"
                        style="color: {{ $theme['primary'] }}"
                    >
                        Abrir loja ↗
                    </a>
                </div>
            </div>

            <section>
                <div class="flex items-center justify-between mb-3">
                    <h2 class="font-bold tracking-widest">
                        LISTA DE CATEGORIAS
                    </h2>

                    <a
                        href="{{ route('products.index') }}"
                        class="font-medium"
                        style="color: {{ $theme['primary'] }}"
                    >
                        Produtos ›
                    </a>
                </div>

                <div class="space-y-4">
                    @forelse($categories as $category)
                        <div
                            class="rounded-3xl p-5 border shadow-sm"
                            style="background: {{ $theme['card'] }}; border-color: {{ $theme['border'] }}"
                        >
                            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">

                                <div class="flex items-center gap-4">
                                    <div
                                        class="w-14 h-14 rounded-2xl flex items-center justify-center text-2xl"
                                        style="background: {{ $theme['secondary'] }}"
                                    >
                                        🗂️
                                    </div>

                                    <div>
                                        <h3 class="font-bold text-lg">
                                            {{ $category->name }}
                                        </h3>

                                        <p class="text-sm mt-1" style="color: {{ $theme['muted'] }}">
                                            {{ $category->products()->count() }} produto(s)
                                        </p>
                                    </div>
                                </div>

                                <div class="flex gap-2">
                                    <button
                                        @click="
                                            editModal = true;
                                            editCategoryId = {{ $category->id }};
                                            editCategoryName = '{{ addslashes($category->name) }}';
                                            editAction = '/minha-loja/categorias/{{ $category->id }}';
                                        "
                                        class="px-5 py-3 rounded-2xl font-semibold border"
                                        style="background: {{ $theme['secondary'] }}; color: {{ $theme['primary'] }}; border-color: {{ $theme['border'] }}"
                                    >
                                        Editar
                                    </button>

                                    <form method="POST" action="{{ route('categories.destroy', $category) }}">
                                        @csrf
                                        @method('DELETE')

                                        <button
                                            onclick="return confirm('Deseja excluir esta categoria?')"
                                            class="px-5 py-3 rounded-2xl font-semibold text-red-500 bg-red-50"
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
                            <div class="text-5xl mb-3">🗂️</div>

                            <p class="text-lg font-semibold">
                                Nenhuma categoria cadastrada.
                            </p>
                        </div>
                    @endforelse
                </div>
            </section>

        </div>

        <!-- MODAL CRIAR -->
        <div
            x-show="createModal"
            x-transition
            class="fixed inset-0 z-50 flex items-center justify-center px-4"
            style="display:none;"
        >
            <div class="absolute inset-0 bg-black/60" @click="createModal = false"></div>

            <div
                class="relative w-full max-w-lg rounded-3xl p-6 shadow-2xl"
                style="background: {{ $theme['card'] }}; color: {{ $theme['text'] }}"
            >
                <div class="flex items-center justify-between gap-4 mb-6">
                    <div class="flex items-center gap-4 min-w-0">
                        <span class="h-14 w-14 shrink-0 rounded-2xl flex items-center justify-center shadow-sm" style="background: {{ $theme['secondary'] }}; color: {{ $theme['primary'] }}">
                            <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                                <path d="M4 7h16"></path>
                                <path d="M4 12h10"></path>
                                <path d="M4 17h7"></path>
                                <path d="M17 14v6"></path>
                                <path d="M14 17h6"></path>
                            </svg>
                        </span>

                        <div class="min-w-0">
                            <p class="text-xs font-bold tracking-widest uppercase" style="color: {{ $theme['muted'] }}">Organizacao</p>
                            <h2 class="text-2xl md:text-3xl font-black leading-tight truncate">Nova categoria</h2>
                            <p class="text-sm mt-1" style="color: {{ $theme['muted'] }}">Crie um grupo para organizar seus produtos.</p>
                        </div>
                    </div>

                    <button type="button" @click="createModal = false" class="h-11 w-11 shrink-0 rounded-2xl flex items-center justify-center transition hover:scale-105" style="background: {{ $theme['secondary'] }}; color: {{ $theme['text'] }}" aria-label="Fechar">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                            <path d="M18 6 6 18"></path>
                            <path d="m6 6 12 12"></path>
                        </svg>
                    </button>
                </div>

                <form method="POST" action="{{ route('categories.store') }}" class="space-y-4">
                    @csrf

                    <input
                        type="text"
                        name="name"
                        placeholder="Nome da categoria"
                        class="w-full border rounded-2xl p-4"
                        style="border-color: {{ $theme['border'] }}"
                        required
                    >

                    <button
                        class="w-full text-white py-4 rounded-2xl font-semibold"
                        style="background: {{ $theme['primary'] }}"
                    >
                        Criar categoria
                    </button>
                </form>
            </div>
        </div>

        <!-- MODAL EDITAR -->
        <div
            x-show="editModal"
            x-transition
            class="fixed inset-0 z-50 flex items-center justify-center px-4"
            style="display:none;"
        >
            <div class="absolute inset-0 bg-black/60" @click="editModal = false"></div>

            <div
                class="relative w-full max-w-lg rounded-3xl p-6 shadow-2xl"
                style="background: {{ $theme['card'] }}; color: {{ $theme['text'] }}"
            >
                <div class="flex items-center justify-between gap-4 mb-6">
                    <div class="flex items-center gap-4 min-w-0">
                        <span class="h-14 w-14 shrink-0 rounded-2xl flex items-center justify-center shadow-sm" style="background: {{ $theme['secondary'] }}; color: {{ $theme['primary'] }}">
                            <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                                <path d="M4 7h10"></path>
                                <path d="M4 12h8"></path>
                                <path d="M4 17h5"></path>
                                <path d="M16.5 10.5a2.1 2.1 0 0 1 3 3L14 19l-4 1 1-4z"></path>
                            </svg>
                        </span>

                        <div class="min-w-0">
                            <p class="text-xs font-bold tracking-widest uppercase" style="color: {{ $theme['muted'] }}">Ajuste</p>
                            <h2 class="text-2xl md:text-3xl font-black leading-tight truncate">Editar categoria</h2>
                            <p class="text-sm mt-1" style="color: {{ $theme['muted'] }}">Atualize o nome usado nos filtros da vitrine.</p>
                        </div>
                    </div>

                    <button type="button" @click="editModal = false" class="h-11 w-11 shrink-0 rounded-2xl flex items-center justify-center transition hover:scale-105" style="background: {{ $theme['secondary'] }}; color: {{ $theme['text'] }}" aria-label="Fechar">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                            <path d="M18 6 6 18"></path>
                            <path d="m6 6 12 12"></path>
                        </svg>
                    </button>
                </div>

                <form :action="editAction" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <input
                        type="text"
                        name="name"
                        x-model="editCategoryName"
                        class="w-full border rounded-2xl p-4"
                        style="border-color: {{ $theme['border'] }}"
                        required
                    >

                    <button
                        class="w-full text-white py-4 rounded-2xl font-semibold"
                        style="background: {{ $theme['primary'] }}"
                    >
                        Salvar alterações
                    </button>
                </form>
            </div>
        </div>

    </div>
</x-app-layout>
