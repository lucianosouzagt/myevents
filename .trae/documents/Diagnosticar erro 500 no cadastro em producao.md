Objetivo: Identificar a causa do 500 ao cadastrar em <https://myevents.com.br/register> e aplicar correção definitiva.

Passo 1 — Capturar logs exatos do erro:

* Rode e cole a saída completa após reproduzir o erro:

  * docker compose -f infra/docker/docker-compose.prod.yml exec app tail -n 200 storage/logs/laravel.log

  * docker compose -f infra/docker/docker-compose.prod.yml logs -n 200 app

  * docker compose -f infra/docker/docker-compose.prod.yml logs -n 100 nginx

Passo 2 — Verificar conectividade e ambiente do app:

* (Opcional, apenas se suspeitar de banco) Rode:

  * docker compose -f infra/docker/docker-compose.prod.yml exec app sh -lc 'php -r "try{$dsn="pgsql:host=".getenv("DB\_HOST").";port=".getenv("DB\_PORT").";dbname=".getenv("DB\_DATABASE"); new PDO($dsn,getenv("DB\_USERNAME"),getenv("DB\_PASSWORD")); echo "DB OK\n";}catch(Exception $e){echo "DB ERR ".$e->getMessage()."\n";}"'

  * docker compose -f infra/docker/docker-compose.prod.yml exec app php artisan migrate:status

Passo 3 — Diagnóstico rápido esperado (vamos confirmar pelos logs):

* Falha de conexão/timeout no Postgres (ex.: could not translate host name)

* Violação de unique (email duplicado) não capturada (pode indicar validação/colação)

* Erro de sessão/criptografia (APP\_KEY, cookies, permissões) — menos provável após ajustes

Passo 4 — Correções (aplicarei após analisar os logs):

* Se for banco intermittente: estabilizar dependências do app com healthchecks e depends\_on, e ajustar retry/timeout; checar rede do compose

* Se for unique/validação: revisar regra unique:users e mensagens; normalizar lower-case do email no controller/validação

* Se for sessão/APP\_KEY/permissões: recachear config, garantir APP\_KEY e permissões storage/bootstrap/cache

Passo 5 — Validação final:

* Reproduzir cadastro com sucesso, observar 200/302 e ausência de novas entradas de erro no laravel.log

* Rodar quick smoke em login e criação de evento

