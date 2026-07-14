# Deploy na HostGator pelo cPanel

## Estrutura usada

- Aplicacao Laravel: `/home4/USUARIO_CPANEL/shopla`
- Raiz publica do dominio: `/home4/USUARIO_CPANEL/public_html`
- Arquivos enviados pelos usuarios: `/home4/USUARIO_CPANEL/public_html/storage`

O arquivo `.env` fica somente dentro de `shopla` e nunca deve ser colocado em `public_html` ou em um ZIP de deploy.

## Configuracao do ambiente

Use `.env.hostgator.example` apenas como referencia e preencha os valores diretamente no `.env` do servidor. Os pontos que diferem do antigo Docker sao:

```env
DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
QUEUE_CONNECTION=sync
FILESYSTEM_DISK=public
PUBLIC_FILESYSTEM_ROOT=/home4/USUARIO_CPANEL/public_html/storage
```

Nao existe worker permanente nem tarefa agendada no projeto atual. Portanto, nao mantenha um cron do Laravel na hospedagem compartilhada.

## `public_html/index.php`

O arquivo `deploy/hostgator/public_html/index.php` e um modelo para instalacao inicial. Ele aponta para a aplicacao em `../shopla` e informa ao Laravel que `public_html` e o diretorio publico real.

Em atualizacoes, preserve o `index.php` que ja funciona no servidor. Preserve tambem:

- `public_html/.htaccess`, inclusive o handler PHP criado pelo cPanel;
- `public_html/.well-known`;
- `public_html/.user.ini`, caso exista;
- `public_html/storage` e todas as imagens;
- `/home4/USUARIO_CPANEL/shopla/.env`.

## Gerar uma atualizacao

No PowerShell, na raiz do projeto:

```powershell
powershell -ExecutionPolicy Bypass -File .\scripts\build-hostgator-update.ps1
```

O script executa `npm ci`, compila os assets, instala um `vendor` de producao sem dependencias de desenvolvimento e cria dois ZIPs em `dist`:

- `shopla-app-update-*.zip`: `vendor`, locks, cache de pacotes e configuracao alterada;
- `shopla-public-build-*.zip`: somente a pasta `build` compilada.

O script tambem gera hashes SHA-256 e restaura as dependencias de desenvolvimento locais ao terminar.

## Atualizar sem SSH

1. Faca um backup novo do MySQL e das duas pastas da hospedagem.
2. No Gerenciador de Arquivos, envie o ZIP da aplicacao para `shopla`.
3. Renomeie `vendor` para `vendor-before-update`.
4. Extraia o ZIP da aplicacao dentro de `shopla`.
5. Envie o ZIP publico para `public_html`.
6. Renomeie `build` para `build-before-update`.
7. Extraia o ZIP publico dentro de `public_html`.
8. Confira as permissoes: pastas `0755`, arquivos `0644` e `.env` `0600`.
9. Teste pagina inicial, login normal, login Google, imagens, envio de e-mail e cobranca/webhook Asaas.
10. Depois de um periodo estavel, exclua `vendor-before-update`, `build-before-update` e os ZIPs do servidor.

Se o site falhar, remova os novos diretórios `vendor` e `build`, devolva os nomes anteriores e restaure os arquivos de configuracao a partir do backup.

## Cloudflare

- Registros raiz e `www` apontam para a HostGator e usam proxy laranja.
- SSL/TLS usa `Completo (estrito)`.
- HTTPS obrigatorio permanece ativado.
- A regra de reescrita remove apenas o parametro `iss` do `GET /auth/google/callback` para evitar o falso positivo do ModSecurity.

## Permissoes

- Pastas: `0755`
- Arquivos: `0644`
- `.env`: `0600`

Se um ZIP extraido pelo cPanel voltar a criar `0777/0666`, execute temporariamente o ajuste de permissoes e exclua o cron assim que o arquivo de confirmacao for gerado.
