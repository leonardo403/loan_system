# 🚀 Quick Start - Docker

## ⚡ Inicialização Rápida (5 minutos)

### Opção 1: Script Automático (recomendado)

```bash
./init-docker.sh
```

Isso faz tudo automaticamente:
- ✓ Verifica Docker
- ✓ Cria arquivo .env
- ✓ Constrói imagens
- ✓ Inicia containers
- ✓ Executa migrações

### Opção 2: Usando Makefile

```bash
make install
```

### Opção 3: Passo a Passo

```bash
# 1. Construir imagens
docker-compose build

# 2. Iniciar containers
docker-compose up -d

# 3. Aguardar MySQL estar pronto
sleep 10

# 4. Executar migrações
docker-compose exec app php artisan migrate --force
```

---

## 🌐 Acessar a Aplicação

Após iniciar, acesse:
- **Frontend**: http://localhost
- **API**: http://localhost/api

---

## 📋 Comandos Rápidos

### Com Script `docker.sh`
```bash
./docker.sh up       # Iniciar
./docker.sh down     # Parar
./docker.sh logs     # Ver logs
./docker.sh shell    # Acessar bash
./docker.sh migrate  # Executar migrações
./docker.sh reset    # Resetar banco
```

### Com Makefile
```bash
make up              # Iniciar
make down            # Parar
make logs            # Ver logs
make shell           # Acessar bash
make migrate         # Executar migrações
make reset           # Resetar banco
make help            # Ver todos os comandos
```

### Com Docker Compose Direto
```bash
docker-compose up -d                          # Iniciar
docker-compose down                           # Parar
docker-compose exec app php artisan migrate   # Migrações
docker-compose exec app bash                  # Bash
```

---

## 📚 Documentação Completa

Para mais detalhes, veja [DOCKER.md](DOCKER.md)

---

## ❓ Problemas Comuns

### Porta 80 já em uso
Edite `docker-compose.yml` e mude `80:80` para `8080:80` (use porta 8080)

### MySQL não respondendo
```bash
docker-compose logs mysql
```

### Reconstruir tudo do zero
```bash
./docker.sh reset
```

---

## 🎯 Próximos Passos

1. Execute o script de inicialização
2. Abra http://localhost no navegador
3. Use `make shell` para acessar a aplicação
4. Consulte DOCKER.md para mais informações

**Boa sorte! 🚀**
