@php
    $plans = app(\App\Services\Plans\PlanCatalog::class)->all();
    $supportPhone = preg_replace('/\D/', '', (string) config('services.whatsapp.support_number'));
    $supportPhone = $supportPhone && ! str_starts_with($supportPhone, '55') ? '55' . $supportPhone : $supportPhone;
    $supportMessage = 'Oi, vim pelo site da Shopla e quero tirar uma duvida.';

    $painPoints = [
        ['title' => 'Cliente pergunta e some', 'text' => 'No WhatsApp puro, voce manda foto, preco e descricao toda vez. A compra esfria antes do pedido ficar claro.'],
        ['title' => 'Catalogo espalhado', 'text' => 'Produto fica no story, no destaque, na conversa antiga e em fotos soltas. O cliente nao encontra o que quer.'],
        ['title' => 'Pedido chega baguncado', 'text' => 'Falta tamanho, cor, endereco, observacao e quantidade. Voce perde tempo perguntando tudo de novo.'],
    ];

    $proofPoints = [
        ['icon' => 'home', 'title' => 'Publique hoje', 'text' => 'Crie a vitrine, cadastre os primeiros produtos e compartilhe o link em poucos passos.'],
        ['icon' => 'orders', 'title' => 'Pedido mais claro', 'text' => 'O cliente escolhe os itens, informa os dados e envia tudo organizado para o WhatsApp.'],
        ['icon' => 'link', 'title' => 'Um link para vender', 'text' => 'Use na bio, nos grupos, nas conversas e nos anuncios sem precisar enviar foto por foto.'],
    ];

    $useCases = [
        ['icon' => 'gift', 'title' => 'Personalizados e presentes', 'text' => 'Mostre modelos, opcoes, nomes, cores e observacoes sem perder nada na conversa.'],
        ['icon' => 'store', 'title' => 'Doces, bolos e encomendas', 'text' => 'Organize sabores, tamanhos, retirada, entrega e pedidos com detalhes do cliente.'],
        ['icon' => 'package', 'title' => 'Roupas e acessorios', 'text' => 'Cadastre fotos, variacoes, pronta entrega e estoque para evitar venda duplicada.'],
        ['icon' => 'category', 'title' => 'Catalogos pequenos', 'text' => 'Comece com poucos produtos e cresca o catalogo conforme a loja vender mais.'],
    ];

    $features = [
        ['icon' => 'store', 'title' => 'Pare de reenviar foto por foto', 'text' => 'Mostre seus produtos em uma vitrine bonita com busca, categorias, card ou lista.'],
        ['icon' => 'orders', 'title' => 'Receba pedidos mais completos', 'text' => 'O carrinho organiza itens, quantidades, dados do cliente e observacoes antes da conversa.'],
        ['icon' => 'package', 'title' => 'Ajude o cliente a decidir', 'text' => 'Cadastre ate tres imagens, variacoes, descricoes e produtos em destaque para reduzir duvidas.'],
        ['icon' => 'stock', 'title' => 'Evite vender o que acabou', 'text' => 'Defina estoque, pronta entrega, sob encomenda ou esgotado de forma simples por produto.'],
        ['icon' => 'trophy', 'title' => 'Venda mais o que ja funciona', 'text' => 'Veja os mais vendidos e use esses produtos como destaque na sua divulgacao.'],
        ['icon' => 'external', 'title' => 'Tenha um endereco mais profissional', 'text' => 'Comece com link automatico no gratis ou escolha um slug curto quando assinar um plano.'],
        ['icon' => 'settings', 'title' => 'Gerencie sem complicacao', 'text' => 'Acompanhe inicio, pedidos, produtos, estoque, ranking, loja, assinatura e configuracoes.'],
    ];

    $steps = [
        ['number' => '01', 'title' => 'Escolha o tema', 'text' => 'Comece pelo visual do painel e da vitrine. A loja ja nasce com identidade.'],
        ['number' => '02', 'title' => 'Configure a loja', 'text' => 'Nome, WhatsApp, Instagram, endereco, cores, categorias e link publico.'],
        ['number' => '03', 'title' => 'Cadastre produtos', 'text' => 'Fotos otimizadas, preco, descricao, categorias, estoque e destaque.'],
        ['number' => '04', 'title' => 'Compartilhe e venda', 'text' => 'Envie o link para clientes, bio do Instagram, grupos e conversas.'],
    ];

    $bonuses = [
        ['title' => 'Respostas que vendem', 'text' => 'Modelos de mensagens para atender mais rapido e fechar pedidos com mais clareza.'],
        ['title' => 'Wizard de primeiros passos', 'text' => 'Um fluxo guiado para o lojista criar a primeira loja sem precisar entender sistema.'],
        ['title' => 'Temas prontos', 'text' => 'Layouts de vitrine e painel para deixar a loja bonita sem contratar designer.'],
    ];

    $comparisons = [
        ['before' => 'Fotos soltas no WhatsApp', 'after' => 'Vitrine organizada por categorias'],
        ['before' => 'Pedido escrito de qualquer jeito', 'after' => 'Carrinho com dados do cliente'],
        ['before' => 'Sem saber o que mais vende', 'after' => 'Ranking de produtos vendidos'],
        ['before' => 'Estoque conferido na memoria', 'after' => 'Controle simples por produto'],
    ];

    $faqs = [
        ['q' => 'Quanto tempo demora para criar minha loja?', 'a' => 'Voce consegue sair do zero com tema, dados da loja, categorias e primeiros produtos em poucos minutos pelo wizard inicial.'],
        ['q' => 'Preciso saber programar?', 'a' => 'Nao. O Shopla foi pensado para quem quer cadastrar produtos, personalizar a loja e compartilhar o link sem mexer com codigo.'],
        ['q' => 'O cliente precisa baixar aplicativo?', 'a' => 'Nao. Ele abre o link da vitrine pelo celular, escolhe os produtos e envia o pedido pelo WhatsApp.'],
        ['q' => 'Da para usar de graca?', 'a' => 'Sim. O plano gratuito permite cadastrar ate 6 produtos para testar a vitrine e comecar a vender.'],
        ['q' => 'Serve para qual tipo de loja?', 'a' => 'Serve para produtos fisicos, artesanato, personalizados, roupas, doces, presentes, papelaria, pequenos catalogos e lojas que vendem pelo WhatsApp.'],
        ['q' => 'Como o pedido chega para mim?', 'a' => 'O cliente monta o carrinho, preenche os dados e o Shopla prepara uma mensagem organizada para enviar no seu WhatsApp.'],
    ];
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Shopla - sua vitrine online para vender pelo WhatsApp</title>
    <meta name="description" content="Crie uma vitrine online bonita, organize pedidos pelo WhatsApp, controle produtos, estoque e vendas com um painel simples.">

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800,900&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            --ink: #111827;
            --muted: #667085;
            --line: #f3cddd;
            --primary: #e1578b;
            --primary-strong: #c53771;
            --primary-soft: #fde8f1;
            --cream: #fff8f5;
            --green: #2fac66;
            --green-soft: #e6f8ef;
            --yellow: #f6c24a;
            --yellow-soft: #fff3c4;
            --violet: #7c55d9;
            --violet-soft: #eee8ff;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            background: var(--cream);
            color: var(--ink);
        }

        .landing-shell {
            min-height: 100vh;
            background:
                linear-gradient(180deg, rgba(255, 248, 245, 0.94), rgba(255, 248, 245, 1) 62%, #fff 100%),
                repeating-linear-gradient(135deg, rgba(225, 87, 139, 0.055) 0 1px, transparent 1px 22px);
            background-size: auto, 180px 180px;
            animation: landing-texture-drift 22s linear infinite;
        }

        .glass-nav {
            background: rgba(255, 255, 255, 0.86);
            backdrop-filter: blur(18px);
        }

        .hero-pattern {
            position: absolute;
            inset: 0;
            pointer-events: none;
            overflow: hidden;
        }

        .hero-pattern::before {
            content: "";
            position: absolute;
            inset: 0;
            background-image:
                linear-gradient(rgba(17, 24, 39, 0.06) 1px, transparent 1px),
                linear-gradient(90deg, rgba(17, 24, 39, 0.06) 1px, transparent 1px);
            background-size: 56px 56px;
            mask-image: linear-gradient(180deg, rgba(0, 0, 0, 0.72), transparent 82%);
            animation: hero-grid-drift 18s linear infinite;
        }

        .mock-board,
        .mock-cart,
        .mock-proof {
            border: 1px solid rgba(225, 87, 139, 0.22);
            background: rgba(255, 255, 255, 0.9);
            box-shadow: 0 28px 70px rgba(79, 18, 45, 0.14);
            backdrop-filter: blur(18px);
        }

        .mock-board {
            position: absolute;
            right: max(24px, calc((100vw - 1180px) / 2));
            top: 138px;
            width: min(590px, 42vw);
            border-radius: 30px;
            padding: 18px;
            transform: rotate(-1.6deg);
            animation: mock-board-float 7.5s ease-in-out infinite;
        }

        .mock-cart {
            position: absolute;
            right: max(260px, calc((100vw - 920px) / 2));
            top: 420px;
            width: 310px;
            border-radius: 28px;
            padding: 18px;
            transform: rotate(2deg);
            animation: mock-cart-float 6.8s ease-in-out infinite;
        }

        .mock-proof {
            position: absolute;
            right: max(24px, calc((100vw - 1220px) / 2));
            top: 605px;
            border-radius: 24px;
            padding: 14px 16px;
            animation: mock-proof-float 6.5s ease-in-out infinite;
        }

        .mock-board::after,
        .mock-cart::after {
            content: "";
            position: absolute;
            inset: 1px;
            border-radius: inherit;
            pointer-events: none;
            background: linear-gradient(120deg, transparent 8%, rgba(255, 255, 255, 0.42) 24%, transparent 42%);
            transform: translateX(-120%);
            animation: mock-sheen 8.5s ease-in-out infinite;
        }

        .mock-board .bg-red-300,
        .mock-board .bg-yellow-300,
        .mock-board .bg-green-300,
        .mock-board-mobile .bg-red-300,
        .mock-board-mobile .bg-yellow-300,
        .mock-board-mobile .bg-green-300 {
            animation: dot-pulse 2.8s ease-in-out infinite;
        }

        .mock-board .bg-yellow-300,
        .mock-board-mobile .bg-yellow-300 {
            animation-delay: .25s;
        }

        .mock-board .bg-green-300,
        .mock-board-mobile .bg-green-300 {
            animation-delay: .5s;
        }

        .product-art {
            position: relative;
            overflow: hidden;
            background:
                linear-gradient(135deg, rgba(225, 87, 139, 0.14), rgba(246, 194, 74, 0.32)),
                linear-gradient(45deg, #fff 0 18%, #f6bfd4 18% 34%, #fff 34% 52%, #c8f3d9 52% 70%, #fff 70% 100%);
            background-size: 130% 130%;
            animation: product-art-pan 12s ease-in-out infinite alternate;
        }

        .product-art::after {
            content: "";
            position: absolute;
            inset: 0;
            background: linear-gradient(110deg, transparent 20%, rgba(255, 255, 255, 0.52) 45%, transparent 70%);
            transform: translateX(-125%);
            animation: product-shine 6.5s ease-in-out infinite;
        }

        .product-art.alt {
            background:
                linear-gradient(135deg, rgba(17, 24, 39, 0.1), rgba(124, 85, 217, 0.18)),
                repeating-linear-gradient(45deg, #f9d2e1 0 18px, #fff 18px 38px, #dff7ea 38px 58px);
        }

        .mock-board-mobile {
            animation: mock-mobile-float 7s ease-in-out infinite;
        }

        .landing-badge {
            animation: landing-badge-float 5.5s ease-in-out infinite;
        }

        .landing-proof-card {
            animation: landing-card-float 7s ease-in-out infinite;
        }

        .landing-stat-card {
            animation: landing-card-float 8s ease-in-out infinite;
        }

        .section-white {
            background: #fff;
        }

        .soft-card {
            border: 1px solid rgba(225, 87, 139, 0.18);
            background: rgba(255, 255, 255, 0.88);
            box-shadow: 0 18px 50px rgba(79, 18, 45, 0.08);
        }

        .dark-card {
            background: #111827;
            color: #fff;
            box-shadow: 0 24px 70px rgba(17, 24, 39, 0.2);
        }

        .cta-button {
            background: var(--primary);
            color: #fff;
            box-shadow: 0 18px 40px rgba(225, 87, 139, 0.28);
        }

        .cta-button:hover {
            background: var(--primary-strong);
        }

        .outline-button {
            border: 1px solid rgba(225, 87, 139, 0.22);
            background: rgba(255, 255, 255, 0.9);
            color: var(--ink);
        }

        .underline-marker {
            background: linear-gradient(transparent 62%, rgba(246, 194, 74, 0.42) 62%);
        }

        .mini-label {
            border: 1px solid rgba(225, 87, 139, 0.18);
            background: rgba(255, 255, 255, 0.88);
        }

        .whatsapp-float {
            animation: whatsapp-float 4.8s ease-in-out infinite;
        }

        .whatsapp-float::before {
            content: "";
            position: absolute;
            inset: -8px;
            border-radius: 999px;
            background: rgba(47, 172, 102, 0.18);
            animation: whatsapp-pulse 2.8s ease-out infinite;
            z-index: -1;
        }

        @keyframes mock-board-float {
            0%, 100% {
                transform: translate3d(0, 0, 0) rotate(-1.6deg);
            }
            35% {
                transform: translate3d(8px, -16px, 0) rotate(-0.9deg);
            }
            50% {
                transform: translate3d(0, -20px, 0) rotate(-0.5deg);
            }
            72% {
                transform: translate3d(-6px, -8px, 0) rotate(-1.3deg);
            }
        }

        @keyframes mock-cart-float {
            0%, 100% {
                transform: translate3d(0, 0, 0) rotate(2deg);
            }
            38% {
                transform: translate3d(-10px, 14px, 0) rotate(1deg);
            }
            50% {
                transform: translate3d(-14px, 18px, 0) rotate(0.8deg);
            }
            76% {
                transform: translate3d(7px, 8px, 0) rotate(2.6deg);
            }
        }

        @keyframes mock-proof-float {
            0%, 100% {
                transform: translate3d(0, 0, 0);
            }
            50% {
                transform: translate3d(0, -13px, 0);
            }
        }

        @keyframes mock-mobile-float {
            0%, 100% {
                transform: translate3d(0, 0, 0) rotate(-1deg);
            }
            50% {
                transform: translate3d(0, -8px, 0) rotate(-0.3deg);
            }
        }

        @keyframes product-art-pan {
            0% {
                background-position: 0% 50%;
            }
            100% {
                background-position: 100% 50%;
            }
        }

        @keyframes product-shine {
            0%, 42% {
                transform: translateX(-125%);
            }
            62%, 100% {
                transform: translateX(125%);
            }
        }

        @keyframes mock-sheen {
            0%, 50% {
                transform: translateX(-120%);
                opacity: 0;
            }
            62% {
                opacity: 1;
            }
            82%, 100% {
                transform: translateX(120%);
                opacity: 0;
            }
        }

        @keyframes dot-pulse {
            0%, 100% {
                transform: scale(1);
                opacity: .78;
            }
            50% {
                transform: scale(1.18);
                opacity: 1;
            }
        }

        @keyframes whatsapp-float {
            0%, 100% {
                transform: translate3d(0, 0, 0);
            }
            50% {
                transform: translate3d(0, -8px, 0);
            }
        }

        @keyframes whatsapp-pulse {
            0% {
                transform: scale(.86);
                opacity: .7;
            }
            100% {
                transform: scale(1.45);
                opacity: 0;
            }
        }

        @keyframes landing-texture-drift {
            0% {
                background-position: 0 0, 0 0;
            }
            100% {
                background-position: 0 0, 180px 180px;
            }
        }

        @keyframes hero-grid-drift {
            0% {
                background-position: 0 0, 0 0;
            }
            100% {
                background-position: 56px 56px, 56px 56px;
            }
        }

        @keyframes landing-badge-float {
            0%, 100% {
                transform: translate3d(0, 0, 0);
            }
            50% {
                transform: translate3d(0, -6px, 0);
            }
        }

        @keyframes landing-card-float {
            0%, 100% {
                transform: translate3d(0, 0, 0);
            }
            50% {
                transform: translate3d(0, -7px, 0);
            }
        }

        @media (prefers-reduced-motion: reduce) {
            .landing-shell,
            .hero-pattern::before,
            .mock-board,
            .mock-cart,
            .mock-proof,
            .mock-board-mobile,
            .product-art,
            .product-art::after,
            .mock-board::after,
            .mock-cart::after,
            .mock-board .bg-red-300,
            .mock-board .bg-yellow-300,
            .mock-board .bg-green-300,
            .mock-board-mobile .bg-red-300,
            .mock-board-mobile .bg-yellow-300,
            .mock-board-mobile .bg-green-300,
            .whatsapp-float,
            .whatsapp-float::before,
            .landing-badge,
            .landing-proof-card,
            .landing-stat-card {
                animation: none;
            }
        }

        @media (max-width: 1023px) {
            .mock-board {
                right: -190px;
                top: 430px;
                width: 560px;
                opacity: 0.42;
            }

            .mock-cart,
            .mock-proof {
                display: none;
            }
        }

        @media (max-width: 640px) {
            .mock-board {
                display: none;
            }
        }
    </style>
</head>
<body class="font-sans antialiased">
    <div class="landing-shell">
        <header class="fixed top-0 left-0 right-0 z-40 border-b border-pink-100 glass-nav">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between">
                <a href="/" class="flex items-center gap-3" aria-label="Shopla">
                    <span class="h-11 w-11 rounded-2xl flex items-center justify-center text-white font-black shadow-lg" style="background: var(--primary);">
                        S
                    </span>
                    <span class="text-2xl font-black">Shopla</span>
                </a>

                <nav class="hidden lg:flex items-center gap-7 text-sm font-bold text-gray-600">
                    <a href="{{ route('about') }}" class="hover:text-pink-600">Sobre</a>
                    <a href="#problema" class="hover:text-pink-600">Problema</a>
                    <a href="#metodo" class="hover:text-pink-600">Metodo</a>
                    <a href="#recursos" class="hover:text-pink-600">Beneficios</a>
                    <a href="#planos" class="hover:text-pink-600">Planos</a>
                    <a href="{{ route('contact') }}" class="hover:text-pink-600">Contato</a>
                    <a href="#faq" class="hover:text-pink-600">Duvidas</a>
                </nav>

                <div class="flex items-center gap-3">
                    @auth
                        <a href="{{ route('dashboard') }}" class="rounded-2xl px-5 py-3 font-black text-white cta-button">
                            Abrir painel
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="inline-flex rounded-2xl px-4 py-2.5 sm:px-5 sm:py-3 font-bold outline-button">
                            Entrar
                        </a>
                        <a href="{{ route('register') }}" class="rounded-2xl px-4 py-2.5 sm:px-5 sm:py-3 font-black text-white cta-button">
                            <span class="hidden sm:inline">Comecar agora</span>
                            <span class="sm:hidden">Cadastrar</span>
                        </a>
                    @endauth
                </div>
            </div>
        </header>

        <main>
            <section class="relative min-h-[850px] pt-32 pb-20 overflow-hidden">
                <div class="hero-pattern" aria-hidden="true"></div>

                <div class="mock-board hidden md:block" aria-hidden="true">
                    <div class="flex items-center justify-between mb-5">
                        <div class="flex items-center gap-2">
                            <span class="h-3 w-3 rounded-full bg-red-300"></span>
                            <span class="h-3 w-3 rounded-full bg-yellow-300"></span>
                            <span class="h-3 w-3 rounded-full bg-green-300"></span>
                        </div>
                        <span class="text-xs font-black text-pink-600">vitrine publicada</span>
                    </div>

                    <div class="grid grid-cols-3 gap-4">
                        <div class="col-span-2 rounded-3xl p-5 bg-pink-50 border border-pink-100">
                            <div class="h-48 rounded-2xl product-art"></div>
                            <div class="mt-5 h-4 w-48 rounded-full bg-gray-900"></div>
                            <div class="mt-3 h-3 w-32 rounded-full bg-pink-300"></div>
                            <div class="mt-6 flex items-center justify-between">
                                <div>
                                    <div class="h-5 w-24 rounded-full bg-pink-500"></div>
                                    <div class="mt-2 h-3 w-20 rounded-full bg-yellow-300"></div>
                                </div>
                                <div class="h-10 w-28 rounded-2xl bg-pink-500"></div>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <div class="rounded-3xl p-4 bg-white border border-pink-100">
                                <div class="h-28 rounded-2xl product-art alt"></div>
                                <div class="mt-4 h-3 w-24 rounded-full bg-gray-900"></div>
                                <div class="mt-3 h-3 w-16 rounded-full bg-pink-300"></div>
                            </div>
                            <div class="rounded-3xl p-4 bg-white border border-pink-100">
                                <div class="flex items-center gap-3">
                                    <div class="h-12 w-12 rounded-2xl bg-pink-100 flex items-center justify-center text-pink-600">
                                        <x-dashboard-icon name="orders" class="w-6 h-6" />
                                    </div>
                                    <div>
                                        <div class="h-3 w-24 rounded-full bg-gray-900"></div>
                                        <div class="mt-2 h-3 w-16 rounded-full bg-pink-300"></div>
                                    </div>
                                </div>
                                <div class="mt-5 h-12 rounded-2xl bg-gray-900"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mock-cart hidden lg:block" aria-hidden="true">
                    <div class="flex items-center justify-between border-b border-pink-100 pb-4">
                        <div class="flex items-center gap-3">
                            <span class="h-10 w-10 rounded-2xl flex items-center justify-center bg-pink-100 text-pink-600">
                                <x-dashboard-icon name="orders" class="w-5 h-5" />
                            </span>
                            <strong>Pedido pronto</strong>
                        </div>
                        <span class="rounded-full px-3 py-1 text-sm font-black text-white" style="background: var(--primary);">2 itens</span>
                    </div>
                    <div class="py-5 space-y-4">
                        <div class="flex items-center gap-4">
                            <div class="h-16 w-16 rounded-2xl product-art"></div>
                            <div class="flex-1">
                                <div class="h-4 w-32 rounded-full bg-gray-900"></div>
                                <div class="mt-3 h-3 w-24 rounded-full bg-pink-200"></div>
                            </div>
                        </div>
                        <div class="rounded-2xl border border-pink-100 p-4 bg-pink-50">
                            <div class="h-3 w-32 rounded-full bg-pink-300"></div>
                            <div class="mt-3 h-12 rounded-2xl bg-white border border-pink-100"></div>
                        </div>
                    </div>
                </div>

                <div class="mock-proof hidden lg:flex items-center gap-3" aria-hidden="true">
                    <span class="h-12 w-12 rounded-2xl flex items-center justify-center bg-green-100 text-green-700">
                        <x-dashboard-icon name="trend" class="w-6 h-6" />
                    </span>
                    <div>
                        <p class="font-black">Cliente sem baixar app</p>
                        <p class="text-sm text-gray-500">abre o link e faz o pedido</p>
                    </div>
                </div>

                <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="max-w-[680px] pt-10 lg:pt-20">
                        <p class="landing-badge inline-flex rounded-full px-4 py-2 mini-label text-sm font-black text-pink-700">
                            Para vender pelo WhatsApp com menos bagunca e mais pedidos
                        </p>

                        <h1 class="mt-7 text-5xl sm:text-6xl lg:text-7xl font-black leading-[1.02]">
                            Crie sua loja online em minutos e transforme conversas em <span class="underline-marker">pedidos prontos.</span>
                        </h1>

                        <p class="mt-7 text-xl leading-8 max-w-2xl text-gray-600">
                            Monte uma vitrine com produtos, fotos e categorias. O cliente escolhe, preenche os dados e envia o pedido organizado direto para o seu WhatsApp.
                        </p>

                        <div class="mt-9 flex flex-col sm:flex-row gap-4">
                            @auth
                                <a href="{{ route('dashboard') }}" class="inline-flex items-center justify-center gap-2 rounded-2xl px-8 py-4 font-black text-white cta-button">
                                    Abrir minha loja
                                    <x-dashboard-icon name="external" class="w-5 h-5" />
                                </a>
                            @else
                                <a href="{{ route('register') }}" class="inline-flex items-center justify-center gap-2 rounded-2xl px-8 py-4 font-black text-white cta-button">
                                    Comecar em 2 minutos
                                    <x-dashboard-icon name="external" class="w-5 h-5" />
                                </a>
                            @endauth
                            <a href="#metodo" class="inline-flex items-center justify-center gap-2 rounded-2xl px-8 py-4 font-black outline-button">
                                Ver como funciona
                                <x-dashboard-icon name="trend" class="w-5 h-5" />
                            </a>
                        </div>

                        <div class="mt-7 grid sm:grid-cols-3 gap-3 max-w-3xl">
                            @foreach($proofPoints as $proof)
                                <article class="landing-proof-card soft-card rounded-3xl p-4 flex gap-3" style="animation-delay: {{ $loop->index * 0.45 }}s">
                                    <span class="h-10 w-10 shrink-0 rounded-2xl flex items-center justify-center bg-pink-50 text-pink-600">
                                        <x-dashboard-icon :name="$proof['icon']" class="w-5 h-5" />
                                    </span>
                                    <div>
                                        <h3 class="text-sm font-black">{{ $proof['title'] }}</h3>
                                        <p class="mt-1 text-xs leading-5 text-gray-500">{{ $proof['text'] }}</p>
                                    </div>
                                </article>
                            @endforeach
                        </div>

                        <div class="md:hidden mt-8 relative">
                            <div class="mock-board-mobile rounded-[2rem] p-3 soft-card rotate-[-1deg]">
                                <div class="flex items-center justify-between mb-3 px-1">
                                    <div class="flex items-center gap-1.5">
                                        <span class="h-2.5 w-2.5 rounded-full bg-red-300"></span>
                                        <span class="h-2.5 w-2.5 rounded-full bg-yellow-300"></span>
                                        <span class="h-2.5 w-2.5 rounded-full bg-green-300"></span>
                                    </div>
                                    <span class="text-[10px] font-black text-pink-600">vitrine publicada</span>
                                </div>

                                <div class="grid grid-cols-[1.3fr_.7fr] gap-3">
                                    <div class="rounded-3xl p-3 bg-pink-50 border border-pink-100">
                                        <div class="h-32 rounded-2xl product-art"></div>
                                        <div class="mt-4 h-3 w-28 rounded-full bg-gray-900"></div>
                                        <div class="mt-2 h-2.5 w-20 rounded-full bg-pink-300"></div>
                                        <div class="mt-4 h-9 rounded-2xl bg-pink-500"></div>
                                    </div>

                                    <div class="space-y-3">
                                        <div class="rounded-3xl p-3 bg-white border border-pink-100">
                                            <div class="h-16 rounded-2xl product-art alt"></div>
                                            <div class="mt-3 h-2.5 w-16 rounded-full bg-gray-900"></div>
                                            <div class="mt-2 h-2.5 w-12 rounded-full bg-pink-300"></div>
                                        </div>

                                        <div class="rounded-3xl p-3 bg-white border border-pink-100">
                                            <div class="h-8 rounded-2xl bg-gray-900"></div>
                                            <div class="mt-3 h-2.5 w-14 rounded-full bg-pink-300"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="landing-badge absolute -bottom-5 right-2 rounded-3xl border border-pink-100 bg-white/95 px-4 py-3 shadow-xl flex items-center gap-3">
                                <span class="h-10 w-10 rounded-2xl flex items-center justify-center bg-pink-100 text-pink-600">
                                    <x-dashboard-icon name="orders" class="w-5 h-5" />
                                </span>
                                <div>
                                    <p class="text-sm font-black">Pedido pronto</p>
                                    <p class="text-xs text-gray-500">2 itens no carrinho</p>
                                </div>
                            </div>
                        </div>

                        <div class="mt-10 grid sm:grid-cols-3 gap-3 max-w-2xl">
                            <div class="landing-stat-card soft-card rounded-3xl p-4">
                                <p class="text-3xl font-black">6</p>
                                <p class="text-sm text-gray-500">produtos para testar gratis</p>
                            </div>
                            <div class="landing-stat-card soft-card rounded-3xl p-4" style="animation-delay: .35s">
                                <p class="text-3xl font-black">3</p>
                                <p class="text-sm text-gray-500">fotos para valorizar cada produto</p>
                            </div>
                            <div class="landing-stat-card soft-card rounded-3xl p-4" style="animation-delay: .7s">
                                <p class="text-3xl font-black">0</p>
                                <p class="text-sm text-gray-500">codigo ou instalacao para comecar</p>
                            </div>
                        </div>

                        <p class="mt-6 text-sm font-semibold text-gray-500">
                            Comece gratis, publique hoje e compartilhe sua vitrine com quem ja compra pelo WhatsApp.
                        </p>
                    </div>
                </div>
            </section>

            <section class="py-10" style="background: #fff8f5;">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="soft-card rounded-[2rem] p-5 md:p-7">
                        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-5">
                            <div class="max-w-xl">
                                <p class="text-xs font-black uppercase tracking-[0.24em] text-pink-600">Para quem e</p>
                                <h2 class="mt-2 text-2xl md:text-3xl font-black">Feito para negocios que ja vendem pelo WhatsApp.</h2>
                            </div>
                            <p class="max-w-xl text-sm md:text-base leading-7 text-gray-600">
                                Se hoje o cliente pede foto, preco, opcoes e endereco pela conversa, a Shopla transforma esse atendimento em um fluxo mais facil de comprar.
                            </p>
                        </div>

                        <div class="mt-6 grid md:grid-cols-2 lg:grid-cols-4 gap-3">
                            @foreach($useCases as $case)
                                <article class="rounded-2xl border border-pink-100 bg-pink-50/60 p-4">
                                    <div class="flex items-center gap-3">
                                        <span class="h-10 w-10 shrink-0 rounded-2xl flex items-center justify-center bg-white text-pink-600">
                                            <x-dashboard-icon :name="$case['icon']" class="w-5 h-5" />
                                        </span>
                                        <h3 class="font-black leading-tight">{{ $case['title'] }}</h3>
                                    </div>
                                    <p class="mt-3 text-sm leading-6 text-gray-600">{{ $case['text'] }}</p>
                                </article>
                            @endforeach
                        </div>
                    </div>
                </div>
            </section>

            <section id="problema" class="section-white py-20">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="max-w-3xl">
                        <p class="text-sm font-black uppercase text-pink-600">O problema</p>
                        <h2 class="mt-3 text-4xl md:text-5xl font-black">Vender pelo WhatsApp funciona. O problema e quando tudo depende da sua memoria.</h2>
                        <p class="mt-5 text-lg text-gray-600">
                            A maioria das vendas pequenas nao se perde por falta de produto. Se perde por falta de organizacao, velocidade e clareza na hora do cliente decidir.
                        </p>
                    </div>

                    <div class="mt-12 grid md:grid-cols-3 gap-5">
                        @foreach($painPoints as $point)
                            <article class="soft-card rounded-3xl p-7">
                                <span class="h-12 w-12 rounded-2xl flex items-center justify-center bg-red-50 text-red-600">
                                    <x-dashboard-icon name="alert" class="w-6 h-6" />
                                </span>
                                <h3 class="mt-6 text-xl font-black">{{ $point['title'] }}</h3>
                                <p class="mt-3 text-gray-600">{{ $point['text'] }}</p>
                            </article>
                        @endforeach
                    </div>
                </div>
            </section>

            <section id="metodo" class="py-20" style="background: #fff8f5;">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="grid lg:grid-cols-[0.95fr_1.05fr] gap-10 items-center">
                        <div>
                            <p class="text-sm font-black uppercase text-pink-600">O metodo Shopla</p>
                            <h2 class="mt-3 text-4xl md:text-5xl font-black">Do primeiro produto ao pedido no WhatsApp.</h2>
                            <p class="mt-5 text-lg text-gray-600">
                                Em vez de criar uma loja pesada, o Shopla faz o caminho direto: publica seus produtos, guia a escolha do cliente e entrega a conversa ja organizada.
                            </p>
                            <div class="mt-8 flex flex-col sm:flex-row gap-3">
                                <a href="{{ auth()->check() ? route('dashboard') : route('register') }}" class="inline-flex justify-center rounded-2xl px-7 py-4 font-black text-white cta-button">
                                    Criar minha loja agora
                                </a>
                                <a href="#planos" class="inline-flex justify-center rounded-2xl px-7 py-4 font-black outline-button">
                                    Ver planos
                                </a>
                            </div>
                        </div>

                        <div class="grid sm:grid-cols-2 gap-5">
                            @foreach($steps as $step)
                                <article class="rounded-3xl p-6 bg-white border border-pink-100 shadow-sm">
                                    <span class="inline-flex h-11 w-11 rounded-2xl items-center justify-center text-white font-black" style="background: var(--primary);">{{ $step['number'] }}</span>
                                    <h3 class="mt-5 text-xl font-black">{{ $step['title'] }}</h3>
                                    <p class="mt-3 text-gray-600">{{ $step['text'] }}</p>
                                </article>
                            @endforeach
                        </div>
                    </div>
                </div>
            </section>

            <section id="recursos" class="section-white py-20">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-6">
                        <div class="max-w-3xl">
                            <p class="text-sm font-black uppercase text-pink-600">Beneficios</p>
                            <h2 class="mt-3 text-4xl md:text-5xl font-black">O que melhora quando sua loja deixa de depender so da conversa.</h2>
                        </div>
                        <p class="max-w-xl text-lg text-gray-600">
                            Cada recurso precisa ajudar em uma coisa concreta: vender com menos repeticao, menos duvida e mais clareza para o cliente.
                        </p>
                    </div>

                    <div class="mt-12 grid md:grid-cols-2 lg:grid-cols-3 gap-5">
                        @foreach($features as $feature)
                            <article class="soft-card rounded-3xl p-7">
                                <span class="h-12 w-12 rounded-2xl flex items-center justify-center bg-pink-50 text-pink-600">
                                    <x-dashboard-icon :name="$feature['icon']" class="w-6 h-6" />
                                </span>
                                <h3 class="mt-6 text-xl font-black">{{ $feature['title'] }}</h3>
                                <p class="mt-3 text-gray-600">{{ $feature['text'] }}</p>
                            </article>
                        @endforeach
                    </div>
                </div>
            </section>

            <section class="py-20" style="background: #fff8f5;">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="dark-card rounded-[2rem] overflow-hidden">
                        <div class="grid lg:grid-cols-2">
                            <div class="p-8 md:p-12">
                                <p class="text-sm font-black uppercase text-pink-300">Antes e depois</p>
                                <h2 class="mt-3 text-4xl md:text-5xl font-black">O cliente sente que esta comprando em uma loja, nao improvisando em uma conversa.</h2>
                                <p class="mt-5 text-lg text-gray-300">
                                    Essa percepcao muda tudo: fica mais facil escolher, comparar, enviar dados e fechar o pedido.
                                </p>
                            </div>

                            <div class="bg-white text-gray-900 p-8 md:p-12">
                                <div class="space-y-4">
                                    @foreach($comparisons as $item)
                                        <div class="grid sm:grid-cols-2 gap-3">
                                            <div class="rounded-2xl p-4 bg-red-50 border border-red-100 text-red-700 font-semibold">
                                                {{ $item['before'] }}
                                            </div>
                                            <div class="rounded-2xl p-4 bg-green-50 border border-green-100 text-green-700 font-semibold">
                                                {{ $item['after'] }}
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="section-white py-20">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="text-center max-w-3xl mx-auto">
                        <p class="text-sm font-black uppercase text-pink-600">Bonus de ativacao</p>
                        <h2 class="mt-3 text-4xl md:text-5xl font-black">Nao e so criar a loja. E colocar a loja para vender.</h2>
                        <p class="mt-5 text-lg text-gray-600">
                            Recursos extras ajudam o lojista a sair do cadastro e chegar na primeira divulgacao com mais seguranca.
                        </p>
                    </div>

                    <div class="mt-12 grid md:grid-cols-3 gap-5">
                        @foreach($bonuses as $bonus)
                            <article class="rounded-3xl p-7 border border-pink-100 bg-pink-50">
                                <span class="h-12 w-12 rounded-2xl flex items-center justify-center bg-white text-pink-600">
                                    <x-dashboard-icon name="gift" class="w-6 h-6" />
                                </span>
                                <h3 class="mt-6 text-xl font-black">{{ $bonus['title'] }}</h3>
                                <p class="mt-3 text-gray-600">{{ $bonus['text'] }}</p>
                            </article>
                        @endforeach
                    </div>
                </div>
            </section>

            <section id="planos" class="py-20" style="background: #fff8f5;">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-5">
                        <div>
                            <p class="text-sm font-black uppercase text-pink-600">Planos</p>
                            <h2 class="mt-3 text-4xl md:text-5xl font-black">Comece gratis e cresca quando precisar.</h2>
                        </div>
                        <p class="max-w-xl text-gray-600">
                            Teste a vitrine sem pagar. Quando o catalogo crescer, escolha o plano que combina com o tamanho da sua loja.
                        </p>
                    </div>

                    <div class="mt-12 grid md:grid-cols-2 xl:grid-cols-5 gap-5">
                        @foreach(['free', 'plus', 'pro', 'premium', 'enterprise'] as $key)
                            @php($plan = $plans[$key])
                            @php($isPaid = isset($plan['price']))
                            @php($isContact = $key === 'enterprise')
                            <article class="rounded-3xl p-7 bg-white border shadow-sm flex flex-col {{ $key === 'pro' ? 'border-pink-300 ring-4 ring-pink-100' : 'border-pink-100' }}">
                                @if($key === 'pro')
                                    <span class="inline-flex rounded-full px-3 py-1 text-xs font-black text-white w-fit" style="background: var(--primary);">Mais escolhido</span>
                                @endif

                                <h3 class="mt-5 text-2xl font-black">{{ $plan['name'] }}</h3>
                                <p class="mt-3 text-gray-600 min-h-[72px]">{{ $plan['description'] }}</p>

                                <div class="mt-7">
                                    @if($isPaid)
                                        <strong class="text-4xl font-black">R$ {{ number_format($plan['price'], 2, ',', '.') }}</strong>
                                        <span class="text-gray-500">{{ $plan['period_label'] ?? 'por mes' }}</span>
                                        @if(isset($plan['annual_price']))
                                            <p class="mt-2 text-xs font-bold text-gray-500">
                                                R$ {{ number_format($plan['annual_price'], 2, ',', '.') }} {{ $plan['annual_label'] ?? 'por ano' }}
                                                @if(($plan['annual_discount_percent'] ?? 0) > 0)
                                                    <span class="text-pink-600">com {{ number_format($plan['annual_discount_percent'], 0) }}% off</span>
                                                @endif
                                            </p>
                                        @endif
                                    @elseif($isContact)
                                        <strong class="text-4xl font-black">200+</strong>
                                        <span class="text-gray-500">produtos</span>
                                    @else
                                        <strong class="text-4xl font-black">R$ 0</strong>
                                        <span class="text-gray-500">para comecar</span>
                                    @endif
                                </div>

                                <p class="mt-5 rounded-2xl bg-pink-50 px-4 py-3 font-bold text-pink-700">
                                    {{ $isContact ? 'Acima de 200 produtos' : 'Ate ' . $plan['limit'] . ' produtos' }}
                                </p>

                                <ul class="mt-6 space-y-3 text-sm text-gray-600">
                                    <li class="flex gap-2"><span class="text-green-600 font-black">✓</span> Vitrine publica</li>
                                    <li class="flex gap-2"><span class="text-green-600 font-black">✓</span> Carrinho para WhatsApp</li>
                                    <li class="flex gap-2"><span class="text-green-600 font-black">✓</span> Painel de produtos e pedidos</li>
                                    <li class="flex gap-2"><span class="text-green-600 font-black">✓</span> {{ $isContact || $isPaid ? 'Link personalizado da loja' : 'Link automatico da loja' }}</li>
                                </ul>

                                <a href="{{ auth()->check() ? route('plans.index') : route('register') }}" class="mt-7 flex justify-center rounded-2xl px-5 py-4 font-black {{ $key === 'pro' ? 'text-white cta-button' : 'outline-button' }}">
                                    {{ $key === 'free' ? 'Comecar gratis' : ($isContact ? 'Entrar em contato' : 'Escolher plano') }}
                                </a>
                            </article>
                        @endforeach
                    </div>
                </div>
            </section>

            <section id="faq" class="section-white py-20">
                <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="text-center">
                        <p class="text-sm font-black uppercase text-pink-600">Duvidas comuns</p>
                        <h2 class="mt-3 text-4xl md:text-5xl font-black">Perguntas que travam a decisao antes de comecar.</h2>
                    </div>

                    <div class="mt-12 space-y-4">
                        @foreach($faqs as $faq)
                            <details class="group rounded-3xl border border-pink-100 bg-white p-6 shadow-sm">
                                <summary class="cursor-pointer list-none flex items-center justify-between gap-4">
                                    <span class="text-lg font-black">{{ $faq['q'] }}</span>
                                    <span class="h-9 w-9 rounded-2xl bg-pink-50 text-pink-600 flex items-center justify-center group-open:rotate-45 transition">
                                        <x-dashboard-icon name="plus" class="w-5 h-5" />
                                    </span>
                                </summary>
                                <p class="mt-4 text-gray-600 leading-7">{{ $faq['a'] }}</p>
                            </details>
                        @endforeach
                    </div>
                </div>
            </section>

            <section class="py-20" style="background: #111827;">
                <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 text-center text-white">
                    <p class="text-sm font-black uppercase text-pink-300">Proximo passo</p>
                    <h2 class="mt-3 text-4xl md:text-6xl font-black">Pronto para vender com menos bagunca hoje?</h2>
                    <p class="mt-5 text-lg text-gray-300 max-w-2xl mx-auto">
                        Comece pelo plano gratuito, publique sua primeira vitrine e mande para seus clientes um link mais facil de comprar.
                    </p>
                    <div class="mt-9 flex flex-col sm:flex-row justify-center gap-4">
                        <a href="{{ auth()->check() ? route('dashboard') : route('register') }}" class="inline-flex items-center justify-center gap-2 rounded-2xl px-9 py-4 font-black text-white cta-button">
                            Comecar agora
                            <x-dashboard-icon name="external" class="w-5 h-5" />
                        </a>
                        <a href="#planos" class="inline-flex items-center justify-center rounded-2xl px-9 py-4 font-black border border-white/20 text-white">
                            Comparar planos
                        </a>
                    </div>
                </div>
            </section>
        </main>

        <footer class="border-t border-pink-100 bg-white py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row items-center justify-between gap-4 text-sm text-gray-500">
                <p class="font-bold text-gray-900">Shopla</p>
                <div class="flex flex-wrap items-center justify-center gap-4">
                    <a href="{{ route('about') }}" class="font-bold text-gray-700 hover:text-pink-600">Sobre</a>
                    <a href="{{ route('contact') }}" class="font-bold text-gray-700 hover:text-pink-600">Contato</a>
                    <p>Vitrine, pedidos e gestao simples para pequenos negocios.</p>
                </div>
            </div>
        </footer>

        @if($supportPhone)
            <div
                x-data="{ open: false, message: @js($supportMessage) }"
                class="fixed bottom-5 right-5 z-50 flex flex-col items-end gap-3"
            >
                <div
                    x-cloak
                    x-show="open"
                    x-transition
                    @click.outside="open = false"
                    class="w-[min(92vw,340px)] rounded-[1.75rem] border border-green-100 bg-white p-4 shadow-2xl shadow-green-900/10"
                >
                    <div class="flex items-center gap-3">
                        <span class="flex h-11 w-11 items-center justify-center rounded-2xl bg-green-100 text-green-700">
                            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                <path d="M20.52 3.48A11.86 11.86 0 0 0 12.08 0C5.52 0 .18 5.34.18 11.9c0 2.1.55 4.15 1.6 5.96L0 24l6.3-1.65a11.88 11.88 0 0 0 5.78 1.47h.01c6.56 0 11.9-5.34 11.9-11.9 0-3.18-1.24-6.17-3.47-8.44ZM12.09 21.8h-.01a9.86 9.86 0 0 1-5.02-1.37l-.36-.21-3.74.98 1-3.65-.24-.38a9.83 9.83 0 0 1-1.5-5.27c0-5.45 4.43-9.88 9.88-9.88 2.64 0 5.12 1.03 6.99 2.9a9.82 9.82 0 0 1 2.89 6.99c0 5.45-4.43 9.89-9.89 9.89Zm5.42-7.4c-.3-.15-1.76-.87-2.03-.97-.27-.1-.47-.15-.67.15-.2.3-.77.97-.94 1.17-.17.2-.35.22-.65.07-.3-.15-1.26-.46-2.4-1.47-.89-.79-1.49-1.77-1.66-2.07-.17-.3-.02-.46.13-.61.13-.13.3-.35.45-.52.15-.17.2-.3.3-.5.1-.2.05-.37-.02-.52-.08-.15-.67-1.61-.92-2.2-.24-.58-.49-.5-.67-.51h-.57c-.2 0-.52.07-.79.37-.27.3-1.04 1.02-1.04 2.48s1.07 2.88 1.22 3.08c.15.2 2.1 3.2 5.08 4.49.71.31 1.26.49 1.69.63.71.23 1.36.2 1.88.12.57-.09 1.76-.72 2-1.41.25-.69.25-1.29.17-1.41-.07-.13-.27-.2-.57-.35Z"/>
                            </svg>
                        </span>
                        <div>
                            <p class="font-black">Fale com a Shopla</p>
                            <p class="text-xs text-gray-500">Respondemos pelo WhatsApp</p>
                        </div>
                    </div>

                    <textarea
                        x-model="message"
                        rows="4"
                        class="mt-4 w-full rounded-2xl border border-green-100 bg-green-50/50 px-4 py-3 text-sm outline-none focus:border-green-300 focus:ring-4 focus:ring-green-100"
                    ></textarea>

                    <a
                        x-bind:href="'https://wa.me/{{ $supportPhone }}?text=' + encodeURIComponent(message)"
                        target="_blank"
                        class="mt-3 flex w-full items-center justify-center rounded-2xl bg-green-500 px-5 py-3 font-black text-white"
                    >
                        Enviar mensagem
                    </a>
                </div>

                <button
                    type="button"
                    @click="open = !open"
                    class="whatsapp-float relative flex h-16 w-16 items-center justify-center rounded-full bg-green-500 text-white shadow-2xl shadow-green-900/20"
                    aria-label="Abrir chat no WhatsApp"
                >
                    <svg class="h-8 w-8" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                        <path d="M20.52 3.48A11.86 11.86 0 0 0 12.08 0C5.52 0 .18 5.34.18 11.9c0 2.1.55 4.15 1.6 5.96L0 24l6.3-1.65a11.88 11.88 0 0 0 5.78 1.47h.01c6.56 0 11.9-5.34 11.9-11.9 0-3.18-1.24-6.17-3.47-8.44ZM12.09 21.8h-.01a9.86 9.86 0 0 1-5.02-1.37l-.36-.21-3.74.98 1-3.65-.24-.38a9.83 9.83 0 0 1-1.5-5.27c0-5.45 4.43-9.88 9.88-9.88 2.64 0 5.12 1.03 6.99 2.9a9.82 9.82 0 0 1 2.89 6.99c0 5.45-4.43 9.89-9.89 9.89Zm5.42-7.4c-.3-.15-1.76-.87-2.03-.97-.27-.1-.47-.15-.67.15-.2.3-.77.97-.94 1.17-.17.2-.35.22-.65.07-.3-.15-1.26-.46-2.4-1.47-.89-.79-1.49-1.77-1.66-2.07-.17-.3-.02-.46.13-.61.13-.13.3-.35.45-.52.15-.17.2-.3.3-.5.1-.2.05-.37-.02-.52-.08-.15-.67-1.61-.92-2.2-.24-.58-.49-.5-.67-.51h-.57c-.2 0-.52.07-.79.37-.27.3-1.04 1.02-1.04 2.48s1.07 2.88 1.22 3.08c.15.2 2.1 3.2 5.08 4.49.71.31 1.26.49 1.69.63.71.23 1.36.2 1.88.12.57-.09 1.76-.72 2-1.41.25-.69.25-1.29.17-1.41-.07-.13-.27-.2-.57-.35Z"/>
                    </svg>
                </button>
            </div>
        @endif
    </div>
</body>
</html>
