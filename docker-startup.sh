#!/bin/bash
cp .env.example .env
docker compose up -d
docker compose ps
echo 'Waiting for containers to startup...'
sleep 10
docker exec -it app composer install
docker exec -it app php artisan key:generate
docker exec -it app php artisan migrate
docker exec -it app php artisan db:seed
docker exec -it app php artisan migrate --env=testing
docker exec -it app php ./vendor/phpunit/phpunit/phpunit --configuration ./phpunit.xml ./tests
