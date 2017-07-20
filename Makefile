IMAGE_ID := "pldin601/musicloud-service"

local-install:
	composer install
	npm install

local-clean:
	npm run rimraf -- vendor/ node_modules/

local-build:
	npm run gulp
	npm run webpack

local-test:
	composer test
	npm test

local-migrate:
	composer run migrate:up env

docker-build:
	docker-compose build

docker-up:
	docker-compose up -d

docker-stop:
	docker-compose stop

docker-down:
	docker-compose down

docker-logs:
	docker-compose logs -f web

docker-bash:
	docker-compose exec web bash

docker-deploy:
	docker build -t $(IMAGE_ID) . && docker push $(IMAGE_ID)
