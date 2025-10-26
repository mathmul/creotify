
# Creotify

Creatim Backend Assignment


## Latest test results

```bash
➜  creotify git:(main) ✗ composer test

   DEPR  Tests\Unit\AppFixturesTest
  ! it executes AppFixtures load() successfully → Class "Doctrine\ORM\Proxy\Autoloader" is deprecated. Use native lazy objects instead. (Auto… 0.19s

   PASS  Tests\Unit\ExampleTest
  ✓ example                                                                                                                                    0.01s

   PASS  Tests\Feature\GetApiHealthTest
  ✓ it returns 200 OK for /api/health                                                                                                          0.04s

  Tests:    1 deprecated, 2 passed (3 assertions)
  Duration: 0.30s

  Controller/Api/HealthController ........................................................................................................... 100.0%
  Kernel .................................................................................................................................... 100.0%
  ──────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────
                                                                                                                                      Total: 100.0 %
```

## Stack

- Symfony 7.3.4
- PHP 8.4
- Pest v4
- Ray
- PostgreSQL (local via Docker)


## TODO

### Setup

- [x] Create new Symfony project via `symfony new creotify --webapp`
- [x] Configure project with Laravel Herd & HTTPS (https://creotify.test)
- [ ] Add Docker setup (PHP-FPM + Nginx + MySQL + Mailpit)
- [ ] Extend Dockerfile with Xdebug + Ray pre-configuration
- [ ] Update `.env` with Postgres credentials
- [x] Require optional dev dependencies:
    ```bash
    composer require pestphp/pest --dev --with-all-dependencies
    composer require --dev doctrine/doctrine-fixtures-bundle fakerphp/faker friendsofphp/php-cs-fixer
    composer require spatie/ray
    ```
- [x] Configure CS Fixer
- [x] Initialize Pest:
    ```bash
    ./vendor/bin/pest --init
    ```
- [x] Add `"test": "XDEBUG_MODE=coverage ./vendor/bin/pest --coverage --min=100"` to composer.json scripts
- [x] Add `"format": "./vendor/bin/php-cs-fixer fix src --verbose"` to composer.json scripts
- [x] Ensure all tests pass with `composer test` and 100% coverage


### Domain Modeling

- [x] Create entities:
  - [x] `Article`
  - [x] `SubscriptionPackage`
  - [x] `Customer`
  - [x] `Order`
  - [x] `OrderItem`
- [x] Generate and run Doctrine migrations
- [ ] Add Fixtures for sample data using Faker

### Business Logic

- [ ] Implement validation for unique item/subscription per customer
- [ ] Implement order total calculation
- [ ] Implement error handling (custom exceptions)
- [ ] Implement SMS service management (two services, 5 SMS/min limit)
- [ ] Implement fallback strategy between SMS providers
- [ ] Log SMS events (Spatie Ray / Monolog)

### Controllers & Forms

- [ ] CRUD for `Article`
- [ ] CRUD for `SubscriptionPackage`
- [ ] Order endpoints:
  - [ ] Create order (purchase flow)
  - [ ] List orders
  - [ ] Cancel order (handle deletion logic)
- [ ] Implement Symfony Forms for validation and clean DTO mapping

### Testing (TDD with Pest)

- [x] Write smoke tests for:
  - [x] Kernel
  - [x] Health endpoint
  - [x] All public routes
- [ ] Write unit tests for:
  - [ ] OrderService (purchase logic)
  - [ ] SMSService (fallback logic)
- [ ] Write functional tests for:
  - [ ] Order creation endpoint
  - [ ] Duplicate purchase restriction
- [ ] Enforce 100% coverage via composer script
- [ ] Configure Ray to show test output (`ray()->showQueries()->showEvents();`)

### Dockerization

- [ ] Add `Dockerfile` + `docker-compose.yml`
- [ ] Include PHP 8.4, Composer, MySQL 8, Nginx
- [ ] Configure for local HTTPS (creotify.test)

### Documentation

- [ ] Add setup instructions to README
- [ ] Add Postman/cURL examples for all API endpoints
- [ ] Document SMS Service usage (interface + example)
- [ ] Add note on test execution (`composer test`)

### Polishing

- [ ] Ensure code comments in complex logic
- [ ] Apply DRY and MVC clean-up
- [ ] Format code via PHP-CS-Fixer
- [ ] Commit history clean and semantic


## Getting started

We support two local setups - pick one (don't run both on the same ports):

Herd (macOS, Windows) - uses Docker only for Postgres
Docker (Linux, macOS, Windows) - Nginx + PHP-FPM + Postgres in containers

### Herd

Prerequisites:

- Laravel Herd (acts as local PHP server with HTTPS)
- Parent folder of project root is added to Herd Paths (in Herd settings)
- Docker Desktop 4.x (only for Postgres)

Quick start:

```bash
# 1) Clone
git clone https://github.com/mathmul/creotify.git && cd creotify

# 2) Create .env.local and copy DATABASE_URL for Herd from .env
touch .env.local

# 3) Install dependencies
herd composer install

# 4) Start postgres docker
docker compose --profile dbonly up -d
# To stop: docker compose --profile dbonly down

# 5) Create the database
php bin/console doctrine:database:create

# 6) Run migrations
php bin/console doctrine:migrations:migrate

# 7) Load fixtures
php bin/console doctrine:fixtures:load

# 8) Serve via Herd (as <folder-name>.test)
herd init

# 9) Enable HTTPS
herd secure
# To disable HTTPS: herd unsecure

# 10) (Optional) Health + tests
curl https://creotify.test/api/health && echo
./vendor/bin/pest
# or with coverage
XDEBUG_MODE=coverage ./vendor/bin/pest --coverage --min=100
```

### Docker

Prerequisites

- Docker Desktop 4.x
- Ports 8080 (web) and 5432 (Postgres) free

Quick start:

```bash
# 1) Clone
git clone git@github.com:mathmul/creotify.git && cd creotify

# 2) (Optional) Create .env.local for overrides
touch .env.local

# 3) Start full docker stack
docker compose --profile full up -d
# To stop: docker compose --profile full down

# 4) (Optional) Check logs
docker compose logs -f app

# 5) Run migrations
docker compose exec app php bin/console doctrine:migrations:migrate

# 6) Load fixtures
docker compose exec app php bin/console doctrine:fixtures:load

# 7) (Optional) Health + tests
curl http://localhost:8080/api/health && echo
docker compose exec app vendor/bin/pest
# or with coverage
docker compose exec app XDEBUG_MODE=coverage vendor/bin/pest --coverage --min=100
```


## Development

### TDD

We follow a Test-Driven Development (TDD) approach:

1. Write a feature test
2. Run the test and verify it fails (red)
3. Implement the minimal code to pass
4. Run the test and verify it passes (green)
5. Refactor if needed
6. Repeat

#### Pest

We use **Pest** instead of PHPUnit for cleaner, expressive tests.
No Symfony plugin is required — Symfony’s native test classes (`WebTestCase`, `KernelTestCase`) work perfectly with Pest.

Symfony provides two main testing layers:

| Type | Base class | Purpose |
|------|-------------|----------|
| **Unit tests** | `KernelTestCase` | Tests that use the service container or Doctrine |
| **Functional tests** | `WebTestCase` | Simulate HTTP requests using Symfony’s `HttpKernelBrowser` |

We configured Pest to use these automatically in `tests/Pest.php`:

```php
<?php

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

uses(KernelTestCase::class)->in('Unit');
uses(WebTestCase::class)->in('Feature');
```

We initialized Pest:

```bash
./vendor/bin/pest --init
```

You can create new tests using the built-in Symfony Maker command or simply create files manually under `tests/`:

```bash
php bin/console make:test <TestName>          # Functional test
php bin/console make:test <TestName> --unit   # Unit test
```

Initialize additional Pest structures:

```bash
./vendor/bin/pest --dataset <DatasetName>
```

Example test:

```php
use Symfony\Bundle\FrameworkBundle\KernelBrowser;

it('returns 200 OK for /api/health', function () {
    /** @var KernelBrowser $client */
    $client = static::createClient();
    $client->request('GET', '/api/health');

    expect($client->getResponse()->getStatusCode())->toBe(200);
    expect($client->getResponse()->getContent())->toBeJson();
});
```

Run tests:

```bash
./vendor/bin/pest
# or
docker compose exec app ./vendor/bin/pest

# With coverage
XDEBUG_MODE=coverage ./vendor/bin/pest --coverage --min=100
# or
docker compose exec app XDEBUG_MODE=coverage ./vendor/bin/pest --coverage --min=100
```

#### Code Style (PHP-CS-Fixer)

We use **PHP-CS-Fixer** for automatic code formatting following the official Symfony and PSR-12 standards.
It was already installed as a dev dependency with:

```bash
composer require --dev friendsofphp/php-cs-fixer
```

But if dependencies conflict, you can use the shim instead:

```bash
composer require --dev php-cs-fixer/shim
```

Configuration file: `.php-cs-fixer.dist.php`

You can lint and auto-fix the code manually:

```bash
php vendor/bin/php-cs-fixer fix --verbose
# or for a dry run (check only):
php vendor/bin/php-cs-fixer fix --dry-run --diff
```

Add a Composer script for convenience:

```json
    "scripts": {
        "lint": "php vendor/bin/php-cs-fixer fix --dry-run --diff",
        "format": "php vendor/bin/php-cs-fixer fix --verbose"
    }
```

Run via Composer:

```bash
composer lint     # checks style
composer format   # fixes style issues
```

#### Database Models

Make sure docker container is running - see [Getting started](#getting-started).

After creating/updating models, create new migration files:
```bash
php bin/console make:migration
```

Then run migrations to sync database schema:
```bash
php bin/console doctrine:migrations:migrate
```
