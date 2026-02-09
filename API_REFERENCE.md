# 📖 Documentação da API - MyEvents

Esta documentação fornece detalhes sobre os endpoints da API, parâmetros necessários e exemplos de execução usando `curl`.

URL Base: `http://localhost:8081/api`

## 🔐 Autenticação

### Registrar Novo Usuário
Cria uma nova conta e retorna o token de acesso.

**POST** `/register`

**Body:**
```json
{
    "name": "Luciano",
    "email": "luciano@example.com",
    "password": "password123",
    "password_confirmation": "password123"
}
```

**Exemplo curl:**
```bash
curl -X POST http://localhost:8081/api/register \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{"name": "Luciano", "email": "luciano@example.com", "password": "password123", "password_confirmation": "password123"}'
```

---

### Login
Autentica um usuário existente e retorna o token.

**POST** `/login`

**Body:**
```json
{
    "email": "luciano@example.com",
    "password": "password123"
}
```

**Exemplo curl:**
```bash
curl -X POST http://localhost:8081/api/login \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{"email": "luciano@example.com", "password": "password123"}'
```

**Resposta de Sucesso:**
```json
{
    "access_token": "1|abcdef...",
    "token_type": "Bearer",
    "user": { "id": 1, "name": "Luciano", ... }
}
```

> ⚠️ **Nota:** Para todas as requisições protegidas abaixo, adicione o header:
> `Authorization: Bearer <seu_token_aqui>`

---

### Logout
Revoga o token atual.

**POST** `/logout` (Protegido)

**Exemplo curl:**
```bash
curl -X POST http://localhost:8081/api/logout \
  -H "Authorization: Bearer 1|abcdef..." \
  -H "Accept: application/json"
```

---

## 🎉 Eventos

### Listar Eventos (Público)
Retorna todos os eventos públicos ou, se autenticado, pode incluir lógica específica (no momento, focado em eventos públicos).

**GET** `/events`

**Exemplo curl:**
```bash
curl -X GET http://localhost:8081/api/events \
  -H "Accept: application/json"
```

### Detalhes do Evento
**GET** `/events/{id}`

**Exemplo curl:**
```bash
curl -X GET http://localhost:8081/api/events/1 \
  -H "Accept: application/json"
```

### Criar Evento (Protegido)
**POST** `/events`

**Body:**
```json
{
    "title": "Workshop de Laravel",
    "description": "Aprenda Laravel do zero",
    "location": "Online",
    "start_time": "2026-12-01 10:00:00",
    "end_time": "2026-12-01 12:00:00",
    "capacity": 50,
    "is_public": true
}
```

**Exemplo curl:**
```bash
curl -X POST http://localhost:8081/api/events \
  -H "Authorization: Bearer <TOKEN>" \
  -H "Content-Type: application/json" \
  -d '{
    "title": "Workshop de Laravel",
    "description": "Aprenda Laravel do zero",
    "location": "Online",
    "start_time": "2026-12-01 10:00:00",
    "end_time": "2026-12-01 12:00:00",
    "capacity": 50,
    "is_public": true
  }'
```

### Atualizar Evento (Protegido - Apenas Organizador)
**PUT** `/events/{id}`

**Exemplo curl:**
```bash
curl -X PUT http://localhost:8081/api/events/1 \
  -H "Authorization: Bearer <TOKEN>" \
  -H "Content-Type: application/json" \
  -d '{"title": "Workshop Avançado de Laravel"}'
```

### Excluir Evento (Protegido - Apenas Organizador)
**DELETE** `/events/{id}`

**Exemplo curl:**
```bash
curl -X DELETE http://localhost:8081/api/events/1 \
  -H "Authorization: Bearer <TOKEN>"
```

---

## ✉️ Convites & RSVP

### Enviar Convites (Protegido - Apenas Organizador)
Dispara e-mails com token único para os convidados.

**POST** `/invitations`

**Body:**
```json
{
    "event_id": 1,
    "emails": ["convidado@teste.com", "outro@teste.com"]
}
```

**Exemplo curl:**
```bash
curl -X POST http://localhost:8081/api/invitations \
  -H "Authorization: Bearer <TOKEN>" \
  -H "Content-Type: application/json" \
  -d '{"event_id": 1, "emails": ["convidado@teste.com"]}'
```

### Ver Convite (Público)
Visualiza detalhes do convite através do token recebido no e-mail.

**GET** `/invitations/{token}`

**Exemplo curl:**
```bash
curl -X GET http://localhost:8081/api/invitations/TOKEN_DO_CONVITE
```

### Confirmar Presença (RSVP) (Público)
**POST** `/invitations/{token}/rsvp`

**Body:**
```json
{
    "status": "confirmed" 
}
```
*(Status pode ser `confirmed` ou `declined`)*

**Exemplo curl:**
```bash
curl -X POST http://localhost:8081/api/invitations/TOKEN_DO_CONVITE/rsvp \
  -H "Content-Type: application/json" \
  -d '{"status": "confirmed"}'
```

### Obter QR Code (Público)
Retorna o QR Code (SVG) para o check-in.

**GET** `/invitations/{token}/qrcode`

**Exemplo curl:**
```bash
curl -X GET http://localhost:8081/api/invitations/TOKEN_DO_CONVITE/qrcode
```

---

## ✅ Check-in

### Realizar Check-in (Protegido - Apenas Organizador)
O organizador escaneia o QR Code e envia o token para validar a entrada.

**POST** `/checkin`

**Body:**
```json
{
    "event_id": 1,
    "token": "TOKEN_DO_QR_CODE"
}
```

**Exemplo curl:**
```bash
curl -X POST http://localhost:8081/api/checkin \
  -H "Authorization: Bearer <TOKEN>" \
  -H "Content-Type: application/json" \
  -d '{"event_id": 1, "token": "TOKEN_DO_QR_CODE"}'
```

---
**Dica:** Para testar facilmente, você pode importar esses exemplos para o Postman ou Insomnia.
