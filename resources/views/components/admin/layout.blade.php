@props([
    'title' => 'Admin',
])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title }} - {{ config('app.name', 'Shopla') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800,900&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased bg-slate-50 text-slate-950">
    <div class="min-h-screen">
        <header class="sticky top-0 z-30 border-b border-slate-200 bg-white/90 backdrop-blur">
            <div class="mx-auto flex max-w-7xl flex-col gap-4 px-4 py-4 sm:px-6 lg:flex-row lg:items-center lg:justify-between lg:px-8">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3">
                    <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-slate-950 text-white shadow-lg shadow-slate-950/15">
                        <x-dashboard-icon name="settings" class="h-6 w-6" />
                    </span>

                    <span>
                        <span class="block text-lg font-black">Shopla Admin</span>
                        <span class="block text-xs font-bold uppercase tracking-[0.18em] text-slate-400">controle do sistema</span>
                    </span>
                </a>

                <nav class="flex flex-wrap items-center gap-2">
                    <a
                        href="{{ route('admin.dashboard') }}"
                        class="inline-flex items-center gap-2 rounded-2xl px-4 py-2 text-sm font-bold transition {{ request()->routeIs('admin.dashboard') ? 'bg-slate-950 text-white shadow-lg shadow-slate-950/15' : 'bg-white text-slate-600 ring-1 ring-slate-200 hover:text-slate-950' }}"
                    >
                        <x-dashboard-icon name="trend" class="h-4 w-4" />
                        Dashboard
                    </a>

                    <a
                        href="{{ route('admin.users.index') }}"
                        class="inline-flex items-center gap-2 rounded-2xl px-4 py-2 text-sm font-bold transition {{ request()->routeIs('admin.users.*') ? 'bg-slate-950 text-white shadow-lg shadow-slate-950/15' : 'bg-white text-slate-600 ring-1 ring-slate-200 hover:text-slate-950' }}"
                    >
                        <x-dashboard-icon name="store" class="h-4 w-4" />
                        Usuarios
                    </a>

                    <a
                        href="{{ route('admin.settings.index') }}"
                        class="inline-flex items-center gap-2 rounded-2xl px-4 py-2 text-sm font-bold transition {{ request()->routeIs('admin.settings.*') ? 'bg-slate-950 text-white shadow-lg shadow-slate-950/15' : 'bg-white text-slate-600 ring-1 ring-slate-200 hover:text-slate-950' }}"
                    >
                        <x-dashboard-icon name="settings" class="h-4 w-4" />
                        Configuracoes
                    </a>

                    <a
                        href="{{ route('dashboard') }}"
                        class="inline-flex items-center gap-2 rounded-2xl bg-pink-50 px-4 py-2 text-sm font-bold text-pink-700 ring-1 ring-pink-100 transition hover:bg-pink-100"
                    >
                        <x-dashboard-icon name="external" class="h-4 w-4" />
                        Painel da loja
                    </a>
                </nav>
            </div>
        </header>

        <main class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-6 rounded-3xl border border-emerald-100 bg-emerald-50 px-5 py-4 text-sm font-bold text-emerald-700">
                    {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="mb-6 rounded-3xl border border-red-100 bg-red-50 px-5 py-4 text-sm font-bold text-red-700">
                    Confira os dados informados antes de continuar.
                </div>
            @endif

            {{ $slot }}
        </main>
    </div>
</body>
</html>
