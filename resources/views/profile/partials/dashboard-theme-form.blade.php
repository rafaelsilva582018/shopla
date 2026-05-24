@php
    $store = auth()->user()?->store;
    $theme = $store ? $store->dashboardTheme() : config('dashboard-themes.blush');
    $dashboardThemes = config('dashboard-themes');
    $dashboardThemeMode = old('dashboard_theme_mode', ($store?->dashboard_theme ?? 'blush') === 'custom' ? 'custom' : 'preset');
    $selectedDashboardTheme = old('dashboard_theme', ($store?->dashboard_theme ?? 'blush') === 'custom' ? 'blush' : $store?->dashboard_theme);
    $selectedDashboardTheme = array_key_exists($selectedDashboardTheme, $dashboardThemes) ? $selectedDashboardTheme : 'blush';
@endphp

<section>
    <header class="flex items-start gap-4">
        <span class="w-12 h-12 rounded-2xl flex items-center justify-center" style="background: {{ $theme['secondary'] }}; color: {{ $theme['primary'] }}">
            <x-dashboard-icon name="settings" class="w-6 h-6" />
        </span>

        <div>
            <h2 class="text-xl font-bold">
                Aparencia do painel
            </h2>

            <p class="mt-1 text-sm" style="color: {{ $theme['muted'] }}">
                Escolha um tema pronto ou personalize as cores do painel do lojista.
            </p>
        </div>
    </header>

    @if(!$store)
        <div class="mt-6 rounded-3xl border p-5" style="border-color: {{ $theme['border'] }}; color: {{ $theme['muted'] }}">
            Crie sua loja para personalizar o painel.
        </div>
    @else
        <form
            method="POST"
            action="{{ route('profile.dashboard-theme.update') }}"
            x-data="{ mode: '{{ $dashboardThemeMode }}' }"
            class="mt-6 space-y-6"
        >
            @csrf
            @method('PUT')

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

            <div x-show="mode === 'preset'" x-transition style="{{ $dashboardThemeMode === 'preset' ? '' : 'display:none;' }}" class="grid sm:grid-cols-2 lg:grid-cols-4 gap-5">
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

                            <h3 class="font-bold" style="color: {{ $dashboardTheme['text'] }}">
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

            <div class="flex items-center gap-4">
                <button class="text-white px-6 py-3 rounded-2xl font-semibold" style="background: {{ $theme['primary'] }}">
                    Salvar aparencia
                </button>

                @if (session('status') === 'dashboard-theme-updated')
                    <p
                        x-data="{ show: true }"
                        x-show="show"
                        x-transition
                        x-init="setTimeout(() => show = false, 2000)"
                        class="text-sm"
                        style="color: {{ $theme['muted'] }}"
                    >
                        Aparencia atualizada.
                    </p>
                @endif
            </div>
        </form>
    @endif
</section>
