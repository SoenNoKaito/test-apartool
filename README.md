# API TEST ON LARAVEL 10

## Table of Contents
- [Introduction](#introduction)
- [Requirements](#requirements)
- [Installation](#installation)
- [Database Setup](#database-setup)
- [API Endpoints](#api-endpoints)
- [Authentication](#authentication)
- [Extra Notes](#extra-notes)
- [License](#license)

## Introduction

This repository contains the API for managing buildings, property managers, and building owners. It includes features such as listing buildings with various filters, authenticating users, and disabling building owners along with their associated entities.

## Requirements

- PHP >= 8.0
- Composer
- Laravel 10.x
- MySQL or another compatible database system
- Laravel Passport for API authentication

## Installation

1. Clone the repository: git clone https://github.com/SoenNoKaito/test-apartool
2. Navigate to the project directory:
    ```bash
    cd test-apartool
    ```
   
3. Install the project dependencies:
    ```bash
    composer install
    ```
4. Copy the example environment file and make the required configuration adjustments:
    ```bash
    cp .env.example .env
    ```
   
5. Generate the application key:
    ```bash
    php artisan key:generate
    ```
   
6. Run the database migrations:
    ```bash
    php artisan migrate
    ```
   
7. Install Laravel Passport:
    ```bash
    php artisan passport:install
    ```

8. Seed the database with sample data:
    ```bash
    php artisan db:seed
    ```
   
9. Start the local development server:
    ```bash
    php artisan serve
    ```
   
9. The API will be available at http://localhost:8000.

## Database Setup

1. Set up your database credentials in the `.env` file.
2. Run the migrations:
    ```bash
    php artisan migrate
    ```
   
3. Seed the database with sample data:
    ```bash
    php artisan db:seed
    ```

4. The database will be populated with sample data for buildings, property managers, and building owners and a default user for authentication (email: test@test.com password: test).

## API Endpoints

The API includes the following endpoints:

- `POST /api/login`: Authenticate a user and return an access token. (A default user is created with the following credentials: email: test@test.com password: test)
- `GET /api/buildings/list`: List all buildings with optional filters for name, property manager, and building owner. (Need authentication)
- `PATH /api/building-owner/{id}/disable`: Disable a building owner and all associated entities (buildings, property managers). (Need authentication)

A postman collection is included in the repository for testing the API in the `postman` directory.

## Authentication

The API uses Laravel Passport for authentication. To authenticate a user, send a POST request to `/api/login` with the user's email and password. The API will return an access token that should be included in the `Authorization` header for all subsequent requests.

## Extra Notes

Tests are included in the `tests` directory. To run the tests, use the following command:
```bash
php artisan test
```

The phpunit.xml is prepared to work with sqlite in memory, so it is not necessary to have a database to run the tests. However,
if the test are going to be run with the production database, keep in mind that passport must be re-installed and the database must be seeded after the tests since the seeders are not set to upload passport clients.

```bash 
php artisan passport:install
php artisan migrate:refresh --seed
```

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
```



