# Define the default target
.DEFAULT_GOAL := help

# Define colors for output
COLOR_RESET := \033[0m
COLOR_INFO := \033[32m
COLOR_COMMENT := \033[33m

# Help target (displays available targets)
help:
	@awk '/^## / \
        { if (c) {printf "$(COLOR_INFO)%s$(COLOR_RESET)\n", c}; c=substr($$0, 4); next } \
        c && /(^[[:alpha:]][[:alnum:]_-]+:)/ \
        	{ printf "  $(COLOR_COMMENT)%s$(COLOR_RESET) %s\n", $$1, c; c=0 }' $(MAKEFILE_LIST)

##
## Containers
##

## Build Docker Compose services with no cache
build:
	cd ./.docker && docker-compose --env-file ./../source/.env build --no-cache

## Start Docker Compose services, pull images and wait for them to be up
up:
	cd ./.docker && docker-compose --env-file ./../source/.env up -d

## Stop and remove Docker Compose services
down:
	cd ./.docker && docker-compose --env-file ./../source/.env down --remove-orphans

## Show logs
logs:
	docker-compose -f ./.docker/docker-compose.yaml logs

bash:
	docker exec -it php-fpm bash

##
## Composer
##

## Execute composer install
composer-install:
	docker exec php-fpm composer install

## Execute composer dump-autoload
composer-dump-autoload:
	docker exec php-fpm composer dump-autoload

##
## Migrations
##

## Migration status
migration-up:
	docker exec php-fpm php artisan migrate

## Migrate DB
migration-rollback:
	docker exec php-fpm php artisan migrate:rollback