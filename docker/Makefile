init: docker-compose-override-init \
	docker-build \
	composer-install \
	docker-compose-up \
	doctrine-migrations
	$(call print_info,Initialization has been successfully completed)
	$(call print_info,The application runs at http://localhost:8080)
	$(call print_info,See README.md for more information.)

dummy-data:
	$(call print_info,Loading data fixtures)
	docker-compose run --rm admin-php bin/console doctrine:fixtures:load --no-interaction --purge-exclusions=setting

composer-install:
	$(call print_info,Running app composer install)
	docker-compose run --rm warehouse-php composer install

docker-compose-override-init:
	$(call print_info,Copying dist of docker-compose.override)
	cp -n docker-compose.override.yml.dist docker-compose.override.yml

docker-build:
	$(call print_info,Building docker containers)
	docker-compose build

docker-compose-up:
	$(call print_info,Starting up docker containers)
	docker-compose up -d

doctrine-migrations:
	docker-compose run --rm warehouse-php bin/console doctrine:migrations:migrate --no-interaction --allow-no-migration

# code quality
ecs:
	docker-compose run --rm warehouse-php /bin/bash -c './vendor/bin/ecs check --config ecs.php'
ecs-fix:
	docker-compose run --rm warehouse-php /bin/bash -c './vendor/bin/ecs check --config ecs.php --fix'

phpstan:
	docker-compose run --rm warehouse-php /bin/bash -c 'php ./vendor/bin/phpstan analyse -c phpstan.neon --memory-limit=-1'

#tests
test-all: test-clear-cache test-unit test-func test-int

test-clear-cache:
	docker-compose run --rm warehouse-php /bin/bash -c 'bin/console cache:clear --env=test'
test-unit:
	docker-compose run --rm warehouse-php /bin/bash -c './bin/phpunit --testsuite=unit'

test-func:
	docker-compose run --rm warehouse-php /bin/bash -c './bin/phpunit-db-wrapper --testsuite=functional'

test-int:
	docker-compose run --rm warehouse-php /bin/bash -c './bin/phpunit-db-wrapper --testsuite=integration'




