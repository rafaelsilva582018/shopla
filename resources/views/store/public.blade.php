@php
    $storefrontTheme = $store->storefrontTheme();
@endphp

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $store->name }}</title>
    @vite(['resources/css/app.css'])
    @livewireStyles
</head>

<body
    class="min-h-screen"
    style="background: {{ $storefrontTheme['background'] }}; color: {{ $storefrontTheme['text'] }};"
>

    <header
        class="sticky top-0 z-30 shadow-sm"
        style="background: {{ $storefrontTheme['secondary'] }};"
    >
        <div class="max-w-5xl mx-auto px-4 py-4 flex items-center justify-between gap-4">
            <div class="flex items-center gap-3 min-w-0">
                <div
                    class="w-12 h-12 rounded-2xl flex items-center justify-center text-white text-lg font-bold shadow-md overflow-hidden"
                    style="background: {{ $storefrontTheme['primary'] }};"
                >
                    @if($store->logo)
                        <img src="{{ asset('storage/' . $store->logo) }}" class="w-full h-full object-cover">
                    @else
                        {{ mb_substr($store->name, 0, 1) }}
                    @endif
                </div>

                <div class="min-w-0">
                    <h1 class="text-xl md:text-2xl font-bold truncate" style="color: {{ $storefrontTheme['text'] }};">
                        {{ $store->name }}
                    </h1>

                    @if($store->instagram)
                        <a
                            href="https://instagram.com/{{ $store->instagram }}"
                            target="_blank"
                            class="block text-sm font-semibold truncate"
                            style="color: {{ $storefrontTheme['muted'] }};"
                        >
                            {{ '@' . $store->instagram }}
                        </a>
                    @endif
                </div>
            </div>

            <div class="flex items-center gap-3">
                @if($store->whatsapp)
                    <a
                        href="https://wa.me/{{ preg_replace('/\D/', '', $store->whatsapp) }}"
                        target="_blank"
                        class="hidden sm:inline-flex items-center gap-2 text-white px-5 py-3 rounded-2xl font-semibold shadow-sm"
                        style="background: {{ $storefrontTheme['primary'] }};"
                    >
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M21 11.5a8.4 8.4 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.4 8.4 0 0 1-3.8-.9L3 21l1.9-5.7a8.4 8.4 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.4 8.4 0 0 1 3.8-.9h.5a8.5 8.5 0 0 1 8 8z" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        Fale conosco
                    </a>
                @endif

                <livewire:store-cart :store="$store" :storefront-theme="$storefrontTheme" />
            </div>
        </div>
    </header>

    <main class="max-w-5xl mx-auto px-4 py-8">
        <livewire:public-store :store="$store" :storefront-theme="$storefrontTheme" />
    </main>

    @livewireScripts
</body>
</html>
