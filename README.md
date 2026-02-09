# Gestão de Eventos API

Sistema completo e escalável para Gestão de Eventos, desenvolvido com Laravel 11, PostgreSQL e Docker.

## 🚀 Tecnologias Utilizadas

- **PHP 8.3** + **Laravel 11**
- **PostgreSQL 15** (Banco de Dados)
- **Redis** (Filas e Cache)
- **Docker** & **Laravel Sail** (Ambiente de Desenvolvimento)
- **Simple QR Code** (Geração de QR Codes)
- **Sanctum** (Autenticação API)
- **MailHog** (Teste de E-mails)

## 📦 Funcionalidades Implementadas

### 👤 Usuários & Autenticação
- Cadastro e Login (API Sanctum)
- RBAC (Roles & Permissions) - Estrutura de banco pronta

### 🎉 Eventos
- CRUD Completo (Criar, Listar, Editar, Excluir)
- Controle de privacidade (Público/Privado)
- Regras de negócio via Service Layer

### ✉️ Convites & RSVP
- Envio de convites por e-mail (Assíncrono via Queue)
- Geração de Token Único por convite
- Endpoint público para Confirmação de Presença (RSVP)
- Prevenção de convites duplicados

### ✅ Check-in & QR Code
- Geração de QR Code para convidados confirmados
- Validação de QR Code no dia do evento (apenas organizador)
- Bloqueio de check-in duplicado

## 🛠️ Como Rodar o Projeto

### Pré-requisitos
- Docker & Docker Compose instalados

### Passo a Passo

1. **Clone o repositório:**
   ```bash
   git clone <url-do-repositorio>
   cd myevents
   ```

2. **Configure o ambiente:**
   ```bash
   cp .env.example .env
   ```
   *As configurações do Docker já estão ajustadas no `.env.example` (Portas: App 8081, DB 5434, Redis 6380).*

3. **Suba os containers:**
   ```bash
   ./vendor/bin/sail up -d
   ```

4. **Instale as dependências e gere a chave:**
   ```bash
   ./vendor/bin/sail composer install
   ./vendor/bin/sail artisan key:generate
   ```

5. **Execute as migrações:**
   ```bash
   ./vendor/bin/sail artisan migrate
   ```

6. **(Opcional) Popule o banco com dados falsos:**
   ```bash
   ./vendor/bin/sail artisan db:seed
   ```

## 🧪 Rodando os Testes

O projeto possui cobertura de testes automatizados para as principais funcionalidades.

```bash
./vendor/bin/sail artisan test
```

## 📚 Documentação da API (Endpoints Principais)

### Autenticação
- `GET /api/user` - Dados do usuário logado

### Eventos
- `GET /api/events` - Listar eventos públicos
- `POST /api/events` - Criar evento (Auth Required)
- `PUT /api/events/{id}` - Atualizar evento (Auth + Owner)
- `DELETE /api/events/{id}` - Excluir evento (Auth + Owner)

### Convites & RSVP
- `POST /api/invitations` - Enviar convites (Auth + Owner)
  - Body: `{ "event_id": 1, "emails": ["email@teste.com"] }`
- `GET /api/invitations/{token}` - Ver detalhes do convite
- `POST /api/invitations/{token}/rsvp` - Confirmar presença
  - Body: `{ "status": "confirmed" }`

### Check-in
- `GET /api/invitations/{token}/qrcode` - Obter QR Code do convite
- `POST /api/checkin` - Realizar Check-in (Auth + Owner)
  - Body: `{ "event_id": 1, "token": "token-do-convite" }`

## 📨 Testando E-mails

Os e-mails são interceptados pelo **MailHog**. Acesse o painel em:
`http://localhost:8025`

## 🏗️ Estrutura de Pastas

- `app/Services` - Lógica de Negócio (DDD-Lite)
- `app/Repositories` - Abstração de Banco de Dados
- `app/Models` - Entidades Eloquent
- `app/Http/Controllers` - Entrada da API
- `tests/Feature` - Testes de Integração

---
Desenvolvido com ❤️ e Boas Práticas.
