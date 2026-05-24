<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Shopla') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-[#3c2430] antialiased">
        <div class="min-h-screen bg-[#fff7f9] flex items-center justify-center px-4 py-8">
            <div class="w-full max-w-6xl grid lg:grid-cols-[.9fr_1.1fr] bg-white rounded-[2rem] shadow-xl border border-pink-100 overflow-hidden">
                <div class="hidden lg:flex flex-col justify-between p-10 bg-gradient-to-br from-pink-500 to-pink-200 text-white relative overflow-hidden">
                    <div class="absolute -right-20 -top-20 w-64 h-64 rounded-full bg-white/20"></div>
                    <div class="absolute left-1/2 -bottom-28 w-72 h-72 rounded-full bg-white/15"></div>

                    <div class="relative z-10">
                        <div class="w-14 h-14 rounded-2xl bg-white/20 border border-white/30 flex items-center justify-center font-bold text-xl">
                            S
                        </div>

                        <h1 class="text-4xl font-bold mt-8" style="font-family: serif;">
                            Shopla
                        </h1>

                        <p class="text-white/85 mt-3 text-lg">
                            Seu painel para vender, organizar produtos e cuidar da sua vitrine online.
                        </p>
                    </div>

                    <div class="relative z-10 grid grid-cols-2 gap-3 text-sm">
                        <div class="rounded-2xl bg-white/15 border border-white/25 p-4">
                            Catálogo
                        </div>
                        <div class="rounded-2xl bg-white/15 border border-white/25 p-4">
                            Pedidos
                        </div>
                        <div class="rounded-2xl bg-white/15 border border-white/25 p-4">
                            Estoque
                        </div>
                        <div class="rounded-2xl bg-white/15 border border-white/25 p-4">
                            Ranking
                        </div>
                    </div>
                </div>

                <div class="p-6 sm:p-10">
                    <div class="lg:hidden mb-8 flex items-center gap-3">
                        <div class="w-12 h-12 rounded-2xl bg-pink-500 text-white flex items-center justify-center font-bold">
                            S
                        </div>
                        <div>
                            <h1 class="font-bold text-xl">Shopla</h1>
                            <p class="text-sm text-pink-700/70">Painel da loja</p>
                        </div>
                    </div>

                    {{ $slot }}
                </div>
            </div>
        </div>
    </body>
</html>
