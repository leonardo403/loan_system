# Dashboard Docker - Guia de Uso

Este guia explica como usar Docker e Docker Compose para desenvolver e rodar o backend do sistema de empréstimos.

## Pré-requisitos

- [Docker](https://docs.docker.com/get-docker/) instalado
- [Docker Compose](https://docs.docker.com/compose/install/) instalado
- Linux/macOS ou Windows (com WSL2 recomendado)

Verifique a instalação com:
```bash
docker --version
docker-compose --version
```

## Estrutura do Docker

O projeto utiliza 3 serviços:

1. **app** - PHP-FPM 8.4 (executa a aplicação Laravel)
2. **mysql** - MySQL 8.0 (banco de dados)
3. **nginx** - Nginx (servidor web)

## Usando o Script Helper

Para facilitar o gerenciamento, use o script `docker.sh`:

### Inicialização Rápida

```bash
# Construir as imagens Docker (primeira vez)
./docker.sh build

# Iniciar todos os containers
./docker.sh up
```

Isso irá:
- Construir as imagens
- Iniciar os containers
- Executar as migrações automaticamente
- Gerar a chave da aplicação

### Acessar a Aplicação

Após executar `./docker.sh up`, a aplicação estará disponível em:
- **Frontend**: http://localhost
- **API**: http://localhost/api

### Comandos Úteis do Script

```bash
# Ver logs em tempo real
./docker.sh logs

# Abrir shell do PHP (acesso ao container)
./docker.sh shell

# Executar migrações
./docker.sh migrate

# Popular banco com dados de teste
./docker.sh seed

# Abrir Laravel Tinker (console interativo)
./docker.sh tinker

# Limpar cache
./docker.sh clear

# Resetar banco de dados completamente
./docker.sh reset

# Parar os containers
./docker.sh down
```

## Usando Docker Compose Diretamente

Se preferir usar `docker-compose` diretamente:

```bash
# Construir imagens
docker-compose build

# Iniciar containers em background
docker-compose up -d

# Parar containers
docker-compose down

# Ver logs
docker-compose logs -f

# Executar comando no container PHP
docker-compose exec app php artisan migrate

# Abrir bash no container
docker-compose exec app bash
```

## Configuração de Variáveis de Ambiente

As variáveis de ambiente estão definidas em `docker-compose.yml`. Para customizar:

1. Crie um arquivo `.env` copiando de `.env.docker`:
```bash
cp .env.docker .env
```

2. Edite o arquivo `.env` com seus valores

3. Reinicie os containers:
```bash
./docker.sh down
./docker.sh up
```

### Variáveis Importantes

```
DB_CONNECTION=mysql
DB_HOST=mysql           # Nome do serviço MySQL no Docker
DB_PORT=3306
DB_DATABASE=loan_system
DB_USERNAME=loan_user
DB_PASSWORD=loan_password
```

## Volumes e Persistência de Dados

- **mysql_data**: Persiste dados do MySQL
- **./**: Monta o código do projeto dentro do container

Os dados persistem mesmo com `docker-compose down`.

Para deletar os dados:
```bash
docker-compose down -v
```

## Executar Testes

```bash
# Executar testes
./docker.sh shell
php artisan test

# Ou diretamente
docker-compose exec app php artisan test
```

## Troubleshooting

### Porta já em uso

Se a porta 80, 3306 ou 9000 já está em use, edite `docker-compose.yml`:

```yaml
services:
  nginx:
    ports:
      - "8080:80"    # Mude 8080 para outro número

  mysql:
    ports:
      - "3307:3306"  # Mude 3307 para outro número
```

### Banco de dados não está pronto

Se as migrações falham na primeira execução, aguarde alguns segundos e execute novamente:

```bash
./docker.sh migrate
```

### Verificar status dos containers

```bash
docker-compose ps
```

### Ver logs de erro

```bash
docker-compose logs app
docker-compose logs mysql
docker-compose logs nginx
```

### Limpar dados completamente

```bash
./docker.sh reset
```

## Desenvolvimento

### Instalar novas dependências PHP

```bash
docker-compose exec app composer require vendor/package
```

### Criar nova migração

```bash
docker-compose exec app php artisan make:migration create_table_name
```

### Criar novo Model

```bash
docker-compose exec app php artisan make:model ModelName -m
```

## Performance

Para melhor performance em desenvolvimento:

1. Use volumes montados localmente (já configurado)
2. Ajuste a cache se necessário em `.env`
3. Para produção, considere usar `.dockerignore` (já incluso)

## Próximos Passos

1. Execute `./docker.sh build` para construir as imagens
2. Execute `./docker.sh up` para iniciar tudo
3. Acesse http://localhost na sua navegador
4. Use `./docker.sh shell` para acessar a aplicação

Boa sorte com seu desenvolvimento! 🚀
