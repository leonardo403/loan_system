.PHONY: help build up down logs shell migrate seed tinker clear reset

help: ## Mostrar ajuda
	@echo "Loan System Docker - Comandos Disponíveis"
	@echo "=========================================="
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-15s\033[0m %s\n", $$1, $$2}'

build: ## Construir imagens Docker
	docker-compose build

up: ## Iniciar containers (inclui migrações)
	docker-compose up -d
	sleep 10
	docker-compose exec -T app php artisan migrate --force
	@echo "✓ Aplicação disponível em http://localhost"

down: ## Parar containers
	docker-compose down

logs: ## Ver logs em tempo real
	docker-compose logs -f

shell: ## Abrir bash no container PHP
	docker-compose exec app bash

migrate: ## Executar migrações
	docker-compose exec app php artisan migrate

seed: ## Executar seeders
	docker-compose exec app php artisan db:seed

tinker: ## Abrir Laravel Tinker
	docker-compose exec app php artisan tinker

clear: ## Limpar cache
	docker-compose exec app php artisan cache:clear
	docker-compose exec app php artisan config:clear
	docker-compose exec app php artisan route:clear
	docker-compose exec app php artisan view:clear
	@echo "✓ Cache limpo"

reset: ## Resetar banco de dados (CUIDADO: deleta dados!)
	docker-compose down -v
	docker-compose up -d
	sleep 10
	docker-compose exec -T app php artisan migrate --force --seed
	@echo "✓ Banco resetado"

test: ## Executar testes
	docker-compose exec app php artisan test

ps: ## Ver status dos containers
	docker-compose ps

fresh: ## Reinstalar dependências
	docker-compose exec app composer install
	docker-compose exec app php artisan migrate:fresh --seed

install: ## Instalação completa (build + up)
	@echo "Instalando Loan System Docker..."
	@make build
	@make up
	@echo "✓ Instalação concluída!"
