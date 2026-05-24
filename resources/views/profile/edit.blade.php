@php
    $store = $store ?? auth()->user()?->store;
    $theme = $store ? $store->dashboardTheme() : config('dashboard-themes.blush');
@endphp

<x-app-layout>
    <div class="min-h-screen pb-24" style="background: {{ $theme['bg'] }}; color: {{ $theme['text'] }};">
        <div class="max-w-5xl mx-auto px-4 py-8 space-y-8">
            <header>
                <p class="text-sm font-semibold tracking-widest uppercase" style="color: {{ $theme['muted'] }}">
                    Configuracoes
                </p>

                <h1 class="text-4xl md:text-5xl font-bold mt-2" style="font-family: serif;">
                    Conta e painel
                </h1>

                <p class="mt-3 max-w-2xl" style="color: {{ $theme['muted'] }}">
                    Gerencie seus dados, assinatura, senha e aparencia do painel.
                </p>
            </header>

            @if(session('success'))
                <div
                    class="p-4 rounded-2xl border font-semibold"
                    style="background: {{ $theme['secondary'] }}; color: {{ $theme['primary'] }}; border-color: {{ $theme['border'] }}"
                >
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="p-4 rounded-2xl border font-semibold bg-red-50 text-red-600 border-red-100">
                    {{ session('error') }}
                </div>
            @endif

            <section
                class="rounded-3xl p-6 md:p-8 border shadow-sm"
                style="background: {{ $theme['card'] }}; border-color: {{ $theme['border'] }}"
            >
                @include('profile.partials.subscription-management')
            </section>

            <section
                class="rounded-3xl p-6 md:p-8 border shadow-sm"
                style="background: {{ $theme['card'] }}; border-color: {{ $theme['border'] }}"
            >
                @include('profile.partials.dashboard-theme-form')
            </section>

            <section
                class="rounded-3xl p-6 md:p-8 border shadow-sm"
                style="background: {{ $theme['card'] }}; border-color: {{ $theme['border'] }}"
            >
                @include('profile.partials.update-profile-information-form')
            </section>

            <section
                class="rounded-3xl p-6 md:p-8 border shadow-sm"
                style="background: {{ $theme['card'] }}; border-color: {{ $theme['border'] }}"
            >
                @include('profile.partials.update-password-form')
            </section>

            <section
                class="rounded-3xl p-6 md:p-8 border shadow-sm"
                style="background: {{ $theme['card'] }}; border-color: {{ $theme['border'] }}"
            >
                @include('profile.partials.delete-user-form')
            </section>
        </div>
    </div>
</x-app-layout>
