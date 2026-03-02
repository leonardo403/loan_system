#!/bin/bash

# Script para diagnosticar e corrigir problemas com o vendor

set -e

RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m'

echo -e "${GREEN}╔════════════════════════════════════════╗${NC}"
echo -e "${GREEN}║  Diagnosticando e Corrigindo Vendor    ║${NC}"
echo -e "${GREEN}╚════════════════════════════════════════╝${NC}"
echo ""

# Verificar se containers estão rodando
echo -e "${YELLOW}→ Verificando status dos containers...${NC}"
if ! docker-compose ps | grep -q "app"; then
    echo -e "${RED}❌ Container 'app' não está rodando!${NC}"
    echo "Inicie os containers com: docker-compose up -d"
    exit 1
fi

echo -e "${GREEN}✓ Containers estão rodando${NC}"
echo ""

# Verificar se vendor existe no container
echo -e "${YELLOW}→ Verificando se vendor existe...${NC}"
if docker-compose exec -T app test -d vendor; then
    echo -e "${GREEN}✓ Vendor já existe${NC}"
    
    # Verificar se autoload exists
    if docker-compose exec -T app test -f vendor/autoload.php; then
        echo -e "${GREEN}✓ Arquivo autoload.php existe${NC}"
        echo ""
        echo -e "${GREEN}Vendor parece estar OK!${NC}"
        exit 0
    else
        echo -e "${RED}❌ vendor/autoload.php não existe!${NC}"
    fi
else
    echo -e "${RED}❌ Diretório vendor não existe!${NC}"
fi

echo ""
echo -e "${YELLOW}→ Reinstalando dependências...${NC}"

# Limpar vendor se existir
docker-compose exec -T app sh -c 'rm -rf vendor' || true

# Instalar composer
if ! docker-compose exec -T app composer install \
    --no-dev \
    --no-interaction \
    --no-progress \
    --optimize-autoloader; then
    echo -e "${RED}❌ Erro ao instalar dependências com composer!${NC}"
    docker-compose logs app
    exit 1
fi

echo -e "${GREEN}✓ Dependências instaladas com sucesso!${NC}"
echo ""

# Verificar novamente
echo -e "${YELLOW}→ Verificando após instalação...${NC}"
if docker-compose exec -T app test -f vendor/autoload.php; then
    echo -e "${GREEN}✓ vendor/autoload.php foi criado com sucesso!${NC}"
    echo ""
    echo -e "${GREEN}╔════════════════════════════════════════╗${NC}"
    echo -e "${GREEN}║  Vendor Fixado com Sucesso!           ║${NC}"
    echo -e "${GREEN}╚════════════════════════════════════════╝${NC}"
else
    echo -e "${RED}❌ Falha ao criar vendor/autoload.php${NC}"
    docker-compose logs app | tail -30
    exit 1
fi
