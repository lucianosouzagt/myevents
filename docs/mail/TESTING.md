# Testes de Envio de E-mail (MailTest)

## Visão Geral
- Endpoint de teste: `POST /api/mail/test`
- Ativação: defina `MAIL_TEST_ENABLED=true` no `.env.production` (ou `config/mailtest.php`)
- Objetivo: validar envio sem impactar produção (modo simulado) e validar templates, anexos e múltiplos destinatários.

## Payload
```json
{
  "subject": "Assunto",
  "template": "emails.demo", // opcional
  "html": "<p>Body HTML</p>", // opcional (use template OU html)
  "data": { "nome": "Exemplo" }, // para templates
  "to": ["destino@example.com"],
  "cc": ["cc@example.com"],
  "bcc": ["bcc@example.com"],
  "attachments": [
    { "path": "/caminho/arquivo.pdf", "name": "arquivo.pdf", "mime": "application/pdf" }
  ],
  "simulate": true
}
```

## Respostas
- Simulado: `{ "status": "simulated", ... }` (nenhum e-mail é enviado)
- Real: `{ "status": "sent", ... }`
- Erros de validação: HTTP 422 com `{"error": "mensagem"}`

## Logs
- Canal dedicado: `mailtest` → `storage/logs/mailtest.log`
- Contém contexto completo: destinatários, assunto, anexos, simulação.

## Segurança
- Produção: mantenha `MAIL_TEST_ENABLED=false` por padrão.
- Opcionalmente, proteja com `auth:sanctum` ou IP allowlist (ajuste nas rotas conforme necessidade).

## Poste.io — Portas e TLS
- Portas publicadas (compose): SMTP 25/465/587, IMAP 143/993, POP3 110/995 (TLS implícito em 465/993/995; STARTTLS em 587/143/110).
- Healthcheck configurado para 25/587/993 no serviço `poste`.
- Certificados:
  - Painel via Traefik (ACME HTTP-01).
  - Serviços de e-mail usam TLS do Dovecot/Postfix.

## Procedimento de Rollback
1. Desativar o endpoint de teste:
   - Em `.env.production`: `MAIL_TEST_ENABLED=false`
   - `docker compose --env-file .env.production -f infra/docker/docker-compose.prod.yml up -d`
2. Reverter healthcheck (opcional):
   - Remova o bloco `healthcheck` do serviço `poste` no compose.
3. Limpar caches:
   - `docker compose -f infra/docker/docker-compose.prod.yml exec app php artisan optimize:clear && php artisan config:cache route:cache view:cache`

