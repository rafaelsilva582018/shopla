@php
    $supportPhone = preg_replace('/\D/', '', (string) config('services.whatsapp.support_number'));
    $whatsappUrl = $supportPhone ? 'https://wa.me/' . (str_starts_with($supportPhone, '55') ? $supportPhone : '55' . $supportPhone) . '?text=' . rawurlencode('Oi, vim pela pagina Sobre da Shopla e quero saber mais.') : route('contact');
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sobre a Shopla</title>
    <meta name="description" content="Conheca a Shopla, uma plataforma para pequenos negocios criarem vitrines online e venderem melhor pelo WhatsApp.">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#fff8f5] text-slate-950 antialiased">
    <main class="min-h-screen">
        <header class="border-b border-pink-100 bg-white/85 backdrop-blur">
            <div class="mx-auto flex h-20 max-w-6xl items-center justify-between px-4">
                <a href="/" class="flex items-center gap-3">
                    <span class="flex h-11 w-11 items-center justify-center rounded-2xl bg-pink-500 font-black text-white">S</span>
                    <span class="text-2xl font-black">Shopla</span>
                </a>
                <nav class="flex items-center gap-3 text-sm font-bold">
                    <a href="/" class="rounded-2xl px-4 py-2 hover:bg-pink-50">Inicio</a>
                    <a href="{{ route('contact') }}" class="rounded-2xl bg-pink-500 px-4 py-2 text-white">Contato</a>
                </nav>
            </div>
        </header>

        <section class="mx-auto grid max-w-6xl gap-10 px-4 py-16 lg:grid-cols-[1fr_.8fr] lg:items-center">
            <div>
                <p class="text-sm font-black uppercase tracking-[0.22em] text-pink-600">Sobre a Shopla</p>
                <h1 class="mt-4 text-5xl font-black leading-tight md:text-6xl">Uma vitrine simples para quem vende na conversa.</h1>
                <p class="mt-6 max-w-2xl text-lg leading-8 text-slate-600">
                    A Shopla nasceu para ajudar pequenos negocios a organizar produtos, pedidos e atendimento sem transformar a rotina em um sistema complicado.
                </p>
                <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                    <a href="{{ route('register') }}" class="rounded-2xl bg-pink-500 px-7 py-4 text-center font-black text-white shadow-lg shadow-pink-200">Criar minha loja</a>
                    <a href="{{ $whatsappUrl }}" target="_blank" class="rounded-2xl border border-pink-100 bg-white px-7 py-4 text-center font-black">Falar no WhatsApp</a>
                </div>
            </div>

            <div class="rounded-[2rem] border border-pink-100 bg-white p-6 shadow-xl shadow-pink-100/60">
                <div class="rounded-3xl bg-pink-50 p-5">
                    <p class="font-black text-pink-600">O que guia o produto</p>
                    <div class="mt-5 space-y-4">
                        @foreach([
                            'Vender precisa ser mais claro para o cliente.',
                            'Cadastrar produto deve ser rapido e visual.',
                            'O pedido tem que chegar organizado no WhatsApp.',
                            'A loja pequena tambem merece uma presenca profissional.',
                        ] as $item)
                            <div class="rounded-2xl bg-white p-4 font-semibold text-slate-700">{{ $item }}</div>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>
    </main>
</body>
</html>
