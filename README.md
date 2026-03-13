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
- CRUD de usuários
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

```text
app/
 ├── Http/Controllers → controle das rotas da API
 ├── Services → regras de negócio
 │     └── Payment → lógica de pagamento e orquestração
 │          └── Gateways → integração com APIs externas
 ├── Models → entidades do sistema
 ├── Http/Requests → validação de dados
 └── Http/Resources → formatação das respostas
```

A lógica de pagamento utiliza um **orquestrador de gateways**, que tenta processar a cobrança respeitando a prioridade definida.

Fluxo:

```text
API → PaymentOrchestrator → Gateway 1
↳ falha → Gateway 2
↳ sucesso → transação registrada
```

---

# Banco de Dados

## users

| campo | descrição |
|------|-----------|
id | identificador |
email | email do usuário |
password | senha |
role | perfil do usuário |

## Roles disponíveis:
```text
ADMIN
MANAGER
FINANCE
USER
```
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

Existem duas formas de executar a aplicação:

1. Execução local (PHP + MySQL)
2. Execução com Docker

---

# 1️⃣ Execução local

## Requisitos

- PHP 8.2+
- Composer
- MySQL

## Passos

### Clone o repositório:

```bash
git clone https://github.com/Tarcisia/betalent-api.git
cd betalent-api
```

### Instale as dependências:

composer install

### Configure o arquivo .env:

cp .env.example .env

### Configure as credenciais do banco no .env:

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=betalent
DB_USERNAME=root
DB_PASSWORD=SEU_PASSWORD

### Execute migrations e seeds:

php artisan migrate
php artisan db:seed

### Inicie o servidor:

php artisan serve

### A API estará disponível em:

http://127.0.0.1:8000


---

# 2️⃣ Execução com Docker

## Requisitos

Docker

Docker Compose

## Passos

### Configure o ambiente:

cp .env.example .env

### Suba os containers:

docker compose up --build

### Execute migrations e seeds:

docker exec -it betalant_api php artisan migrate
docker exec -it betalant_api php artisan db:seed

### A API estará disponível em:

http://localhost:8000


---

# Autenticação

A API utiliza Laravel Sanctum.

Após o login, um token será retornado e deverá ser enviado no header das requisições protegidas:

Authorization: Bearer {token}

---

# Rotas principais da API

## Login

POST /api/auth/login

## Usuários
GET /api/users
POST /api/users
PUT /api/users/{id}
DELETE /api/users/{id}

## Produtos
GET /api/products
POST /api/products
PUT /api/products/{id}
DELETE /api/products/{id}

## Compras
POST /api/purchases
GET /api/transactions
GET /api/transactions/{id}

## Reembolso
POST /api/transactions/{id}/refund

## Clientes
GET /api/clients
GET /api/clients/{id}

## Gateways
GET /api/gateways
PATCH /api/gateways/{id}/toggle
PATCH /api/gateways/{id}/priority

## Exemplo de compra
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
```

---

## Fluxo de fallback de gateways

 1. Busca gateways ativos ordenados por prioridade 
 2. Tenta gateway 1
 3. Se falhar → tenta gateway 2
 4. Se algum tiver sucesso → compra aprovada 
 5. Caso todos falhem → erro retornado
 
 ---

 # Testes de gateway 
 
 CVVs simulam cenários: 
 ```text
 | CVV | Resultado                            | 
 |-----|---------------------------------------
 | 010 | sucesso no gateway 1                 |
 | 100 | erro no gateway 1 → sucesso gateway 2|
 | 200 | erro em ambos gateways               | 
 ```

 ---

# Observações

O projeto possui configuração para execução utilizando Docker e Docker Compose.

Caso o ambiente local não possua Docker configurado, a aplicação também pode ser executada utilizando PHP e MySQL instalados diretamente na máquina.

Os gateways utilizados no teste são mocks disponibilizados no enunciado do desafio.

---

# Autor

Tarcisia Solange Dias Luciano
