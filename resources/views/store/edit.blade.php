@php
    $theme = $store->dashboardTheme();
    $dashboardThemes = config('dashboard-themes');
    $storeThemes = config('store-themes');
    $storeThemeMode = old('store_theme_mode', ($store->store_theme ?? 'custom') === 'custom' ? 'custom' : 'preset');
    $selectedStoreTheme = old('store_theme', ($store->store_theme ?? 'custom') === 'custom' ? 'candy' : $store->store_theme);
    $selectedStoreTheme = array_key_exists($selectedStoreTheme, $storeThemes) ? $selectedStoreTheme : 'candy';
    $currentStorefrontTheme = $store->storefrontTheme();
    $dashboardThemeMode = old('dashboard_theme_mode', ($store->dashboard_theme ?? 'blush') === 'custom' ? 'custom' : 'preset');
    $selectedDashboardTheme = old('dashboard_theme', ($store->dashboard_theme ?? 'blush') === 'custom' ? 'blush' : $store->dashboard_theme);
    $selectedDashboardTheme = array_key_exists($selectedDashboardTheme, $dashboardThemes) ? $selectedDashboardTheme : 'blush';
    $canChooseCustomSlug = auth()->user()->canChooseCustomSlug();
@endphp

<x-app-layout>
    <div
        class="min-h-screen pb-24"
        style="background: {{ $theme['bg'] }}; color: {{ $theme['text'] }};"
    >
        <div class="max-w-6xl mx-auto px-4 py-8">

            <div class="mb-8">
                <p class="text-sm font-semibold tracking-widest" style="color: {{ $theme['muted'] }}">
                    MINHA LOJA
                </p>

                <h1 class="text-4xl font-bold mt-1" style="font-family: serif;">
                    Configurações da sua loja ⚙️
                </h1>

                <p class="mt-2" style="color: {{ $theme['muted'] }}">
                    Personalize sua vitrine e painel.
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

            <form
                method="POST"
                action="{{ route('store.update') }}"
                enctype="multipart/form-data"
                data-optimize-images
                class="space-y-8"
            >
                @csrf
                @method('PUT')

                {{-- dados básicos --}}
                <section
                    class="rounded-3xl p-6 shadow-sm border"
                    style="background: {{ $theme['card'] }}; border-color: {{ $theme['border'] }}"
                >
                    <h2 class="font-bold tracking-widest mb-6">
                        INFORMAÇÕES DA LOJA
                    </h2>

                    <div class="grid md:grid-cols-2 gap-5">

                        <div>
                            <label class="block mb-2 font-semibold">
                                Nome da loja
                            </label>

                            <input
                                type="text"
                                name="name"
                                value="{{ old('name', $store->name) }}"
                                class="w-full border rounded-2xl p-4"
                                style="border-color: {{ $theme['border'] }}"
                                required
                            >
                        </div>

                        <div
                            @if($canChooseCustomSlug)
                                x-data="{
                                    slug: '{{ old('slug', $store->slug) }}',
                                    status: null,
                                    message: '',
                                    async checkSlug() {
                                        const clean = this.slug.trim();

                                        if (!clean) {
                                            this.status = null;
                                            this.message = 'Digite o link desejado.';
                                            return;
                                        }

                                        const response = await fetch('{{ route('onboarding.slug-check') }}?slug=' + encodeURIComponent(clean));
                                        const data = await response.json();

                                        this.slug = data.slug;
                                        this.status = data.available ? 'available' : 'unavailable';
                                        this.message = data.message;
                                    }
                                }"
                            @endif
                        >
                            <label class="block mb-2 font-semibold">
                                Link da vitrine
                            </label>

                            @if($canChooseCustomSlug)
                                <input
                                    type="text"
                                    name="slug"
                                    x-model="slug"
                                    @input.debounce.500ms="checkSlug"
                                    class="w-full border rounded-2xl p-4"
                                    style="border-color: {{ $theme['border'] }}"
                                    required
                                >

                                <p class="text-sm mt-2" style="color: {{ $theme['muted'] }}">
                                    {{ url('/') }}/<span x-text="slug || '{{ $store->slug }}'"></span>
                                </p>

                                <p
                                    x-show="message"
                                    x-text="message"
                                    class="text-sm mt-2 font-semibold"
                                    :class="status === 'available' ? 'text-green-600' : (status === 'unavailable' ? 'text-red-500' : '')"
                                    style="display: none;"
                                ></p>
                            @else
                                <div class="w-full border rounded-2xl p-4" style="border-color: {{ $theme['border'] }}; background: {{ $theme['bg'] }}">
                                    <p class="font-semibold">{{ route('store.public', $store->slug) }}</p>
                                    <p class="text-sm mt-2" style="color: {{ $theme['muted'] }}">
                                        No plano gratuito o link e gerado automaticamente. Assine um plano pago para escolher um link personalizado.
                                    </p>
                                </div>

                                <a href="{{ route('plans.index') }}" class="inline-flex mt-3 text-sm font-bold" style="color: {{ $theme['primary'] }}">
                                    Ver planos pagos
                                </a>
                            @endif

                            <x-input-error :messages="$errors->get('slug')" class="mt-2" />
                        </div>

                        <div>
                            <label class="block mb-2 font-semibold">
                                WhatsApp
                            </label>

                            <input
                                type="text"
                                name="whatsapp"
                                value="{{ old('whatsapp', $store->whatsapp) }}"
                                class="w-full border rounded-2xl p-4"
                                style="border-color: {{ $theme['border'] }}"
                            >
                        </div>

                        <div>
                            <label class="block mb-2 font-semibold">
                                Instagram
                            </label>

                            <input
                                type="text"
                                name="instagram"
                                value="{{ old('instagram', $store->instagram) }}"
                                placeholder="@sualoja"
                                class="w-full border rounded-2xl p-4"
                                style="border-color: {{ $theme['border'] }}"
                            >
                        </div>

                        <div>
                            <label class="block mb-2 font-semibold">
                                Descrição
                            </label>

                            <textarea
                                name="description"
                                rows="4"
                                class="w-full border rounded-2xl p-4"
                                style="border-color: {{ $theme['border'] }}"
                            >{{ old('description', $store->description) }}</textarea>
                        </div>

                    </div>
                </section>

                {{-- imagens --}}
                <section
                    class="rounded-3xl p-6 shadow-sm border"
                    style="background: {{ $theme['card'] }}; border-color: {{ $theme['border'] }}"
                >
                    <h2 class="font-bold tracking-widest mb-6">
                        IDENTIDADE VISUAL
                    </h2>

                    <div class="grid md:grid-cols-2 gap-6">

                        <div>
                            <x-upload-dropzone
                                name="logo"
                                label="Logo"
                                hint="Arraste ou clique para escolher a imagem que aparece no topo da vitrine."
                                :current="$store->logo ? asset('storage/' . $store->logo) : null"
                                :border="$theme['border']"
                                :primary="$theme['primary']"
                                :background="$theme['bg']"
                                :muted="$theme['muted']"
                            />

                            @if($store->logo)
                                <div class="mt-4">
                                    <label class="flex items-center gap-3 mt-4 cursor-pointer">
                                        <input
                                            type="checkbox"
                                            name="remove_logo"
                                            value="1"
                                            class="w-5 h-5"
                                        >

                                        <span class="text-red-500 font-semibold">
                                            Remover logo atual
                                        </span>
                                    </label>
                                </div>
                            @endif
                        </div>

                        <div>
                            <x-upload-dropzone
                                name="banner"
                                label="Banner"
                                hint="Use uma imagem horizontal para destacar sua loja."
                                :current="$store->banner ? asset('storage/' . $store->banner) : null"
                                :border="$theme['border']"
                                :primary="$theme['primary']"
                                :background="$theme['bg']"
                                :muted="$theme['muted']"
                            />

                            @if($store->banner)
                                <div class="mt-4">
                                    <label class="flex items-center gap-3 mt-4 cursor-pointer">
                                        <input
                                            type="checkbox"
                                            name="remove_banner"
                                            value="1"
                                            class="w-5 h-5"
                                        >

                                        <span class="text-red-500 font-semibold">
                                            Remover banner atual
                                        </span>
                                    </label>
                                </div>
                            @endif
                        </div>

                    </div>
                </section>

                {{-- cores vitrine --}}
                <section
                    x-data="{ mode: '{{ $storeThemeMode }}' }"
                    class="rounded-3xl p-6 shadow-sm border"
                    style="background: {{ $theme['card'] }}; border-color: {{ $theme['border'] }}"
                >
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-7">
                        <h2 class="font-bold tracking-widest">
                            TEMA DA VITRINE
                        </h2>

                    <div class="inline-flex rounded-2xl border p-1 bg-white" style="border-color: {{ $theme['border'] }}">
                        <label class="cursor-pointer">
                            <input type="radio" name="store_theme_mode" value="preset" class="hidden" x-model="mode">
                            <span class="block px-5 py-3 rounded-xl font-semibold" :class="mode === 'preset' ? 'text-white' : ''" :style="mode === 'preset' ? 'background: {{ $theme['primary'] }}' : ''">
                                Temas prontos
                            </span>
                        </label>

                        <label class="cursor-pointer">
                            <input type="radio" name="store_theme_mode" value="custom" class="hidden" x-model="mode">
                            <span class="block px-5 py-3 rounded-xl font-semibold" :class="mode === 'custom' ? 'text-white' : ''" :style="mode === 'custom' ? 'background: {{ $theme['primary'] }}' : ''">
                                Personalizar cores
                            </span>
                        </label>
                    </div>
                    </div>

                    <div x-show="mode === 'preset'" x-transition style="{{ $storeThemeMode === 'preset' ? '' : 'display:none;' }}" class="mb-6">
                        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6">
                            @foreach($storeThemes as $key => $storeTheme)
                                <label class="cursor-pointer">
                                    <input
                                        type="radio"
                                        name="store_theme"
                                        value="{{ $key }}"
                                        class="hidden peer"
                                        @checked($selectedStoreTheme === $key)
                                    >

                                    <div
                                        class="rounded-3xl p-5 border-2 min-h-52 peer-checked:ring-4 transition"
                                        style="background: {{ $storeTheme['background'] }}; border-color: {{ $storeTheme['secondary'] }}"
                                    >
                                        <div
                                            class="h-24 rounded-2xl mb-5"
                                            style="background: linear-gradient(135deg, {{ $storeTheme['primary'] }}, {{ $storeTheme['secondary'] }})"
                                        ></div>

                                        <h3 class="font-bold" style="color: {{ $storeTheme['text'] }}">
                                            {{ $storeTheme['name'] }}
                                        </h3>

                                        <div class="flex gap-3 mt-5">
                                            <span class="w-5 h-5 rounded-full border" style="background: {{ $storeTheme['primary'] }}"></span>
                                            <span class="w-5 h-5 rounded-full border" style="background: {{ $storeTheme['secondary'] }}"></span>
                                            <span class="w-5 h-5 rounded-full border" style="background: {{ $storeTheme['background'] }}"></span>
                                            <span class="w-5 h-5 rounded-full border" style="background: {{ $storeTheme['text'] }}"></span>
                                            <span class="w-5 h-5 rounded-full border" style="background: {{ $storeTheme['border'] }}"></span>
                                        </div>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <div x-show="mode === 'custom'" x-transition style="{{ $storeThemeMode === 'custom' ? '' : 'display:none;' }}" class="grid md:grid-cols-4 gap-5">
                        <div>
                            <label class="block mb-2 font-semibold">Principal</label>
                            <input type="color" name="primary_color" value="{{ old('primary_color', $store->primary_color) }}" class="w-full h-16 rounded-2xl">
                        </div>

                        <div>
                            <label class="block mb-2 font-semibold">Secundária</label>
                            <input type="color" name="secondary_color" value="{{ old('secondary_color', $store->secondary_color) }}" class="w-full h-16 rounded-2xl">
                        </div>

                        <div>
                            <label class="block mb-2 font-semibold">Fundo</label>
                            <input type="color" name="background_color" value="{{ old('background_color', $store->background_color) }}" class="w-full h-16 rounded-2xl">
                        </div>

                        <div>
                            <label class="block mb-2 font-semibold">Texto</label>
                            <input type="color" name="text_color" value="{{ old('text_color', $store->text_color) }}" class="w-full h-16 rounded-2xl">
                        </div>

                        <div>
                            <label class="block mb-2 font-semibold">Cards</label>
                            <input type="color" name="store_card_color" value="{{ old('store_card_color', $store->store_card_color ?? $currentStorefrontTheme['card']) }}" class="w-full h-16 rounded-2xl">
                        </div>

                        <div>
                            <label class="block mb-2 font-semibold">Texto suave</label>
                            <input type="color" name="store_muted_color" value="{{ old('store_muted_color', $store->store_muted_color ?? $currentStorefrontTheme['muted']) }}" class="w-full h-16 rounded-2xl">
                        </div>

                        <div>
                            <label class="block mb-2 font-semibold">Bordas</label>
                            <input type="color" name="store_border_color" value="{{ old('store_border_color', $store->store_border_color ?? $currentStorefrontTheme['border']) }}" class="w-full h-16 rounded-2xl">
                        </div>

                        <div>
                            <label class="block mb-2 font-semibold">Etiqueta</label>
                            <input type="color" name="store_badge_color" value="{{ old('store_badge_color', $store->store_badge_color ?? $currentStorefrontTheme['badge']) }}" class="w-full h-16 rounded-2xl">
                        </div>

                        <div>
                            <label class="block mb-2 font-semibold">Texto da etiqueta</label>
                            <input type="color" name="store_badge_text_color" value="{{ old('store_badge_text_color', $store->store_badge_text_color ?? $currentStorefrontTheme['badge_text']) }}" class="w-full h-16 rounded-2xl">
                        </div>
                    </div>
                </section>

                {{-- dashboard themes --}}
                <section
                    x-data="{ mode: '{{ $dashboardThemeMode }}' }"
                    class="rounded-3xl p-6 shadow-sm border"
                    style="background: {{ $theme['card'] }}; border-color: {{ $theme['border'] }}"
                >
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
                        <h2 class="font-bold tracking-widest">
                            TEMA DO PAINEL
                        </h2>

                        <div class="inline-flex rounded-2xl border p-1 bg-white" style="border-color: {{ $theme['border'] }}">
                            <label class="cursor-pointer">
                                <input type="radio" name="dashboard_theme_mode" value="preset" class="hidden" x-model="mode">
                                <span class="block px-5 py-3 rounded-xl font-semibold" :class="mode === 'preset' ? 'text-white' : ''" :style="mode === 'preset' ? 'background: {{ $theme['primary'] }}' : ''">
                                    Temas prontos
                                </span>
                            </label>

                            <label class="cursor-pointer">
                                <input type="radio" name="dashboard_theme_mode" value="custom" class="hidden" x-model="mode">
                                <span class="block px-5 py-3 rounded-xl font-semibold" :class="mode === 'custom' ? 'text-white' : ''" :style="mode === 'custom' ? 'background: {{ $theme['primary'] }}' : ''">
                                    Personalizar cores
                                </span>
                            </label>
                        </div>
                    </div>

                    <div x-show="mode === 'preset'" x-transition style="{{ $dashboardThemeMode === 'preset' ? '' : 'display:none;' }}" class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6">
                        @foreach($dashboardThemes as $key => $dashboardTheme)
                            <label class="cursor-pointer">
                                <input
                                    type="radio"
                                    name="dashboard_theme"
                                    value="{{ $key }}"
                                    class="hidden peer"
                                    @checked($selectedDashboardTheme === $key)
                                >

                                <div
                                    class="rounded-3xl p-5 border-2 min-h-52 peer-checked:ring-4 transition"
                                    style="background: {{ $dashboardTheme['bg'] }}; border-color: {{ $dashboardTheme['border'] }}"
                                >
                                    <div
                                        class="h-24 rounded-2xl mb-5"
                                        style="background: linear-gradient(135deg, {{ $dashboardTheme['primary'] }}, {{ $dashboardTheme['secondary'] }})"
                                    ></div>

                                    <h3
                                        class="font-bold"
                                        style="color: {{ $dashboardTheme['text'] }}"
                                    >
                                        {{ $dashboardTheme['name'] }}
                                    </h3>

                                    <div class="flex gap-3 mt-5">
                                        <span class="w-5 h-5 rounded-full border" style="background: {{ $dashboardTheme['primary'] }}"></span>
                                        <span class="w-5 h-5 rounded-full border" style="background: {{ $dashboardTheme['secondary'] }}"></span>
                                        <span class="w-5 h-5 rounded-full border" style="background: {{ $dashboardTheme['bg'] }}"></span>
                                        <span class="w-5 h-5 rounded-full border" style="background: {{ $dashboardTheme['text'] }}"></span>
                                    </div>
                                </div>
                            </label>
                        @endforeach
                    </div>

                    <div x-show="mode === 'custom'" x-transition style="{{ $dashboardThemeMode === 'custom' ? '' : 'display:none;' }}">
                        <div class="grid md:grid-cols-4 gap-5">
                            <div>
                                <label class="block mb-2 font-semibold">Fundo</label>
                                <input type="color" name="dashboard_bg_color" value="{{ old('dashboard_bg_color', $store->dashboard_bg_color ?? $theme['bg']) }}" class="w-full h-16 rounded-2xl">
                            </div>

                            <div>
                                <label class="block mb-2 font-semibold">Cards</label>
                                <input type="color" name="dashboard_card_color" value="{{ old('dashboard_card_color', $store->dashboard_card_color ?? $theme['card']) }}" class="w-full h-16 rounded-2xl">
                            </div>

                            <div>
                                <label class="block mb-2 font-semibold">Principal</label>
                                <input type="color" name="dashboard_primary_color" value="{{ old('dashboard_primary_color', $store->dashboard_primary_color ?? $theme['primary']) }}" class="w-full h-16 rounded-2xl">
                            </div>

                            <div>
                                <label class="block mb-2 font-semibold">Secundaria</label>
                                <input type="color" name="dashboard_secondary_color" value="{{ old('dashboard_secondary_color', $store->dashboard_secondary_color ?? $theme['secondary']) }}" class="w-full h-16 rounded-2xl">
                            </div>

                            <div>
                                <label class="block mb-2 font-semibold">Texto</label>
                                <input type="color" name="dashboard_text_color" value="{{ old('dashboard_text_color', $store->dashboard_text_color ?? $theme['text']) }}" class="w-full h-16 rounded-2xl">
                            </div>

                            <div>
                                <label class="block mb-2 font-semibold">Texto suave</label>
                                <input type="color" name="dashboard_muted_color" value="{{ old('dashboard_muted_color', $store->dashboard_muted_color ?? $theme['muted']) }}" class="w-full h-16 rounded-2xl">
                            </div>

                            <div>
                                <label class="block mb-2 font-semibold">Bordas</label>
                                <input type="color" name="dashboard_border_color" value="{{ old('dashboard_border_color', $store->dashboard_border_color ?? $theme['border']) }}" class="w-full h-16 rounded-2xl">
                            </div>
                        </div>
                    </div>
                </section>

                {{-- ações --}}
                <div class="flex flex-wrap gap-4">
                    <button
                        class="text-white px-8 py-4 rounded-2xl font-semibold shadow"
                        style="background: {{ $theme['primary'] }}"
                    >
                        Salvar alterações
                    </button>

                    <a
                        href="{{ route('store.public', $store->slug) }}"
                        target="_blank"
                        class="px-8 py-4 rounded-2xl font-semibold border"
                        style="background: {{ $theme['card'] }}; color: {{ $theme['primary'] }}; border-color: {{ $theme['border'] }}"
                    >
                        Ver vitrine
                    </a>
                </div>

            </form>
        </div>
    </div>

    <x-image-upload-optimizer />
</x-app-layout>
