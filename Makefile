env ?= local

DOCKERCOMPOSE=docker/docker-compose.yml
DOCKER_APP = paygreen_api-$(env)

#include .env.docker.$(env)

up:
	docker-compose -f $(DOCKERCOMPOSE) -p $(DOCKER_APP) up -d

build:
	docker-compose -f $(DOCKERCOMPOSE) -p $(DOCKER_APP) build

stop:
	docker-compose -f $(DOCKERCOMPOSE) -p $(DOCKER_APP) stop

php:
	docker-compose -f $(DOCKERCOMPOSE) -p $(DOCKER_APP) exec php bash

clean:
	make stop
	docker rm $(shell sudo docker ps -a -q)

logs:
	docker-compose -f $(DOCKERCOMPOSE) -p $(DOCKER_APP) logs -f