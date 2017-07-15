IMAGE_ID := "pldin601/musicloud"
CONTAINER_ID := "musicloud-service"
GIT_CURRENT_COMMIT := $(shell git rev-parse --verify HEAD)

install:
	composer install
	npm install

test:
	composer run phpunit

build:
	time docker build -t $(IMAGE_ID) --build-arg GIT_CURRENT_COMMIT=$(GIT_CURRENT_COMMIT) .

run:
	docker run --rm --env-file "$(CURDIR)/.env" --name $(CONTAINER_ID) -p 8080:8080 $(IMAGE_ID)

debug:
	docker run --rm -it --env-file "$(CURDIR)/.env" --name $(CONTAINER_ID) $(IMAGE_ID) bash

serve:
	docker run --rm -it --env-file "$(CURDIR)/.env" --name $(CONTAINER_ID) -p 80:6060 -v "$(CURDIR)":/usr/app/ $(IMAGE_ID)

deploy:
	git diff-index --quiet HEAD -- && docker push $(IMAGE_ID) || (echo 'You have uncommited changes.' && exit 1)

.PHONY: build test
