# Ailyn MS API

## Description

Ailyn is a NMS Multitenant SaaS plataform build with Laravel, Bref (Serverless) and Passport

## Requirements

-   Yarn (Install git hooks)
-   Docker/Docker-Compose (Run application)
-   PHP (Run precommit scripts)

## Docker Containers

-   web - Required to run the application
-   php - Required to run the application
-   mysql - Required to run the application
-   console - Optional to test console commands
-   app - Optional utility container with php and composer to develop and interact with the application

## Installation

### Setup git hooks (Required for development)

`yarn`

### Start containers

`docker-compose up -d`

### Install dependencies

With the utility container

`docker-compose exec app composer install`

With local installed composer

`composer install --ignore-platform-reqs --no-scripts`

### Migrate

Run tenant migrations

`docker-compose exec app php artisan migrate --path=database/migrations/landlord`

Run regular migrations

`docker-compose exec app php artisan migrate --seed`

### Install passport

`docker-compose exec app php artisan passport:install`

### Setup configurations

Copy the `.env.example` file into a new `.env` file and fill all the empty variables

#### Ignoring recaptcha

You can fill the `LOCAL_IP` var with your IP in the `env` file to bypass recaptcha validations and avoid troubles with local development

### All set

Start making request to `http://localhost:8080`

## Development

### Interact with the application

With artisan

`docker-compose exec app php artisan <command>`

With composer

`docker-compose exec app composer <command>`

## Open API Documentation

All endpoints are documented in the `open-api.yaml`

### Generate static HTML documentation with redoc

[Reference](https://www.npmjs.com/package/redoc-cli)

Install redoc-cli

`npm i redoc-cli -g`

Generate docs

`redoc-cli bundle open-api.yaml`

Open file

`redoc-static.html`
