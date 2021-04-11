.DEFAULT_GOAL := restart

init: docker-down-clear \
	  frontend-clear \
	  docker-pull docker-build docker-up \
	  backend-init frontend-init
up: docker-up
down: docker-down
restart: down up

docker-up:
	docker-compose up -d

docker-down:
	docker-compose down --remove-orphans

docker-down-clear:
	docker-compose down -v --remove-orphans

docker-pull:
	docker-compose pull

docker-build:
	docker-compose build

wait-db:
	docker-compose run --rm php-cli wait-for-it db:3306 -t 30

backend-permissions:
	docker run --rm -v ${PWD}:/app -w /app alpine chmod 777 -R storage bootstrap

backend-composer-install:
	docker-compose run --rm php-cli composer install

backend-init: backend-permissions backend-composer-install backend-copy-env backend-generate-key wait-db backend-migrations backend-create-storage

backend-copy-env:
	cp .env.example .env

backend-create-storage:
	docker-compose run --rm php-cli php artisan storage:link

backend-test:
	docker-compose run --rm php-cli php artisan test

backend-shell:
	docker-compose run --rm php-cli bash

backend-migrations:
	docker-compose run --rm php-cli php artisan migrate

backend-seed:
	docker-compose run --rm php-cli php artisan migrate:fresh --seed

backend-generate-key:
	docker-compose run --rm php-cli php artisan key:generate

backend-php-codesniffer:
	docker-compose run --rm php-cli composer phpcs

backend-php-codesniffer-fix:
	docker-compose run --rm php-cli composer phpcbf

backend-phpstan:
	docker-compose run --rm php-cli composer phpstan

backend-lint: backend-php-codesniffer backend-phpstan

backend-ide-helper-generate:
	docker-compose run --rm php-cli composer ide-helper-generate

backend-ide-helper-models:
	docker-compose run --rm php-cli composer ide-helper-models

backend-ide-helper-meta:
	docker-compose run --rm php-cli composer ide-helper-meta

backend-ide-helper: backend-ide-helper-generate backend-ide-helper-models backend-ide-helper-meta

frontend-clear:
	docker run --rm -v ${PWD}:/app -w /app alpine sh -c 'rm -rf .ready'

frontend-init: frontend-npm-install frontend-ready

frontend-npm-install:
	docker-compose run --rm node-cli npm install

frontend-ready:
	docker run --rm -v ${PWD}:/app -w /app alpine touch .ready
