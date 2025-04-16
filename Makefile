USER := $(shell id -u):$(shell id -g)
PWD := $(shell pwd)

start-dev-dependencies:
	docker compose up -d

stop-dev-dependencies:
	docker compose stop

enter-dev-environment:
	docker build -t musicloud-dev --build-arg USER=$(USER) -f Dockerfile .
	mkdir -p .cache/volume/temp
	mkdir -p .cache/volume/media
	mkdir -p .cache/home
	docker run --rm -it --name musicloud-dev \
			--network musicloud \
			-p 127.0.0.1:8080:8080 \
			-v "$(PWD)":/code \
			-v "$(PWD)/.cache/volume/temp":/volume/temp \
			-v "$(PWD)/.cache/volume/media":/volume/media \
			-v "$(PWD)/.cache/home":/home \
			musicloud-dev bash

run-database-migration:
	docker build -t musicloud-migration -f docker/migration/Dockerfile .
	docker run --rm --name musicloud-migration \
			--network musicloud \
			--env POSTGRES_HOST=db \
			--env POSTGRES_DB=musicloud \
			--env POSTGRES_USER=musicloud \
			--env POSTGRES_PASSWORD=musicloud \
			musicloud-migration
