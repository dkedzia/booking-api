# Booking API

## Overview

API allows to make room reservations in hotel.

In application are included:
* Basic auth
* Integration and unit tests
* Seeder
* Swagger documentation
* Postman collection

## Features

* index reservations
* show one reservation
* add reservation
* edit reservation
* delete reservation
* index taken vacancies

## Run application

To prepare docker containers, migrations and seeders automatically,
execute

> ./docker-startup.sh

In case of any errors, do it manually:

> cp .env.example .env
>
> docker compose up -d
> 
> docker exec -it app composer install
> 
> docker exec -it app php artisan key:generate
>
> docker exec -it app php artisan migrate
> 
> docker exec -it app php artisan db:seed
> 
> docker exec -it app php artisan migrate --env=testing


## Tests

To run unit tests in container, run:

> docker exec -it app php ./vendor/phpunit/phpunit/phpunit --configuration ./phpunit.xml ./tests

## Postman

Postman collection was generated via swagger api-docs.json file and exported to project. 
It can be directly imported to Postman from:

> BookingAPI.postman_collection.json

Enter base auth credentials from below into collection authenticate tab and setup your base url

## Basic auth credentials

> Login: test@example.com
> 
> Password: test

## Swagger

Swagger docs are available under

> base_url/api/documentation
