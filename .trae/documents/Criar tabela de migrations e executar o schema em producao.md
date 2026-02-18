Objetivo: remover o erro 500 no cadastro causado por “relation "users" does not exist” (migrations não criadas).

Passo 1 — Verificar conectividade e serviço do banco:

* docker compose -f infra/docker/docker-compose.prod.yml ps

* Se o Postgres não estiver “Up (healthy)”: docker compose -f infra/docker/docker-compose.prod.yml up -d pgsql

* Verificar conexão a partir do app (opcional): docker compose -f infra/docker/docker-compose.prod.yml exec app sh -lc 'php -r "try{$dsn="pgsql:host=".getenv("DB\_HOST").";port=".getenv("DB\_PORT").";dbname=".getenv("DB\_DATABASE"); new PDO($dsn,getenv("DB\_USERNAME"),getenv("DB\_PASSWORD")); echo "DB OK\n";}catch(Exception $e){echo "DB ERR ".$e->getMessage()."\n";}"'

Passo 2 — Executar as migrations (cria a tabela migrations automaticamente):

* docker compose -f infra/docker/docker-compose.prod.yml exec app php artisan migrate --force

* Se aparecer “could not translate host name ‘pgsql’”, aguarde o healthcheck do banco e repita o comando.

Passo 3 — Validar o schema no Postgres:

* docker compose -f infra/docker/docker-compose.prod.yml exec pgsql sh -lc 'psql -U myevents -d myevents -c "\dt"'

* Conferir existência das tabelas: users, password\_reset\_tokens, sessions, etc.

Passo 4 — Testar e coletar logs em caso de falha:

* Tentar cadastrar em <https://myevents.com.br/register>

* Se falhar, coletar: docker compose -f infra/docker/docker-compose.prod.yml exec app tail -n 200 storage/logs/laravel.log

Passo 5 — Pós-ajustes (se necessário):

* Permissões: docker compose -f infra/docker/docker-compose.prod.yml exec app sh -lc "chown -R www-data:www-data storage bootstrap/cache"

* Recriar caches: docker compose -f infra/docker/docker-compose.prod.yml exec app sh -lc "php artisan optimize:clear && php artisan config:cache route:cache view:cache"

