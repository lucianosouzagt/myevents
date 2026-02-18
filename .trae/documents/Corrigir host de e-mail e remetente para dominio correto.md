* Corrigir o typo do domínio: trocar "myenvents.com.br" por "myevents.com.br" em POSTE\_HOST, MAIL\_HOST, MAIL\_USERNAME e MAIL\_FROM\_ADDRESS no .env.production.

* Recarregar config da aplicação e testar envio (Tinker) após subir com --env-file.

* Validar DNS: A de mail.myevents.com.br deve apontar para o IP do servidor.

* Garantir que o docker compose sempre rode com --env-file para evitar Host("") no Traefik.

