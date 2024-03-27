# Variables
SERVICE=app

# Build the Docker image
build:
	@docker-compose build

# Start the services defined in the docker-compose.yml file
up:
	@docker-compose up -d

# Stop the services
down:
	@docker-compose down

# Access the app service container's shell
app:
	@docker-compose exec -u root $(SERVICE) /bin/bash

# Install Composer dependencies
composer-install:
	@docker-compose exec -u root $(SERVICE) composer install

# Update Composer dependencies
composer-update:
	@docker-compose exec -u root $(SERVICE) composer update

# Run any arbitrary Composer command
# Usage: make composer CMD="require some/package"
composer:
	@docker-compose exec -u root $(SERVICE) composer $(CMD)

# Stop and remove all containers, networks, and volumes
clean:
	@docker-compose down -v

logs:
	@docker-compose logs -f
	
test:
	@docker-compose exec -u root $(SERVICE) ./vendor/bin/phpunit

phpstan:
	@docker-compose exec -u root $(SERVICE) ./vendor/bin/phpstan analyse -c phpstan.neon.dist

lint:
	@docker-compose exec -u root $(SERVICE) ./vendor/bin/phpcs
	
.PHONY: build up down app composer-install composer-update composer clean logs test lint
