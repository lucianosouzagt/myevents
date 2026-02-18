Proposta:
- Atualizar variáveis no .env.production para usar o Poste como SMTP com remetente no-reply@myenvents.com.br
- Recarregar caches de configuração e validar envio

Ajustes no .env.production:
- MAIL_MAILER=smtp
- MAIL_HOST=mail.myenvents.com.br
- MAIL_PORT=587
- MAIL_ENCRYPTION=tls
- MAIL_USERNAME=no-reply@myenvents.com.br
- MAIL_PASSWORD=<SENHA_GERADA_NO_POSTE>
- MAIL_FROM_ADDRESS=no-reply@myenvents.com.br
- MAIL_FROM_NAME="MyEvents"
- (Opcional) QUEUE_CONNECTION=redis para envio assíncrono

Aplicação das mudanças (após editar o .env.production):
- Subir/recriar com env file:
  docker compose --env-file .env.production -f infra/docker/docker-compose.prod.yml up -d --build --remove-orphans
- Recarregar caches:
  docker compose -f infra/docker/docker-compose.prod.yml exec app php artisan optimize:clear && \
  docker compose -f infra/docker/docker-compose.prod.yml exec app php artisan config:cache route:cache view:cache

Teste rápido:
- docker compose -f infra/docker/docker-compose.prod.yml exec app php artisan tinker
- No Tinker:
  Mail::raw('Teste MyEvents via Poste', fn($m)=>$m->to('SEU_EMAIL@DOMINIO')->subject('Teste Poste'));

Observações:
- Confirme no Poste que a caixa no-reply@myenvents.com.br existe e a senha corresponde ao MAIL_PASSWORD
- DNS do domínio myenvents.com.br: MX apontando para mail.myenvents.com.br, SPF (v=spf1 mx -all), DKIM/DMARC publicados
- Portas abertas: 587 (Submission TLS), 465 (SMTPS) opcional; 25 para recebimento externo