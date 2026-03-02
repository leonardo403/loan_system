#!/bin/bash

set -e

# Cores para output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Funções
function print_info() {
    echo -e "${GREEN}[INFO]${NC} $1"
}

function print_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

function print_warning() {
    echo -e "${YELLOW}[WARN]${NC} $1"
}

# Verificar comando
case "$1" in
    build)
        print_info "Construindo imagens Docker..."
        docker-compose build
        print_info "Imagens construídas com sucesso!"
        ;;
    
    up)
        print_info "Iniciando containers..."
        docker-compose up -d
        print_info "Aguardando container MySQL ficar pronto..."
        sleep 10
        print_info "Executando migrações..."
        docker-compose exec -T app php artisan migrate --force
        print_info "Gerando chave da aplicação..."
        docker-compose exec -T app php artisan key:generate
        print_info "Containers iniciados com sucesso!"
        print_info "Backend disponível em: http://localhost"
        ;;
    
    down)
        print_info "Parando containers..."
        docker-compose down
        print_info "Containers parados!"
        ;;
    
    logs)
        print_info "Mostrando logs..."
        docker-compose logs -f
        ;;
    
    shell)
        print_info "Abrindo shell no container PHP..."
        docker-compose exec app bash
        ;;
    
    migrate)
        print_info "Executando migrações..."
        docker-compose exec app php artisan migrate
        ;;
    
    seed)
        print_info "Executando seeders..."
        docker-compose exec app php artisan db:seed
        ;;
    
    tinker)
        print_info "Abrindo Tinker..."
        docker-compose exec app php artisan tinker
        ;;
    
    clear)
        print_info "Limpando cache..."
        docker-compose exec app php artisan cache:clear
        docker-compose exec app php artisan config:clear
        docker-compose exec app php artisan route:clear
        docker-compose exec app php artisan view:clear
        print_info "Cache limpo!"
        ;;
    
    reset)
        print_warning "Isso vai deletar o banco de dados e recriá-lo!"
        read -p "Tem certeza? (s/n) " -n 1 -r
        echo
        if [[ $REPLY =~ ^[Ss]$ ]]; then
            print_info "Deletando e recriando banco..."
            docker-compose down -v
            docker-compose up -d
            sleep 10
            docker-compose exec -T app php artisan migrate --force --seed
            print_info "Banco resetado com sucesso!"
        else
            print_info "Operação cancelada."
        fi
        ;;
    
    *)
        echo "Uso: $0 {build|up|down|logs|shell|migrate|seed|tinker|clear|reset}"
        echo ""
        echo "Comandos disponíveis:"
        echo "  build   - Construir imagens Docker"
        echo "  up      - Iniciar containers"
        echo "  down    - Parar containers"
        echo "  logs    - Ver logs em tempo real"
        echo "  shell   - Abrir shell no container PHP"
        echo "  migrate - Executar migrações"
        echo "  seed    - Executar seeders"
        echo "  tinker  - Abrir Laravel Tinker"
        echo "  clear   - Limpar cache"
        echo "  reset   - Resetar banco de dados"
        exit 1
        ;;
esac
