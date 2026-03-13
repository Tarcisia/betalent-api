# BeTalent - Multi Gateway Payment API

API RESTful para gerenciamento de pagamentos com múltiplos gateways.

Este projeto foi desenvolvido como solução para o **Teste Prático Back-end da BeTalent**, implementando um sistema de pagamentos com fallback automático entre gateways.

---

# Tecnologias utilizadas

- PHP 8.2
- Laravel 12
- MySQL
- Laravel Sanctum (autenticação)
- Docker / Docker Compose
- API REST

---

# Funcionalidades

A API permite:

- Autenticação de usuários
- Controle de acesso por **roles**
- CRUD de produtos
- Registro de clientes
- Criação de compras
- Integração com **múltiplos gateways de pagamento**
- **Fallback automático entre gateways**
- Registro de transações
- Reembolso de compras
- Gerenciamento de gateways (ativar/desativar e prioridade)
- Listagem de clientes e compras

---

# Arquitetura

A aplicação foi estruturada seguindo boas práticas de separação de responsabilidades.


app/
Controllers → controle das rotas da API
Services → regras de negócio
Gateways → integração com APIs externas
Models → entidades do sistema
Requests → validação de dados
Resources → formatação das respostas


A lógica de pagamento utiliza um **orquestrador de gateways**, que tenta processar a cobrança respeitando a prioridade definida.

Fluxo:


API → PaymentOrchestrator → Gateway1
↓ erro
Gateway2
↓ sucesso
Transação registrada


---

# Banco de Dados

## users

| campo | descrição |
|------|-----------|
id | identificador |
email | email do usuário |
password | senha |
role | perfil do usuário |

Roles disponíveis:


ADMIN
MANAGER
FINANCE
USER


---

## gateways

| campo | descrição |
|------|-----------|
name | nome do gateway |
slug | identificador técnico |
priority | ordem de execução |
is_active | gateway ativo |

---

## clients

| campo | descrição |
|------|-----------|
name | nome do cliente |
email | email |

---

## products

| campo | descrição |
|------|-----------|
name | nome |
amount | valor |

---

## transactions

| campo | descrição |
|------|-----------|
client_id | cliente |
gateway_id | gateway utilizado |
external_id | id da transação externa |
status | status da compra |
refund_status | status do reembolso |
amount | valor total |
card_last_numbers | últimos dígitos do cartão |

---

## transaction_products

| campo | descrição |
|------|-----------|
transaction_id | compra |
product_id | produto |
quantity | quantidade |
unit_amount | valor unitário |
total_amount | valor total |

---

# Como executar o projeto

Existem duas formas de executar a aplicação.

---

# Configuração de ambiente

O projeto possui dois ambientes possíveis.

## Execução local


cp .env.example .env


Depois configure as credenciais do banco no `.env`.

## Execução com Docker


cp .env.docker .env
docker compose up --build


---

# 1️⃣ Execução local

### Requisitos

- PHP 8.2+
- Composer
- MySQL

---

### Passos

Clone o projeto


git clone <repo>
cd betalent-api


Instale dependências


composer install


Configure o `.env`


cp .env.example .env


Configure o banco


DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=betalent
DB_USERNAME=root
DB_PASSWORD=SEU_PASSWORD


Execute migrations


php artisan migrate
php artisan db:seed


Inicie o servidor


php artisan serve


API disponível em:


http://127.0.0.1:8000


---

# 2️⃣ Execução com Docker

### Requisitos

- Docker
- Docker Compose

Execute:


docker compose up --build


Após subir os containers execute:


docker exec -it betalant_api php artisan migrate
docker exec -it betalant_api php artisan db:seed


API disponível em:


http://localhost:8000


---

# Gateways de pagamento

Para testes, foram utilizados **mocks de gateways**.

Gateway 1


POST /login
POST /transactions
POST /transactions/:id/charge_back


Gateway 2


POST /transacoes
POST /transacoes/reembolso


O sistema tenta processar a compra respeitando a **prioridade dos gateways**.

Se o primeiro falhar, o segundo é utilizado automaticamente.

---

# Rotas principais da API

## Autenticação

### Login


POST /api/auth/login


---

# Produtos


GET /api/products
POST /api/products
PUT /api/products/{id}
DELETE /api/products/{id}


---

# Compras


POST /api/purchases
GET /api/transactions
GET /api/transactions/{id}


---

# Reembolso


POST /api/transactions/{id}/refund


---

# Clientes


GET /api/clients
GET /api/clients/{id}


---

# Gateways


GET /api/gateways
PATCH /api/gateways/{id}/toggle
PATCH /api/gateways/{id}/priority


---

# Exemplo de compra


POST /api/purchases


Body:

```json
{
  "client": {
    "name": "Tarcisia Luciano",
    "email": "tarcisia@gmail.com"
  },
  "products": [
    {
      "product_id": 2,
      "quantity": 1
    }
  ],
  "card": {
    "number": "5569000000006063",
    "cvv": "010"
  }
}