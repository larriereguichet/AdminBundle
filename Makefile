all: install

.PHONY: tests install update assets@build security@check phpunit@run

current_dir = $(shell pwd)

include .make/assets.mk
include .make/tests.mk

install:
	composer install
	make assets

update:
	composer update
	make assets

assets@build:
	php sam.php
