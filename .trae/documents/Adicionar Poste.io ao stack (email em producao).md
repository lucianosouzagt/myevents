Resumo
- Adicionar um servidor de e‑mail Poste.io na stack Docker
- Expor portas SMTP/IMAP/POP3 diretamente; publicar painel web via Traefik em mail.myevents.com.br
- Configurar DNS (A/MX/SPF/DKIM/DMARC e PTR) e ajustar .env de produção do Laravel

Serviço Poste.io no Compose
- Criar service poste (imagem: analogic/poste.io)
- Volumes: ./infra/mail/data:/data (persistência)
- Rede: usar a mesma network web do compose
- Publicar portas (sem conflito com Traefik):
  - 25:25 (SMTP), 587:587 (Submission TLS), 465:465 (SMTPS)
  - 143:143 (IMAP), 993:993 (IMAPS), 110:110 (POP3), 995:995 (POP3S)
- Não expor 80/443 diretamente; usar Traefik para painel
- Variáveis sugeridas (mínimas):
  - HOSTNAME=mail.myevents.com.br
  - HTTPS=OFF (TLS do painel via Traefik)
  - DISABLE_CLAMAV=1 (opcional; reduz RAM) e outras conforme política
- Traefik labels (painel):
  - Router HTTP/HTTPS Host(mail.myevents.com.br) → poste:80 (service port 80)
  - Certresolver letsencrypt habilitado no Traefik

DNS e Entregabilidade
- A: mail.myevents.com.br → IP do servidor
- MX: myevents.com.br → mail.myevents.com.br (prioridade 10)
- SPF: v=spf1 mx -all (ou incluir IP/relays conforme necessidade)
- DKIM: gerar no painel do Poste e publicar TXT conforme instrução
- DMARC: _dmarc.myevents.com.br TXT v=DMARC1; p=quarantine; rua=mailto:postmaster@myevents.com.br; fo=1
- PTR (reverse DNS): solicitar ao provedor apontar o IP público para mail.myevents.com.br
- Liberar portas 25/465/587/993/995 no firewall/SG; confirmar que seu provedor permite saída na 25 (recebimento e reputação)

Laravel (.env.production)
- MAIL_MAILER=smtp
- MAIL_HOST=mail.myevents.com.br
- MAIL_PORT=587
- MAIL_ENCRYPTION=tls
- MAIL_USERNAME=no-reply@myevents.com.br (criado no Poste)
- MAIL_PASSWORD=******
- MAIL_FROM_ADDRESS=no-reply@myevents.com.br
- MAIL_FROM_NAME="MyEvents"
- (Opcional) Usar filas: QUEUE_CONNECTION=redis e habilitar um worker

Operação
- Subir stack com env file:
  - docker compose --env-file .env.production -f infra/docker/docker-compose.prod.yml up -d --build --remove-orphans
- Criar domínio e usuário no painel do Poste (mail.myevents.com.br via Traefik)
- Publicar DKIM fornecido pelo Poste e aguardar propagação
- Testar envio:
  - docker compose -f infra/docker/docker-compose.prod.yml exec app php artisan tinker
  - Mail::raw('Teste MyEvents', fn($m)=>$m->to('SEU_EMAIL@DOMINIO')->subject('Teste'));

Alternativas e Observações
- Se o provedor bloquear a porta 25, configurar um SMTP relay (smart host) no Poste
- Se preferir painel do Poste com TLS próprio, podemos dedicar 80 ao Poste apenas para ACME; com Traefik ativo, recomendável manter TLS do painel via Traefik
- Após estabilizar, posso remover quaisquer routers de fallback por IP e reforçar HSTS no domínio do painel

Pronto para aplicar: ao aprovar, adiciono o serviço ao compose, labels do Traefik, atualizo documentação e preparo scripts de validação e teste.