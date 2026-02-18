# Deploy em Produção (Linux Cloud)

## Visão Geral
- Stack: Nginx (reverse proxy), PHP-FPM (Laravel), Postgres/Redis (opcional/gerenciado), Vite (assets).
- Ambiente: APP_ENV=production, APP_DEBUG=false, HTTPS recomendado.
- Sem segredos no código. Use `.env` com variáveis corretas (não versionar).

## Option A — Docker (Recomendado)
1. Pré-requisitos
   - Docker Engine e Compose Plugin instalados
   - DNS apontando para o servidor (opcional)
2. Preparar repositório
   - `git clone <repo>`
   - `cp .env.example .env`
   - Ajuste `.env` (APP_URL, DB_*, MAIL_*, CACHE/SESSION/QUEUE)
3. Subida simplificada
   - `docker compose --env-file .env.production -f infra/docker/docker-compose.prod.yml up -d --build`
   - O container `app` copia `.env.production` para `.env` (se ainda não existir), executa `composer install` e gera `APP_KEY` automaticamente na inicialização.
   - `docker compose -f infra/docker/docker-compose.prod.yml exec app php artisan migrate --force`
   - `docker compose -f infra/docker/docker-compose.prod.yml exec app php artisan storage:link`
   - `docker compose -f infra/docker/docker-compose.prod.yml exec app php artisan config:cache route:cache view:cache`
5. HTTPS com Traefik
   - Ajuste `TRAEFIK_HOST` e `TRAEFIK_EMAIL` em `.env.production`
   - Traefik publica 80/443, obtém certificados via ACME HTTP-01 e encaminha para Nginx em 80
   - Certifique-se de que o DNS do domínio aponta para o servidor e as portas 80/443 estão liberadas
6. E-mail com Poste.io
   - Serviço `poste` incluído no compose (imagem `analogic/poste.io`)
   - DNS:
     - `A` para `mail.<seu-dominio>` apontando para o IP do servidor
     - `MX` para o domínio principal apontando para `mail.<seu-dominio>`
     - `SPF`: `v=spf1 mx -all`
     - `DKIM`: gerar no painel do Poste e publicar o TXT fornecido
     - `DMARC`: `_dmarc.<seu-dominio>` com política (ex.: `v=DMARC1; p=quarantine; rua=mailto:postmaster@<seu-dominio>`)
     - PTR (reverse DNS) do IP para `mail.<seu-dominio>` (solicitar ao provedor)
   - Variáveis:
     - Em `.env.production`: `POSTE_HOST=mail.<seu-dominio>`
     - Laravel: `MAIL_HOST=mail.<seu-dominio>`, `MAIL_PORT=587`, `MAIL_ENCRYPTION=tls`, `MAIL_USERNAME`/`MAIL_PASSWORD` de uma conta criada no Poste, `MAIL_FROM_ADDRESS=no-reply@<seu-dominio>`
   - Painel do Poste publicado via Traefik em `https://mail.<seu-dominio>` (certificado automático)

## Option B — Bare Metal (Sem Docker)
1. Instalar pacotes
   - PHP 8.3 + extensões: pdo_pgsql, mbstring, intl, opcache
   - Nginx
   - Node 20 (apenas para build local) e Composer 2
2. Build e deploy
   - `composer install --no-dev --optimize-autoloader`
   - `npm ci && npm run build` (gera `public/build`)
   - Configurar Nginx com root em `public/` e FastCGI para php-fpm
3. Pós-subida
   - `php artisan key:generate`
   - `php artisan migrate --force && php artisan storage:link`
   - `php artisan config:cache route:cache view:cache`
4. Serviços auxiliares
   - Queue Worker (systemd): `php artisan queue:work --sleep=1 --tries=3`
   - Scheduler (crontab): `* * * * * cd /var/www/html && php artisan schedule:run >> /dev/null 2>&1`

## Variáveis de Ambiente (Checklist)
- APP_NAME, APP_ENV=production, APP_KEY, APP_DEBUG=false, APP_URL
- DB_CONNECTION=pgsql, DB_HOST, DB_PORT, DB_DATABASE, DB_USERNAME, DB_PASSWORD
- CACHE_STORE, QUEUE_CONNECTION, SESSION_DRIVER
- MAIL_MAILER, MAIL_HOST, MAIL_PORT, MAIL_USERNAME, MAIL_PASSWORD, MAIL_FROM_*
- REDIS_* (se usar)
- VITE_APP_NAME

Observação:
- O docker-compose já inclui Postgres (serviço `pgsql`). Ajuste `.env.production` com DB_* conforme desejado.
 - Para simplificar, usamos `SESSION_DRIVER=database` e `QUEUE_CONNECTION=sync`, dispensando Redis.

### Troubleshooting
- Erro em build: `composer install ... exit code: 1`
  - Verifique acesso à internet do servidor, DNS e firewall.
  - A imagem já inclui extensões necessárias (zip, gd, intl, pdo_pgsql). Se persistir, rode o composer após subir os serviços:  
    `docker compose -f infra/docker/docker-compose.prod.yml exec app composer install --no-dev --prefer-dist --optimize-autoloader`
  - Depois execute:  
    `php artisan key:generate && php artisan migrate --force && php artisan storage:link && php artisan config:cache route:cache view:cache`

## Segurança e Performance
- Habilitar OPcache (já ativo na imagem) e caches Laravel
- Forçar HTTPS e HSTS
- Limitar upload e tamanho de corpo no Nginx conforme necessidade
- Backups periódicos do banco
- Não executar `php artisan serve` em produção

## Operação
- Logs em `storage/logs/laravel.log` (bind via volume no Docker)
- Monitorar saúde com `GET /up` (Laravel 11)
- Atualizações: `git pull` + `docker compose ... up -d --build` + `artisan migrate`
