# Deploy do Shopla no Ubuntu com Docker e Cloudflare Tunnel

Este guia considera o projeto em `/opt/sites/shopla` e o dominio `shopla.com.br`.

## 1. Preparar o servidor

```bash
sudo apt update
sudo apt install -y ca-certificates curl git

sudo install -m 0755 -d /etc/apt/keyrings
sudo curl -fsSL https://download.docker.com/linux/ubuntu/gpg -o /etc/apt/keyrings/docker.asc
sudo chmod a+r /etc/apt/keyrings/docker.asc

echo \
  "deb [arch=$(dpkg --print-architecture) signed-by=/etc/apt/keyrings/docker.asc] https://download.docker.com/linux/ubuntu \
  $(. /etc/os-release && echo "$VERSION_CODENAME") stable" | \
  sudo tee /etc/apt/sources.list.d/docker.list > /dev/null

sudo apt update
sudo apt install -y docker-ce docker-ce-cli containerd.io docker-buildx-plugin docker-compose-plugin
```

## 2. Colocar o projeto na pasta correta

Se for clonar por Git:

```bash
sudo mkdir -p /opt/sites
sudo chown -R $USER:$USER /opt/sites
cd /opt/sites
git clone SEU_REPOSITORIO_AQUI shopla
cd /opt/sites/shopla
```

Se for enviar por SFTP/WinSCP, envie a pasta do projeto para:

```text
/opt/sites/shopla
```

## 3. Criar o arquivo de ambiente

```bash
cd /opt/sites/shopla
cp .env.production.example .env.production
nano .env.production
```

Troque principalmente:

```env
APP_URL=https://shopla.com.br
APP_PORT=127.0.0.1:8088
DB_PASSWORD=uma-senha-forte
ASAAS_ACCESS_TOKEN=sua-chave-do-asaas
ASAAS_WEBHOOK_TOKEN=um-token-seguro-criado-por-voce
```

Para gerar a chave do Laravel:

```bash
docker compose --env-file .env.production -f docker-compose.prod.yml build app
docker compose --env-file .env.production -f docker-compose.prod.yml run --rm app php artisan key:generate --show
```

Copie o valor gerado e coloque em `APP_KEY` no `.env.production`.

## 4. Subir a aplicacao sem Cloudflare no compose

Use esta opcao se voce ja tem um Cloudflare Tunnel rodando no servidor e quer apenas apontar ele para o Shopla.

```bash
cd /opt/sites/shopla
docker compose --env-file .env.production -f docker-compose.prod.yml up -d --build
```

Teste localmente no servidor:

```bash
curl -I http://127.0.0.1:8088
```

No Cloudflare Zero Trust, adicione um Public Hostname no tunnel existente:

```text
Hostname: shopla.com.br
Service: http://localhost:8088
```

Se o seu `cloudflared` existente roda em Docker, talvez `localhost` aponte para o proprio container do tunnel. Nesse caso, prefira uma destas opcoes:

1. Use o tunnel proprio do Shopla, explicado no passo 5. E o caminho mais simples.
2. Conecte o container `cloudflared` existente na rede Docker do Shopla:

```bash
docker network connect shopla_shopla NOME_DO_CONTAINER_CLOUDFLARED
```

Depois, no Public Hostname do tunnel existente, use:

```text
Service: http://shopla-nginx-1:80
```

Se o nome do container nginx for diferente, veja com:

```bash
docker compose --env-file .env.production -f docker-compose.prod.yml ps
```

## 5. Subir a aplicacao com um tunnel proprio do Shopla

Use esta opcao se quiser criar um tunnel separado so para o Shopla.

No Cloudflare Zero Trust:

1. Va em `Networks > Tunnels`.
2. Clique em `Create a tunnel`.
3. Escolha `Cloudflared`.
4. Copie o token do tunnel.
5. Em `Public Hostnames`, adicione:

```text
Hostname: shopla.com.br
Service: http://nginx:80
```

Opcionalmente adicione tambem:

```text
Hostname: www.shopla.com.br
Service: http://nginx:80
```

No servidor, coloque o token em:

```env
CLOUDFLARED_TOKEN=cole-o-token-aqui
```

Suba com os dois arquivos de compose:

```bash
cd /opt/sites/shopla
docker compose --env-file .env.production -f docker-compose.prod.yml -f docker-compose.cloudflare.yml up -d --build
```

Ver logs:

```bash
docker compose --env-file .env.production -f docker-compose.prod.yml logs -f
```

Ver containers:

```bash
docker compose --env-file .env.production -f docker-compose.prod.yml ps
```

Se estiver usando o compose do Cloudflare, use:

```bash
docker compose --env-file .env.production -f docker-compose.prod.yml -f docker-compose.cloudflare.yml logs -f
docker compose --env-file .env.production -f docker-compose.prod.yml -f docker-compose.cloudflare.yml ps
```

## 6. Atualizar depois de novas alteracoes

```bash
cd /opt/sites/shopla
git pull
docker compose --env-file .env.production -f docker-compose.prod.yml up -d --build
```

Com tunnel proprio:

```bash
cd /opt/sites/shopla
git pull
docker compose --env-file .env.production -f docker-compose.prod.yml -f docker-compose.cloudflare.yml up -d --build
```

As migrations rodam automaticamente porque `RUN_MIGRATIONS=true`.

## 7. Configurar webhook do Asaas

No painel do Asaas, crie um webhook apontando para:

```text
https://shopla.com.br/webhooks/asaas
```

Use o mesmo token configurado em:

```env
ASAAS_WEBHOOK_TOKEN=
```

Eventos recomendados:

```text
CHECKOUT_PAID
CHECKOUT_CANCELED
CHECKOUT_EXPIRED
PAYMENT_CONFIRMED
PAYMENT_RECEIVED
PAYMENT_OVERDUE
PAYMENT_REFUNDED
SUBSCRIPTION_INACTIVATED
SUBSCRIPTION_DELETED
```

## 8. Se ja existir Nginx/Apache no servidor

Se a porta 80 ja estiver em uso, mantenha o `.env.production` assim:

```env
APP_PORT=127.0.0.1:8088
```

Depois suba de novo:

```bash
docker compose --env-file .env.production -f docker-compose.prod.yml up -d
```

Nesse caso, o proxy do servidor deve apontar para:

```text
http://127.0.0.1:8088
```

## 9. Comandos uteis

Entrar no container:

```bash
docker compose --env-file .env.production -f docker-compose.prod.yml exec app bash
```

Rodar migrations manualmente:

```bash
docker compose --env-file .env.production -f docker-compose.prod.yml exec app php artisan migrate --force
```

Limpar cache:

```bash
docker compose --env-file .env.production -f docker-compose.prod.yml exec app php artisan optimize:clear
```

Backup do banco:

```bash
docker compose --env-file .env.production -f docker-compose.prod.yml exec db pg_dump -U shopla shopla > shopla-backup.sql
```
