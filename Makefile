all: install

current_dir = $(shell pwd)
DOCKER_COMPOSE=docker compose
PHP = php
JS = $(DOCKER_COMPOSE) run --rm js

include make/php.mk
include make/js.mk

install: build assets

update: composer.update assets

build: docker.pull docker.build

start:
	$(DOCKER_COMPOSE) up

start.d:
	$(DOCKER_COMPOSE) up -d

stop:
	$(DOCKER_COMPOSE) stop

docker.pull:
	$(DOCKER_COMPOSE) pull

docker.build:
	$(DOCKER_COMPOSE) build

php:
	$(PHP) bash

composer.install:
	$(PHP) composer install

composer.update:
	$(PHP) composer update
