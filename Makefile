all: install

current_dir = $(shell pwd)
DOCKER_COMPOSE=docker-compose
PHP = $(DOCKER_COMPOSE) run --rm php
JS = $(DOCKER_COMPOSE) run --rm js

include make/php.mk
include make/js.mk

install: build composer.install assets

update: composer.update assets

build: docker.pull docker.build

docker.pull:
	$(DOCKER_COMPOSE) pull

docker.build:
	$(DOCKER_COMPOSE) build

composer.install:
	$(PHP) composer install

composer.update:
	$(PHP) composer update
