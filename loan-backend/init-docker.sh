#!/bin/bash

# Script de inicialização rápida do projeto com Docker

set -e

RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m'

echo -e "${GREEN}╔════════════════════════════════════════╗${NC}"
echo -e "${GREEN}║   Setup Inicial - Loan System Docker   ║${NC}"
echo -e "${GREEN}╚════════════════════════════════════════╝${NC}"
echo ""

# Verificar Docker
if ! command -v docker &> /dev/null; then
    echo -e "${RED}❌ Docker não está instalado!${NC}"
    echo "Visite: https://docs.docker.com/get-docker/"
    exit 1
fi

if ! command -v docker-compose &> /dev/null; then
    echo -e "${RED}❌ Docker Compose não está instalado!${NC}"
    echo "Visite: https://docs.docker.com/compose/install/"
    exit 1
fi

echo -e "${GREEN}✓ Docker${NC} encontrado: $(docker --version)"
echo -e "${GREEN}✓ Docker Compose${NC} encontrado: $(docker-compose --version)"
echo ""

# Criar .env se não existir
if [ ! -f .env ]; then
    echo -e "${YELLOW}→ Criando arquivo .env...${NC}"
    cp .env.docker .env
    echo -e "${GREEN}✓ Arquivo .env criado${NC}"
else
    echo -e "${YELLOW}ℹ Arquivo .env já existe${NC}"
fi

echo ""
echo -e "${YELLOW}→ Construindo imagens Docker...${NC}"
docker-compose build

echo ""
echo -e "${YELLOW}→ Iniciando containers...${NC}"
docker-compose up -d

echo -e "${GREEN}✓ Containers iniciados${NC}"
echo ""

# Aguardar MySQL
echo -e "${YELLOW}→ Aguardando banco de dados ficar pronto...${NC}"
sleep 10

# Executar migrações
echo -e "${YELLOW}→ Executando migrações do banco de dados...${NC}"
docker-compose exec -T app php artisan migrate --force

echo -e "${GREEN}✓ Migrações executadas${NC}"
echo ""

# Gerar chave se necessário
if ! grep -q "^APP_KEY=base64:" .env || grep -q "^APP_KEY=$" .env; then
    echo -e "${YELLOW}→ Gerando chave da aplicação...${NC}"
    docker-compose exec -T app php artisan key:generate
    echo -e "${GREEN}✓ Chave gerada${NC}"
else
    echo -e "${YELLOW}ℹ Chave da aplicação já existe${NC}"
fi

echo ""
echo -e "${GREEN}╔════════════════════════════════════════╗${NC}"
echo -e "${GREEN}║        Setup Concluído com Sucesso!    ║${NC}"
echo -e "${GREEN}╚════════════════════════════════════════╝${NC}"
echo ""
echo -e "${GREEN}📍 Aplicação disponível em:${NC}"
echo "   • Frontend: http://localhost"
echo "   • API: http://localhost/api"
echo ""
echo -e "${GREEN}📝 Próximos Passos:${NC}"
echo "   1. Acesse http://localhost no navegador"
echo "   2. Para acessar o shell: ./docker.sh shell"
echo "   3. Para ver logs: ./docker.sh logs"
echo "   4. Para parar: ./docker.sh down"
echo ""
echo -e "${GREEN}💡 Dica:${NC} Consulte DOCKER.md para mais informações"
echo ""
