dev: docker-build docker-up

local-install:
	composer install
	npm install

local-install-missing:
	composer install
	npm-install-missing

local-clean:
	npm run rimraf -- vendor/ node_modules/ publuc/css/ public/scripts/

local-build:
	npm run gulp
	npm run webpack

local-test:
	composer test
	npm test

local-migrate:
	composer run migrate:up env

local-watch:
	npm run webpack -- --watch

docker-build:
	docker-compose build

docker-up:
	docker-compose up

docker-bash:
	docker-compose exec web bash
