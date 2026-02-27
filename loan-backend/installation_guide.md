# 🚀 Guia de Instalação - Sistema de Empréstimos (Laravel 12)

Sistema completo de análise de empréstimos com IA, backend Laravel 12 e banco de dados MySQL.

---

## 📋 Pré-requisitos

- PHP >= 8.2
- Composer
- MySQL >= 8.0 ou MariaDB
- Node.js >= 18 (para o frontend)
- API Key da Anthropic (Claude)
- Git (opcional)

---

## 🔧 Instalação do Backend (Laravel 12)

### 1. Criar o Projeto Laravel

```bash
# Criar novo projeto Laravel 12
composer create-project laravel/laravel loan-system
cd loan-system

# Verificar versão do Laravel
php artisan --version
# Deve mostrar: Laravel Framework 12.x.x
```

### 2. Configurar Banco de Dados

Crie o banco de dados MySQL:

```sql
CREATE DATABASE loan_system CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

Edite o arquivo `.env`:

```env
APP_NAME="Loan System"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_TIMEZONE=America/Sao_Paulo
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=loan_system
DB_USERNAME=root
DB_PASSWORD=sua_senha

ANTHROPIC_API_KEY=sk-ant-your-api-key-here

CACHE_DRIVER=file
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
```

Gere a chave da aplicação:

```bash
php artisan key:generate
```

### 3. Criar Estrutura de Arquivos

#### a) Criar Models

```bash
php artisan make:model Client -m
php artisan make:model LoanApplication -m
```

#### b) Criar Controllers

```bash
php artisan make:controller Api/LoanController
```

#### c) Criar Form Request

```bash
php artisan make:request LoanApplicationRequest
```

#### d) Criar Service

```bash
mkdir -p app/Services
touch app/Services/LoanAnalysisService.php
```

#### e) Criar Middleware CORS

```bash
php artisan make:middleware Cors
```

Cole os códigos do artifact backend nos respectivos arquivos.

### 4. Configurar Services

Adicione no arquivo `config/services.php`:

```php
'anthropic' => [
    'api_key' => env('ANTHROPIC_API_KEY'),
],
```

### 5. Registrar Middleware CORS

Edite `bootstrap/app.php`:

```php
<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->api(prepend: [
            \App\Http\Middleware\Cors::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
```

### 6. Executar Migrations

```bash
# Executar migrations
php artisan migrate

# Ou resetar e executar novamente
php artisan migrate:fresh
```

### 7. (Opcional) Popular Banco de Dados

```bash
# Criar factories
php artisan make:factory ClientFactory
php artisan make:factory LoanApplicationFactory

# Popular com dados de teste
php artisan db:seed
```

### 8. Iniciar o Servidor

```bash
# Iniciar servidor Laravel
php artisan serve

# O servidor rodará em http://localhost:8000
# A API estará em http://localhost:8000/api/loans
```

---

## 🎨 Instalação do Frontend (React)

### Atualizar a URL da API

No artifact do frontend React, atualize a constante:

```javascript
const API_BASE_URL = 'http://localhost:8000/api/loans';
```

### Opção 1: Usar no Claude.ai

1. O artifact já está configurado
2. Apenas certifique-se de que o backend está rodando

### Opção 2: Projeto React Standalone

```bash
# Criar projeto React
npx create-react-app loan-frontend
cd loan-frontend

# Instalar dependências
npm install lucide-react

# Copiar o componente para src/App.js
# Atualizar API_BASE_URL para http://localhost:8000/api/loans

# Iniciar
npm start
```

---

## 🧪 Testando a API

### 1. Teste com cURL

```bash
# Analisar empréstimo
curl -X POST http://localhost:8000/api/loans/analyze \
  -H "Content-Type: application/json" \
  -d '{
    "name": "João Silva",
    "income": 5000,
    "age": 35,
    "employment": "CLT",
    "credit_history": "Bom",
    "requested_amount": 50000,
    "purpose": "Reforma da casa",
    "email": "joao@example.com",
    "phone": "11999999999"
  }'

# Listar aplicações
curl http://localhost:8000/api/loans/applications

# Ver aplicação específica
curl http://localhost:8000/api/loans/applications/1

# Estatísticas
curl http://localhost:8000/api/loans/statistics
```

### 2. Teste com Artisan Tinker

```bash
php artisan tinker
```

```php
// Criar um cliente
$client = \App\Models\Client::create([
    'name' => 'Maria Santos',
    'income' => 8000,
    'age' => 42,
    'employment' => 'CLT',
    'credit_history' => 'Excelente',
    'email' => 'maria@example.com',
    'phone' => '11988888888'
]);

// Criar uma aplicação
$loan = \App\Models\LoanApplication::create([
    'client_id' => $client->id,
    'requested_amount' => 100000,
    'purpose' => 'Compra de veículo',
    'status' => 'pending'
]);

// Listar todas as aplicações
\App\Models\LoanApplication::with('client')->get();
```

---

## 📁 Estrutura de Diretórios

```
loan-system/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   └── Api/
│   │   │       └── LoanController.php
│   │   ├── Middleware/
│   │   │   └── Cors.php
│   │   └── Requests/
│   │       └── LoanApplicationRequest.php
│   ├── Models/
│   │   ├── Client.php
│   │   └── LoanApplication.php
│   └── Services/
│       └── LoanAnalysisService.php
├── bootstrap/
│   └── app.php
├── config/
│   └── services.php
├── database/
│   ├── factories/
│   │   ├── ClientFactory.php
│   │   └── LoanApplicationFactory.php
│   ├── migrations/
│   │   ├── xxxx_create_clients_table.php
│   │   └── xxxx_create_loan_applications_table.php
│   └── seeders/
│       └── DatabaseSeeder.php
├── routes/
│   └── api.php
├── .env
└── composer.json
```

---

## 🔌 Endpoints da API

### POST `/api/loans/analyze`
Analisa uma solicitação de empréstimo com IA

**Request Body:**
```json
{
  "name": "string",
  "income": "number",
  "age": "number (opcional)",
  "employment": "string (opcional)",
  "credit_history": "string (opcional)",
  "requested_amount": "number",
  "purpose": "string (opcional)",
  "email": "string (opcional)",
  "phone": "string (opcional)"
}
```

**Response:**
```json
{
  "success": true,
  "message": "Análise concluída com sucesso",
  "data": {
    "application_id": 1,
    "client_id": 1,
    "analysis": {
      "scoreCredito": 750,
      "riscoCategoria": "baixo",
      "aprovacao": "aprovado",
      "taxaJurosSugerida": 1.5,
      "valorAprovado": 50000,
      "prazoMaximo": 48,
      "justificativa": "...",
      "recomendacoes": [],
      "condicoes": []
    }
  }
}
```

### GET `/api/loans/applications`
Lista todas as aplicações (com paginação)

**Query Params:**
- `per_page`: número de itens por página (padrão: 15)
- `status`: filtrar por status

### GET `/api/loans/applications/{id}`
Detalhes de uma aplicação específica

### GET `/api/loans/clients/{clientId}/applications`
Aplicações de um cliente específico

### GET `/api/loans/statistics`
Estatísticas gerais do sistema

---

## 🔑 Obter API Key da Anthropic

1. Acesse https://console.anthropic.com
2. Faça login ou crie uma conta
3. Vá em "API Keys"
4. Clique em "Create Key"
5. Copie a chave (começa com `sk-ant-`)
6. Cole no arquivo `.env`:
   ```
   ANTHROPIC_API_KEY=sk-ant-api03-sua-chave-aqui
   ```

---

## 🐛 Troubleshooting

### Erro "SQLSTATE[HY000] [2002] Connection refused"

```bash
# Verificar se MySQL está rodando
sudo systemctl status mysql

# Iniciar MySQL
sudo systemctl start mysql

# Verificar credenciais no .env
```

### Erro "Class 'LoanAnalysisService' not found"

```bash
# Recompilar autoload
composer dump-autoload

# Limpar cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear
```

### CORS Error no Frontend

- Verifique se o middleware `Cors` está registrado em `bootstrap/app.php`
- Limpe o cache de configuração: `php artisan config:clear`
- Reinicie o servidor: `php artisan serve`

### Erro 500 na API

```bash
# Ver logs detalhados
tail -f storage/logs/laravel.log

# Verificar permissões
chmod -R 775 storage bootstrap/cache
```

### API Key Inválida

- Verifique se está no formato correto: `sk-ant-api03-...`
- Confirme que a chave está ativa no console da Anthropic
- Verifique se há saldo/créditos disponíveis
- Limpe o cache: `php artisan config:clear`

---

## 📊 Comandos Úteis Laravel

```bash
# Ver todas as rotas
php artisan route:list

# Criar migration
php artisan make:migration create_table_name

# Rodar migrations
php artisan migrate

# Resetar banco de dados
php artisan migrate:fresh --seed

# Criar controller
php artisan make:controller NomeController

# Criar model
php artisan make:model NomeModel -m

# Limpar todos os caches
php artisan optimize:clear

# Modo de manutenção
php artisan down
php artisan up
```

---

## 🚀 Deploy em Produção

### 1. Otimizar Laravel

```bash
# Otimizar autoload
composer install --optimize-autoload --no-dev

# Cachear configurações
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Otimização geral
php artisan optimize
```

### 2. Configurar Servidor Web (Nginx)

```nginx
server {
    listen 80;
    server_name seu-dominio.com;
    root /var/www/loan-system/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

### 3. Configurar Variáveis de Ambiente

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://seu-dominio.com

# Use senhas fortes em produção
DB_PASSWORD=senha-muito-forte-aqui
```

### 4. Configurar SSL (Let's Encrypt)

```bash
sudo apt install certbot python3-certbot-nginx
sudo certbot --nginx -d seu-dominio.com
```

---

## 📝 Recursos Adicionais

### Logs e Monitoramento

```bash
# Ver logs em tempo real
tail -f storage/logs/laravel.log

# Logs de query SQL
# Adicionar em AppServiceProvider.php:
DB::listen(function($query) {
    Log::info($query->sql);
});
```

### Testes Automatizados

```bash
# Criar teste
php artisan make:test LoanControllerTest

# Executar testes
php artisan test
```

### Queue para Processamento Assíncrono

Se quiser processar análises em background:

```bash
# Configurar no .env
QUEUE_CONNECTION=database

# Criar tabela de jobs
php artisan queue:table
php artisan migrate

# Executar worker
php artisan queue:work
```

---

## 📚 Documentação

- Laravel 12: https://laravel.com/docs/12.x
- Anthropic API: https://docs.anthropic.com
- MySQL: https://dev.mysql.com/doc/

---

## ✅ Checklist de Instalação

- [ ] PHP 8.2+ instalado
- [ ] Composer instalado
- [ ] MySQL rodando
- [ ] Projeto Laravel criado
- [ ] Banco de dados configurado
- [ ] Migrations executadas
- [ ] API Key da Anthropic configurada
- [ ] CORS configurado
- [ ] Servidor Laravel rodando
- [ ] Frontend conectado à API
- [ ] Primeiro teste realizado com sucesso

---

**Pronto!** Seu sistema de análise de crédito com Laravel 12 está funcionando! 🎉

Para suporte: verifique os logs em `storage/logs/laravel.log`