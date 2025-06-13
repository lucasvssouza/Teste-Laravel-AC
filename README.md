# 💸 AC Banking System

Sistema de transferência bancária e gerenciamento de conta, construído com Laravel, MySQL e Docker. Ideal para testes de autenticação, movimentação entre contas e simulação de extratos e saldos.

---

## 🚀 Tecnologias Utilizadas

- **Laravel** 12+
- **PHP** 8.3 (via Docker)
- **MySQL** 8.0
- **Docker + Docker Compose**
- **Vite** (para assets front-end)
- **Blade** (dependendo da implementação do front)

---

## 📦 Instalação

### 1. Clonar o repositório

```bash
git clone https://github.com/seu-usuario/nome-do-repo.git
cd nome-do-repo
```

### 2. Copiar o arquivo .env

```bash
cp .env.example .env
```

### 3. Subir containers com Docker (WSL)

```wsl
alias sail='sh $([ -f sail ] && echo sail || echo vendor/bin/sail)'
sail up -d
```

## 🧪 Testes

### Você pode rodar os testes dentro do container

```bash
php artisan test
```

## 📂 Estrutura de Rotas

| Método   | Caminho                   | Controller            | Descrição                 |
| -------- | ------------------------- | --------------------- | ------------------------- |
| GET/POST | /login                    | AuthController        | Login de usuário          |
| GET/POST | /register                 | AuthController        | Cadastro de usuário       |
| GET      | /saldo/atual              | BankAccountController | Verificar saldo atual     |
| GET      | /extrato                  | BankAccountController | Ver extrato de transações |
| GET/POST | /deposito                 | DepositController     | Realizar depósito         |
| GET/POST | /transferencia            | TransactionController | Realizar transferência    |
| POST     | /transacoes/{id}/cancelar | TransactionController | Cancelar uma transação    |

## ⚙️ Variáveis .env principais
```env
APP_NAME="AC Bank"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=root
DB_PASSWORD=secret

APP_PORT=8000
VITE_PORT=5173
```
