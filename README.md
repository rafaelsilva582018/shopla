# Shopla

Shopla é uma aplicação Laravel para pequenos lojistas criarem uma vitrine online, cadastrarem produtos e receberem pedidos pelo WhatsApp.

## Funcionalidades

- Cadastro, login e perfil do lojista.
- Wizard de primeiros passos para escolher tema, configurar a loja, criar categorias e publicar o primeiro produto.
- Planos com limite de produtos, começando pelo plano gratuito com 6 produtos.
- Personalização da vitrine e do painel com temas prontos ou cores manuais.
- Cadastro de categorias e produtos com até duas imagens por produto.
- Otimização automática de imagens antes de salvar no servidor.
- Controle de estoque com produto esgotado, sob encomenda, pronta entrega e quantidade opcional.
- Vitrine pública com busca, filtros, visão em cards ou lista, modal de produto, compartilhamento e carrinho.
- Carrinho em modal com dados do cliente, endereço, observações e envio do pedido para WhatsApp.
- Painel com resumo da loja, clima, relógio, métricas, pedidos recentes e ranking dos mais vendidos.
- Áreas de pedidos, financeiro, estoque, categorias, produtos, ranking, minha loja e perfil.

## Stack

- PHP 8.3
- Laravel 13
- Livewire 4
- Laravel Breeze
- Vite
- Tailwind CSS
- SQLite no ambiente local

## Como Rodar

Instale as dependências:

```bash
composer install
npm install
```

Configure o ambiente:

```bash
copy .env.example .env
php artisan key:generate
php artisan migrate
php artisan storage:link
```

Inicie o projeto:

```bash
composer run dev
```

## Testes

```bash
php artisan test
```

## Rotas Principais

- `/onboarding`: primeiros passos da loja.
- `/dashboard`: painel do lojista.
- `/minha-loja/configuracoes`: configurações da loja.
- `/minha-loja/produtos`: produtos.
- `/minha-loja/estoque`: estoque.
- `/minha-loja/categorias`: categorias.
- `/minha-loja/pedidos`: pedidos.
- `/minha-loja/financeiro`: financeiro.
- `/minha-loja/ranking`: ranking dos mais vendidos.
- `/profile`: perfil do usuário.
- `/{slug}`: vitrine pública.

## Observações

O fluxo atual salva o pedido no sistema e redireciona o cliente para o WhatsApp da loja com a mensagem preenchida. A integração de pagamento ainda pode ser adicionada depois, sem travar o MVP atual.
