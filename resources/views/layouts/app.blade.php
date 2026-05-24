@php
    $currentUser = auth()->user();
    $currentStore = $currentUser?->store;
    $hasPaidPlan = $currentUser && ($currentUser->plan ?: 'free') !== 'free';

    $theme = $currentStore
        ? $currentStore->dashboardTheme()
        : config('dashboard-themes.blush');

    $activeStyle = 'background: ' . $theme['secondary'] . '; color: ' . $theme['primary'] . ';';
    $normalStyle = 'color: ' . $theme['muted'] . ';';
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="{ sidebarOpen: false }">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Shopla') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body
    class="font-sans antialiased"
    style="background: {{ $theme['bg'] }}; color: {{ $theme['text'] }};"
>
    <div
        x-cloak
        x-show="sidebarOpen"
        x-transition.opacity
        @click="sidebarOpen = false"
        class="fixed inset-0 bg-black/40 z-40 lg:hidden"
    ></div>

    <aside
        class="shopla-sidebar fixed left-0 top-0 z-50 h-screen w-72 -translate-x-full border-r shadow-xl transition-transform duration-300 lg:shadow-none"
        :class="sidebarOpen ? 'translate-x-0' : ''"
        style="background: {{ $theme['bg'] }}; border-color: {{ $theme['border'] }};"
    >
        <div class="h-full flex flex-col px-5 py-4">
            <div class="mb-4">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
                    <div
                        class="w-10 h-10 rounded-2xl text-white flex items-center justify-center font-bold"
                        style="background: {{ $theme['primary'] }};"
                    >
                        S
                    </div>

                    <div>
                        <h1 class="font-bold text-lg" style="color: {{ $theme['text'] }};">
                            Shopla
                        </h1>

                        <p class="text-xs" style="color: {{ $theme['muted'] }};">
                            Painel da loja
                        </p>
                    </div>
                </a>
            </div>

            <nav
                class="space-y-3 flex-1 overflow-y-auto pr-1 shopla-scrollbar"
                style="--shopla-scrollbar-thumb: {{ $theme['primary'] }}"
            >
                <div class="space-y-1">
                    <p class="px-3 text-[10px] font-black tracking-[0.16em] uppercase" style="color: {{ $theme['muted'] }};">
                        Principal
                    </p>

                    <a
                        href="{{ route('dashboard') }}"
                        class="group flex items-center gap-3 px-3 py-2.5 rounded-2xl transition"
                        style="{{ request()->routeIs('dashboard') ? $activeStyle : $normalStyle }}"
                    >
                        <span class="h-8 w-8 rounded-xl flex items-center justify-center shrink-0" style="background: {{ request()->routeIs('dashboard') ? $theme['card'] : $theme['secondary'] }}; color: {{ $theme['primary'] }}">
                            <span class="text-base leading-none">&#x1F3E0;</span>
                        </span>
                        <span class="font-semibold text-sm">In&iacute;cio</span>
                    </a>
                </div>

                @if($currentStore)
                    <div class="space-y-1">
                        <p class="px-3 text-[10px] font-black tracking-[0.16em] uppercase" style="color: {{ $theme['muted'] }};">
                            Produtos
                        </p>

                        <a
                            href="{{ route('products.index') }}"
                            class="group flex items-center gap-3 px-3 py-2.5 rounded-2xl transition"
                            style="{{ request()->routeIs('products.*') ? $activeStyle : $normalStyle }}"
                        >
                            <span class="h-8 w-8 rounded-xl flex items-center justify-center shrink-0" style="background: {{ request()->routeIs('products.*') ? $theme['card'] : $theme['secondary'] }}; color: {{ $theme['primary'] }}">
                                <span class="text-base leading-none">&#x1F4E6;</span>
                            </span>
                            <span class="font-semibold text-sm">Cat&aacute;logo</span>
                        </a>

                        <a
                            href="{{ route('categories.index') }}"
                            class="group flex items-center gap-3 px-3 py-2.5 rounded-2xl transition"
                            style="{{ request()->routeIs('categories.*') ? $activeStyle : $normalStyle }}"
                        >
                            <span class="h-8 w-8 rounded-xl flex items-center justify-center shrink-0" style="background: {{ request()->routeIs('categories.*') ? $theme['card'] : $theme['secondary'] }}; color: {{ $theme['primary'] }}">
                                <span class="text-base leading-none">&#x1F5C2;&#xFE0F;</span>
                            </span>
                            <span class="font-semibold text-sm">Categorias</span>
                        </a>

                        <a
                            href="{{ route('stock.index') }}"
                            class="group flex items-center gap-3 px-3 py-2.5 rounded-2xl transition"
                            style="{{ request()->routeIs('stock.*') ? $activeStyle : $normalStyle }}"
                        >
                            <span class="h-8 w-8 rounded-xl flex items-center justify-center shrink-0" style="background: {{ request()->routeIs('stock.*') ? $theme['card'] : $theme['secondary'] }}; color: {{ $theme['primary'] }}">
                                <span class="text-base leading-none">&#x1F4CA;</span>
                            </span>
                            <span class="font-semibold text-sm">Estoque</span>
                        </a>
                    </div>

                    <div class="space-y-1">
                        <p class="px-3 text-[10px] font-black tracking-[0.16em] uppercase" style="color: {{ $theme['muted'] }};">
                            Vendas
                        </p>

                        <a
                            href="{{ route('orders.index') }}"
                            class="group flex items-center gap-3 px-3 py-2.5 rounded-2xl transition"
                            style="{{ request()->routeIs('orders.*') ? $activeStyle : $normalStyle }}"
                        >
                            <span class="h-8 w-8 rounded-xl flex items-center justify-center shrink-0" style="background: {{ request()->routeIs('orders.*') ? $theme['card'] : $theme['secondary'] }}; color: {{ $theme['primary'] }}">
                                <span class="text-base leading-none">&#x1F6CD;&#xFE0F;</span>
                            </span>
                            <span class="font-semibold text-sm">Pedidos</span>
                        </a>

                        <a
                            href="{{ route('rankings.index') }}"
                            class="group flex items-center gap-3 px-3 py-2.5 rounded-2xl transition"
                            style="{{ request()->routeIs('rankings.*') ? $activeStyle : $normalStyle }}"
                        >
                            <span class="h-8 w-8 rounded-xl flex items-center justify-center shrink-0" style="background: {{ request()->routeIs('rankings.*') ? $theme['card'] : $theme['secondary'] }}; color: {{ $theme['primary'] }}">
                                <span class="text-base leading-none">&#x1F3C6;</span>
                            </span>
                            <span class="font-semibold text-sm">Ranking</span>
                        </a>

                        <a
                            href="{{ route('finance.index') }}"
                            class="group flex items-center gap-3 px-3 py-2.5 rounded-2xl transition"
                            style="{{ request()->routeIs('finance.*') ? $activeStyle : $normalStyle }}"
                        >
                            <span class="h-8 w-8 rounded-xl flex items-center justify-center shrink-0" style="background: {{ request()->routeIs('finance.*') ? $theme['card'] : $theme['secondary'] }}; color: {{ $theme['primary'] }}">
                                <span class="text-base leading-none">&#x1F4B0;</span>
                            </span>
                            <span class="font-semibold text-sm">Financeiro</span>
                        </a>
                    </div>

                    <div class="space-y-1">
                        <p class="px-3 text-[10px] font-black tracking-[0.16em] uppercase" style="color: {{ $theme['muted'] }};">
                            Loja
                        </p>

                        <a
                            href="{{ route('store.edit') }}"
                            class="group flex items-center gap-3 px-3 py-2.5 rounded-2xl transition"
                            style="{{ request()->routeIs('store.edit') ? $activeStyle : $normalStyle }}"
                        >
                            <span class="h-8 w-8 rounded-xl flex items-center justify-center shrink-0" style="background: {{ request()->routeIs('store.edit') ? $theme['card'] : $theme['secondary'] }}; color: {{ $theme['primary'] }}">
                                <span class="text-base leading-none">&#x1F3EA;</span>
                            </span>
                            <span class="font-semibold text-sm">Minha Loja</span>
                        </a>

                        @unless($hasPaidPlan)
                            <a
                                href="{{ route('plans.index') }}"
                                class="group flex items-center gap-3 px-3 py-2.5 rounded-2xl transition"
                                style="{{ request()->routeIs('plans.*') ? $activeStyle : $normalStyle }}"
                            >
                                <span class="h-8 w-8 rounded-xl flex items-center justify-center shrink-0" style="background: {{ request()->routeIs('plans.*') ? $theme['card'] : $theme['secondary'] }}; color: {{ $theme['primary'] }}">
                                    <span class="text-base leading-none">&#x1F48E;</span>
                                </span>
                                <span class="font-semibold text-sm">Planos</span>
                            </a>
                        @endunless

                        <a
                            href="{{ route('store.public', $currentStore->slug) }}"
                            target="_blank"
                            class="group flex items-center gap-3 px-3 py-2.5 rounded-2xl transition"
                            style="{{ $normalStyle }}"
                        >
                            <span class="h-8 w-8 rounded-xl flex items-center justify-center shrink-0" style="background: {{ $theme['secondary'] }}; color: {{ $theme['primary'] }}">
                                <span class="text-base leading-none">&#x1F517;</span>
                            </span>
                            <span class="font-semibold text-sm">Abrir vitrine</span>
                        </a>
                    </div>
                @endif

                <div class="space-y-1">
                    <p class="px-3 text-[10px] font-black tracking-[0.16em] uppercase" style="color: {{ $theme['muted'] }};">
                        Conta
                    </p>

                    @if(auth()->user()?->isAdmin())
                        <a
                            href="{{ route('admin.dashboard') }}"
                            class="group flex items-center gap-3 px-3 py-2.5 rounded-2xl transition"
                            style="{{ request()->routeIs('admin.*') ? $activeStyle : $normalStyle }}"
                        >
                            <span class="h-8 w-8 rounded-xl flex items-center justify-center shrink-0" style="background: {{ request()->routeIs('admin.*') ? $theme['card'] : $theme['secondary'] }}; color: {{ $theme['primary'] }}">
                                <span class="text-base leading-none">&#x1F6E1;&#xFE0F;</span>
                            </span>
                            <span class="font-semibold text-sm">Admin</span>
                        </a>
                    @endif

                    <a
                        href="{{ route('profile.edit') }}"
                        class="group flex items-center gap-3 px-3 py-2.5 rounded-2xl transition"
                        style="{{ request()->routeIs('profile.edit') ? $activeStyle : $normalStyle }}"
                    >
                        <span class="h-8 w-8 rounded-xl flex items-center justify-center shrink-0" style="background: {{ request()->routeIs('profile.edit') ? $theme['card'] : $theme['secondary'] }}; color: {{ $theme['primary'] }}">
                            <span class="text-base leading-none">&#x2699;&#xFE0F;</span>
                        </span>
                        <span class="font-semibold text-sm">Configura&ccedil;&otilde;es</span>
                    </a>
                </div>
            </nav>

            <div class="mt-3 space-y-2">
                <div
                    class="rounded-2xl p-3 flex items-center gap-3"
                    style="background: linear-gradient(90deg, {{ $theme['secondary'] }}, {{ $theme['card'] }}); color: {{ $theme['primary'] }};"
                >
                    <span class="h-8 w-8 rounded-xl flex items-center justify-center shrink-0" style="background: {{ $theme['card'] }}">
                        <span class="text-base leading-none">&#x1F381;</span>
                    </span>

                    <div class="min-w-0">
                        <strong class="text-sm leading-tight block">B&ocirc;nus desbloqueado!</strong>
                        <p class="text-xs truncate" style="color: {{ $theme['muted'] }};">
                            Respostas que vendem
                        </p>
                    </div>
                </div>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <button
                        type="submit"
                        class="w-full flex items-center gap-3 px-3 py-2 rounded-2xl text-red-500 hover:bg-red-50"
                    >
                        <svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M10 17l5-5-5-5" />
                            <path d="M15 12H3" />
                            <path d="M21 19V5a2 2 0 0 0-2-2h-5" />
                        </svg>
                        <span class="font-semibold text-sm">Sair</span>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <div class="lg:pl-72 min-h-screen">
        <header
            class="lg:hidden sticky top-0 z-30 backdrop-blur border-b px-4 py-3 flex items-center justify-between"
            style="background: {{ $theme['bg'] }}E6; border-color: {{ $theme['border'] }};"
        >
            <button
                @click="sidebarOpen = true"
                class="w-11 h-11 rounded-2xl border shadow-sm flex items-center justify-center"
                style="background: {{ $theme['card'] }}; border-color: {{ $theme['border'] }}; color: {{ $theme['text'] }};"
            >
                <span class="text-xl leading-none">&#x2630;</span>
            </button>

            <div class="text-center">
                <p class="font-bold" style="color: {{ $theme['text'] }};">
                    Shopla
                </p>

                <p class="text-xs" style="color: {{ $theme['muted'] }};">
                    Painel
                </p>
            </div>

            @if($currentStore)
                <a
                    href="{{ route('store.public', $currentStore->slug) }}"
                    target="_blank"
                    class="w-11 h-11 rounded-2xl border shadow-sm flex items-center justify-center"
                    style="background: {{ $theme['card'] }}; border-color: {{ $theme['border'] }};"
                >
                    <span class="text-xl leading-none">&#x1F517;</span>
                </a>
            @else
                <div class="w-11"></div>
            @endif
        </header>

        @isset($header)
            <header
                class="shadow"
                style="background: {{ $theme['card'] }};"
            >
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endisset

        <main class="pb-24 lg:pb-0">
            {{ $slot }}
        </main>
    </div>

    @if($currentStore)
        <nav
            class="lg:hidden fixed bottom-0 left-0 right-0 z-40 border-t shadow-xl"
            style="background: {{ $theme['card'] }}; border-color: {{ $theme['border'] }};"
        >
            <div class="grid grid-cols-6 h-16 text-xs">
                <a
                    href="{{ route('dashboard') }}"
                    class="flex flex-col items-center justify-center gap-1"
                    style="{{ request()->routeIs('dashboard') ? 'color: '.$theme['primary'] : 'color: '.$theme['muted'] }}"
                >
                    <span class="text-xl leading-none">&#x1F3E0;</span>
                    <span>In&iacute;cio</span>
                </a>

                <a
                    href="{{ route('products.index') }}"
                    class="flex flex-col items-center justify-center gap-1"
                    style="{{ request()->routeIs('products.*') ? 'color: '.$theme['primary'] : 'color: '.$theme['muted'] }}"
                >
                    <span class="text-xl leading-none">&#x1F4E6;</span>
                    <span>Cat&aacute;logo</span>
                </a>

                <a
                    href="{{ route('orders.index') }}"
                    class="flex flex-col items-center justify-center gap-1"
                    style="{{ request()->routeIs('orders.*') ? 'color: '.$theme['primary'] : 'color: '.$theme['muted'] }}"
                >
                    <span class="text-xl leading-none">&#x1F6CD;&#xFE0F;</span>
                    <span>Pedidos</span>
                </a>

                <a
                    href="{{ route('stock.index') }}"
                    class="flex flex-col items-center justify-center gap-1"
                    style="{{ request()->routeIs('stock.*') ? 'color: '.$theme['primary'] : 'color: '.$theme['muted'] }}"
                >
                    <span class="text-xl leading-none">&#x1F4CA;</span>
                    <span>Estoque</span>
                </a>

                <a
                     href="{{ route('finance.index') }}"
                    class="flex flex-col items-center justify-center gap-1"
                    style="{{ request()->routeIs('finance.*') ? 'color: '.$theme['primary'] : 'color: '.$theme['muted'] }}"
                >
                    <span class="text-xl leading-none">&#x1F4B0;</span>
                    <span>Financeiro</span>
                </a>

                <a
                    href="{{ route('rankings.index') }}"
                    class="flex flex-col items-center justify-center gap-1"
                    style="{{ request()->routeIs('rankings.*') ? 'color: '.$theme['primary'] : 'color: '.$theme['muted'] }}"
                >
                    <span class="text-xl leading-none">&#x1F3C6;</span>
                    <span>Ranking</span>
                </a>
            </div>
        </nav>
    @endif
</body>
</html>
