# Documentação Técnica: Sistema de Simulação e Envio de Notificações para Eventos

Este documento detalha a arquitetura e implementação de um sistema robusto para envio de convites e confirmação de presença (RSVP) via E-mail e WhatsApp, incluindo estratégias de simulação (staging), filas, retentativas e monitoramento.

---

## 1. Arquitetura do Sistema

O sistema segue uma arquitetura baseada em eventos e filas para garantir que o processo de envio não bloqueie a interface do usuário e garanta a entrega mesmo em casos de falha temporária dos provedores.

### Diagrama de Fluxo de Dados

```mermaid
graph TD
    A[Cliente/Frontend] -->|POST /api/invite/send| B[API Gateway / Controller]
    B -->|Valida Dados| C{Validação OK?}
    C -->|Não| D[Retorna Erro 400]
    C -->|Sim| E[Salva/Atualiza Convite 'Pending']
    E --> F{Modo Debug?}
    F -->|Sim| G[Loga Payload no DB]
    G --> H[Retorna Sucesso (Simulado)]
    F -->|Não| I[Enfileira Job (Queue)]
    I --> J[Worker Process]
    J --> K{Canal?}
    K -->|Email| L[SMTP Provider (Mailhog/AWS SES)]
    K -->|WhatsApp| M[WhatsApp API (Twilio/Meta)]
    L -->|Sucesso/Falha| N[Atualiza Log de Convite]
    M -->|Sucesso/Falha| N
    N --> O[Webhook de Callback (Opcional)]
```

---

## 2. Configuração de Serviços

### 2.1 E-mail (SMTP)
Para ambiente de desenvolvimento, recomenda-se o uso de **Mailhog** ou **Mailtrap**.

**Variáveis de Ambiente (.env):**
```bash
MAIL_MAILER=smtp
MAIL_HOST=mailhog
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="no-reply@myevents.com"
MAIL_FROM_NAME="MyEvents System"
```

### 2.2 WhatsApp (Twilio Sandbox)
Para desenvolvimento, utiliza-se o Sandbox do Twilio.

**Variáveis de Ambiente (.env):**
```bash
TWILIO_SID=ACxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
TWILIO_TOKEN=your_auth_token
TWILIO_WHATSAPP_FROM="whatsapp:+14155238886" # Número Sandbox
```

---

## 3. Templates de Mensagens

O sistema deve suportar templates com placeholders dinâmicos para personalização.

### Placeholders Suportados
*   `{{guest_name}}`: Nome do convidado.
*   `{{event_title}}`: Título do evento.
*   `{{event_date}}`: Data formatada.
*   `{{rsvp_link}}`: Link único para confirmação.

### Exemplo Template WhatsApp
```text
Olá {{guest_name}}! 
Você foi convidado para {{event_title}} no dia {{event_date}}.
Confirme sua presença: {{rsvp_link}}
```

---

## 4. Filas e Retry Mechanisms

Para garantir confiabilidade, nenhum envio externo deve ser síncrono.

*   **Queue Driver:** Redis ou Database.
*   **Retry Policy:**
    *   Tentativas máximas: 3
    *   Backoff strategy: Exponencial (10s, 30s, 60s).
*   **Dead Letter Queue (DLQ):** Mensagens que falharem após 3 tentativas são movidas para uma tabela de `failed_jobs` para análise manual.

---

## 5. Logs e Monitoramento

Todo disparo deve gerar um registro na tabela `invitation_logs`.

### Estrutura da Tabela de Logs
| Coluna | Tipo | Descrição |
| :--- | :--- | :--- |
| `id` | UUID | Identificador único do log. |
| `invitation_id` | UUID | FK para o convite. |
| `channel` | VARCHAR | 'email' ou 'whatsapp'. |
| `status` | VARCHAR | 'queued', 'sent', 'failed', 'delivered'. |
| `response` | TEXT | Resposta JSON do provedor (ou mensagem de erro). |
| `sent_at` | TIMESTAMP | Data/hora do disparo. |

**Métricas de Sucesso:**
*   Taxa de Entrega = (Entregues / Total Enviado) * 100
*   Taxa de Conversão = (Confirmados / Entregues) * 100

---

## 6. Ambiente de Staging e Modo Debug

Para evitar custos e spam durante o desenvolvimento:

1.  **Flag `debug=true` na Requisição:**
    *   O sistema processa toda a lógica (validação, persistência).
    *   No momento do envio, o `Service` detecta a flag.
    *   Em vez de chamar a API externa, grava o conteúdo da mensagem no campo `response` do log com status `debug`.
    *   Retorna sucesso para o frontend.

2.  **Mocking de Serviços:**
    *   Em testes automatizados, as classes `Mail` e `TwilioClient` devem ser mockadas.

---

## 7. Validação de Dados

*   **E-mail:** Validação RFC padrão + verificação de domínio (DNS check opcional).
*   **Telefone (BR):**
    *   Regex: `/^(\+?55|0)?\s?\(?\d{2}\)?\s?\d{4,5}-?\d{4}$/`
    *   Sanitização: Remover tudo que não for dígito. Se não começar com `55` e tiver 10/11 dígitos, adicionar prefixo.

---

## 8. Estratégia de Testes

A cobertura deve garantir 100% dos cenários críticos.

### Cenários de Teste (Unitários e Integração)
1.  **Sucesso no Envio:** Verificar se o log é criado com status `sent`.
2.  **Falha no Provedor:** Simular exceção na API do Twilio e verificar se o job entra em retry ou falha.
3.  **Dados Inválidos:** Tentar enviar para email mal formatado (deve retornar 422).
4.  **Modo Debug:** Verificar se a API externa **NÃO** foi chamada.
5.  **Duplicidade:** Garantir que convites repetidos não geram múltiplos tokens (ou atualizam o existente).

---

## 9. Documentação da API

### POST `/api/events/{eventId}/guests/{invitationId}/send`

**Headers:**
`Content-Type: application/json`
`Authorization: Bearer {token}`

**Body:**
```json
{
  "channels": ["email", "whatsapp"],
  "debug": true
}
```

**Response (200 OK):**
```json
{
  "success": true,
  "results": {
    "email": "sent",
    "whatsapp": {
      "status": "debug",
      "sid": "debug_654321",
      "response": "Debug mode: Message logged only."
    }
  }
}
```

---

## 10. Exemplos de Implementação

Abaixo, exemplos de como implementar o *Service* de envio em Node.js e Python, seguindo a lógica descrita.

### Exemplo 1: Node.js (Express + Twilio)

```javascript
// services/notificationService.js
const twilio = require('twilio');
const nodemailer = require('nodemailer');

class NotificationService {
    constructor() {
        this.twilioClient = twilio(process.env.TWILIO_SID, process.env.TWILIO_TOKEN);
        this.emailTransporter = nodemailer.createTransport({
            host: process.env.SMTP_HOST,
            port: process.env.SMTP_PORT,
            auth: { user: process.env.SMTP_USER, pass: process.env.SMTP_PASS }
        });
    }

    async sendWhatsApp(to, message, isDebug = false) {
        if (isDebug) {
            console.log(`[DEBUG] WhatsApp to ${to}: ${message}`);
            return { status: 'debug', sid: 'mock_sid', body: message };
        }

        try {
            const result = await this.twilioClient.messages.create({
                body: message,
                from: process.env.TWILIO_FROM,
                to: `whatsapp:${to}`
            });
            return { status: 'sent', sid: result.sid };
        } catch (error) {
            console.error('Twilio Error:', error);
            throw new Error('Failed to send WhatsApp');
        }
    }

    async sendEmail(to, subject, html, isDebug = false) {
        if (isDebug) {
            console.log(`[DEBUG] Email to ${to}: ${subject}`);
            return { status: 'debug', messageId: 'mock_id' };
        }

        try {
            const info = await this.emailTransporter.sendMail({
                from: process.env.EMAIL_FROM,
                to, subject, html
            });
            return { status: 'sent', messageId: info.messageId };
        } catch (error) {
            console.error('SMTP Error:', error);
            throw new Error('Failed to send Email');
        }
    }
}

// controller/inviteController.js
const service = new NotificationService();

exports.sendInvite = async (req, res) => {
    const { phone, email, name, eventName, debug } = req.body;
    const message = `Olá ${name}, você foi convidado para ${eventName}!`;
    
    try {
        const results = {};
        if (phone) results.whatsapp = await service.sendWhatsApp(phone, message, debug);
        if (email) results.email = await service.sendEmail(email, 'Convite', `<h1>${message}</h1>`, debug);
        
        res.json({ success: true, data: results });
    } catch (e) {
        res.status(500).json({ error: e.message });
    }
};
```

### Exemplo 2: Python (FastAPI + Celery)

```python
# tasks.py (Celery Worker)
import os
from celery import Celery
from twilio.rest import Client
import smtplib
from email.mime.text import MIMEText

app = Celery('tasks', broker='redis://localhost:6379/0')

@app.task(bind=True, max_retries=3)
def send_whatsapp_task(self, to_number, message_body, debug=False):
    if debug:
        return {"status": "debug", "body": message_body}

    try:
        client = Client(os.getenv('TWILIO_SID'), os.getenv('TWILIO_TOKEN'))
        message = client.messages.create(
            from_=os.getenv('TWILIO_FROM'),
            body=message_body,
            to=f'whatsapp:{to_number}'
        )
        return {"status": "sent", "sid": message.sid}
    except Exception as exc:
        raise self.retry(exc=exc, countdown=60)

# main.py (FastAPI)
from fastapi import FastAPI, BackgroundTasks
from pydantic import BaseModel
from tasks import send_whatsapp_task

app = FastAPI()

class InviteRequest(BaseModel):
    phone: str
    name: str
    event_name: str
    debug: bool = False

@app.post("/send-invite")
async def send_invite(req: InviteRequest):
    message = f"Olá {req.name}, convite para {req.event_name}!"
    
    # Em Python, podemos usar BackgroundTasks do FastAPI para coisas simples
    # ou chamar a task do Celery para produção robusta
    task = send_whatsapp_task.delay(req.phone, message, req.debug)
    
    return {"status": "queued", "task_id": task.id}
```
