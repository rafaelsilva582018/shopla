@php
    $supportPhone = preg_replace('/\D/', '', (string) config('services.whatsapp.support_number'));
    $normalizedPhone = $supportPhone && ! str_starts_with($supportPhone, '55') ? '55' . $supportPhone : $supportPhone;
    $defaultMessage = 'Oi, vim pela pagina de contato da Shopla e quero falar com o suporte.';
    $whatsappUrl = $normalizedPhone ? 'https://wa.me/' . $normalizedPhone . '?text=' . rawurlencode($defaultMessage) : null;
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Contato - Shopla</title>
    <meta name="description" content="Fale com a Shopla pelo WhatsApp para tirar duvidas sobre vitrines, planos e vendas pelo WhatsApp.">
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
                    <a href="{{ route('about') }}" class="rounded-2xl px-4 py-2 hover:bg-pink-50">Sobre</a>
                    <a href="{{ route('login') }}" class="rounded-2xl bg-white px-4 py-2 ring-1 ring-pink-100">Entrar</a>
                </nav>
            </div>
        </header>

        <section class="mx-auto grid max-w-6xl gap-10 px-4 py-16 lg:grid-cols-[.9fr_1.1fr] lg:items-start">
            <div>
                <p class="text-sm font-black uppercase tracking-[0.22em] text-pink-600">Contato</p>
                <h1 class="mt-4 text-5xl font-black leading-tight md:text-6xl">Fale com a Shopla.</h1>
                <p class="mt-6 max-w-xl text-lg leading-8 text-slate-600">
                    Tem duvida sobre planos, primeira loja, cadastro de produtos ou quer ajuda para comecar? Chame no WhatsApp.
                </p>

                <div class="mt-8 rounded-[2rem] border border-pink-100 bg-white p-5 shadow-xl shadow-pink-100/60">
                    <p class="text-xs font-black uppercase tracking-[0.2em] text-pink-600">Atendimento</p>
                    <h2 class="mt-2 text-2xl font-black">WhatsApp da Shopla</h2>
                    <p class="mt-2 text-slate-600">{{ $supportPhone ? '+' . $normalizedPhone : 'Configure SHOPLA_WHATSAPP no ambiente.' }}</p>

                    @if($whatsappUrl)
                        <a href="{{ $whatsappUrl }}" target="_blank" class="mt-5 inline-flex rounded-2xl bg-green-500 px-6 py-4 font-black text-white shadow-lg shadow-green-100">
                            Abrir WhatsApp
                        </a>
                    @endif
                </div>
            </div>

            <div class="rounded-[2rem] border border-pink-100 bg-white p-6 shadow-xl shadow-pink-100/60" x-data="{ message: 'Oi, vim pelo site da Shopla e quero tirar uma duvida.' }">
                <h2 class="text-2xl font-black">Enviar mensagem rapida</h2>
                <p class="mt-2 text-slate-600">Escreva uma mensagem e envie direto para o WhatsApp da Shopla.</p>

                <label for="contact-message" class="mt-6 block text-sm font-black text-slate-700">Mensagem</label>
                <textarea id="contact-message" x-model="message" rows="7" class="mt-2 w-full rounded-3xl border border-pink-100 bg-pink-50/40 px-5 py-4 outline-none focus:border-pink-300 focus:ring-4 focus:ring-pink-100"></textarea>

                @if($whatsappUrl)
                    <a
                        x-bind:href="'https://wa.me/{{ $normalizedPhone }}?text=' + encodeURIComponent(message)"
                        target="_blank"
                        class="mt-5 flex w-full items-center justify-center rounded-2xl bg-pink-500 px-6 py-4 font-black text-white shadow-lg shadow-pink-200"
                    >
                        Enviar no WhatsApp
                    </a>
                @else
                    <p class="mt-5 rounded-2xl bg-yellow-50 p-4 text-sm font-bold text-yellow-800">
                        Para ativar o envio, configure a variavel SHOPLA_WHATSAPP no servidor.
                    </p>
                @endif
            </div>
        </section>
    </main>
</body>
</html>
