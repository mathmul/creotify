
# Creotify

Backend Assignment for Creatim


## Latest test results

```bash
➜  creotify git:(main) ✗ composer test

   PASS  Tests\Smoke\HealthEndpointSmokeTest
  ✓ it responds with 200 OK for /api/health                                                    0.07s

   PASS  Tests\Smoke\KernelSmokeTest
  ✓ it boots the Symfony kernel successfully                                                   0.01s

   PASS  Tests\Smoke\RouteSmokeTest
  ✓ it all public routes respond without error                                                 0.04s

   PASS  Tests\Smoke\UnimplementedClassesTest
  ✓ it sends SMS with ProviderA                                                                0.01s
  ✓ it sends SMS with ProviderB                                                                0.01s

   PASS  Tests\Unit\Entity\CustomerTest
  ✓ it adds and removes orders                                                                 0.01s

   PASS  Tests\Unit\Entity\OrderTest
  ✓ it generates an order number automatically
  ✓ it adds and removes order items
  ✓ it updates total price when items are added or removed

   PASS  Tests\Unit\Service\OrderServiceTest
  ✓ it creates a valid order successfully                                                      0.01s
  ✓ it throws on duplicate article                                                             0.01s
  ✓ it throws on duplicate subscription                                                        0.01s

   PASS  Tests\Unit\Service\SMS\RateLimitedSmsManagerTest
  ✓ it uses primary provider until rate limit is reached                                       0.01s
  ✓ it switches to fallback provider after 5 messages
  ✓ it resets counter after a minute and uses primary again

   DEPR  Tests\Feature\ApiOrderTest
  ! it creates an order successfully → Passing $sequence as a Sequence object to
    Doctrine\DBAL\Platforms\AbstractPlatform::getDropSequenceSQL is deprecated. Pass it as a quoted
    name instead. (AbstractPlatform.php:2434 called by…                                        0.14s
  ✓ it prevents duplicate subscription purchase                                                0.11s
  ✓ it lists all orders for a customer                                                         0.14s
  ✓ it retrieves a specific order by orderNumber                                               0.11s
  ✓ it cancels (deletes) an order successfully                                                 0.13s

   PASS  Tests\Integration\DataFixtures\AppFixturesTest
  ✓ it executes AppFixtures load() successfully                                                0.11s

   PASS  Tests\Integration\Repository\OrderRepositoryTest
  ✓ it returns bool for customerHasSubscription                                                0.08s
  ✓ it returns bool for customerHasArticle                                                     0.07s
  ──────────────────────────────────────────────────────────────────────────────────────────────────
   DEPRECATED  Tests\Feature\ApiOrderTest > it creates an order successfully
  Passing $sequence as a Sequence object to
  Doctrine\DBAL\Platforms\AbstractPlatform::getDropSequenceSQL is deprecated. Pass it as a quoted
  name instead. (AbstractPlatform.php:2434 called by PostgreSQLPlatform.php:808,
  https://github.com/doctrine/dbal/issues/4798, package doctrine/dbal)

  at vendor/doctrine/deprecations/src/Deprecation.php:208
    204▕             $link,
    205▕             $package
    206▕         );
    207▕
  ➜ 208▕         @trigger_error($message, E_USER_DEPRECATED);
    209▕     }
    210▕
    211▕     /**
    212▕      * A non-local-aware version of PHPs basename function.

      +8 vendor frames
  9   tests/Traits/RefreshDatabase.php:33
  10  tests/Feature/ApiOrderTest.php:14


  Tests:    1 deprecated, 22 passed (57 assertions)
  Duration: 1.17s

  Controller/Api/HealthController ........................................................... 100.0%
  Controller/Api/OrderController ............................... 33, 58..59, 68..69, 99, 119 / 88.9%
  Entity/Article ....................................................... 46, 58, 82, 94..101 / 64.7%
  Entity/Customer ................................................................... 39..63 / 63.2%
  Entity/Order ................................................ 51, 61..63, 85..87, 109..111 / 80.6%
  Entity/OrderItem .............................................. 42, 64..66, 76..78, 88..90 / 58.8%
  Entity/SubscriptionPackage ........................................... 46, 58, 82, 94..101 / 64.7%
  Kernel .................................................................................... 100.0%
  Repository/ArticleRepository ...................................................... 31..35 / 20.0%
  Repository/Contract/ArticleRepositoryInterface ............................................ 100.0%
  Repository/Contract/CustomerRepositoryInterface ........................................... 100.0%
  Repository/Contract/OrderItemRepositoryInterface .......................................... 100.0%
  Repository/Contract/OrderRepositoryInterface .............................................. 100.0%
  Repository/Contract/RepositoryInterface ................................................... 100.0%
  Repository/Contract/SubscriptionPackageRepositoryInterface ................................ 100.0%
  Repository/CustomerRepository ............................................................. 100.0%
  Repository/OrderItemRepository .................................................... 31..38 / 12.5%
  Repository/OrderRepository ................................................................ 100.0%
  Repository/SubscriptionPackageRepository .......................................... 31..35 / 20.0%
  Service/OrderService ...................................................................... 100.0%
  Service/SMS/Contract/SmsManagerInterface .................................................. 100.0%
  Service/SMS/Contract/SmsProviderInterface ................................................. 100.0%
  Service/SMS/RateLimitedSmsManager ......................................................... 100.0%
  Service/SMS/SmsProviderA .................................................................. 100.0%
  Service/SMS/SmsProviderB .................................................................. 100.0%
  ──────────────────────────────────────────────────────────────────────────────────────────────────
                                                                                       Total: 78.5 %

   FAIL  Code coverage below expected  100.0 %, currently  78.5 %.

Script XDEBUG_MODE=coverage php vendor/bin/pest --coverage --min=100 handling the test event
returned with error code 1
```

> _**Note:** Deprecation warnings are expected due to Doctrine DBAL 4 compatibility; they do not indicate test failures._

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
- [x] Add Docker setup (PHP-FPM + Nginx + Postgres + Mailpit)
- [x] Extend Dockerfile with Xdebug + Ray pre-configuration
- [x] Update `.env` with Postgres credentials
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
- [x] Add PHPStan
- [x] Add composer scripts:
    ```json
        "scripts": {
            "lint": "php vendor/bin/php-cs-fixer fix --dry-run --diff",
            "lint:fix": "php vendor/bin/php-cs-fixer fix --verbose",
            "test": "XDEBUG_MODE=coverage php vendor/bin/pest --coverage --min=100",
            "test:smoke": "php vendor/bin/pest --group=smoke",
            "test:unit": "php vendor/bin/pest --group=unit",
            "test:feature": "php vendor/bin/pest --group=feature",
            "test:integration": "php vendor/bin/pest --group=integration",
        }
    ```
    See composer.json `"scripts"` for more aliases.
- [x] Ensure all tests pass with `composer test` and 100% coverage
- [ ] Add authentication


### Domain Modeling

- [x] Create entities:
  - [x] `Article`
  - [x] `SubscriptionPackage`
  - [x] `Customer`
  - [x] `Order`
  - [x] `OrderItem`
- [x] Generate and run Doctrine migrations
- [x] Add Fixtures for sample data using Faker

### Business Logic

- [x] Implement validation for unique item/subscription per customer
- [x] Implement order total calculation
- [x] Implement error handling (custom exceptions)
- [x] Implement SMS service management (two services, 5 SMS/min limit + fallback)
- [x] Log SMS events (Spatie Ray / Monolog)
- [ ] Implement soft-deletion for all entities

### Controllers & Forms

- [x] Order endpoints:
  - [x] Create order (purchase flow)
  - [x] List orders
  - [x] Cancel order (handle deletion logic)
- [ ] Implement Symfony Forms for validation and clean DTO mapping

### Testing (TDD with Pest)

- [x] Write smoke tests for:
  - [x] Kernel
  - [x] Health endpoint
  - [x] All public routes
- [x] Write unit tests for:
  - [x] OrderService (purchase logic)
  - [x] SMSService (fallback logic)
- [ ] Write functional tests for:
  - [ ] Order creation endpoint
  - [ ] Duplicate purchase restriction
- [x] Enforce 100% coverage via composer script
- [ ] Configure Ray to show test output (`ray()->showQueries()->showEvents();`)

### Dockerization

- [x] Add `Dockerfile` + `docker-compose.yml`
- [x] Include PHP 8.4, Composer, Postgres 18, Nginx

### Documentation

- [x] Add setup instructions to README
- [ ] Add Postman/cURL examples for all API endpoints
- [x] Document SMS Service usage (interface + example)
- [ ] Add note on test execution (`composer test`)

### Polishing

- [ ] Ensure code comments in complex logic
- [ ] Apply DRY and MVC clean-up
- [x] Format code via PHP-CS-Fixer
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
composer migrate

# 7) Load fixtures
composer dev:db:seed

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
docker compose exec app composer migrate

# 6) Load fixtures
docker compose exec app composer dev:db:seed

# 7) (Optional) Health + tests with coverage
curl http://localhost:8080/api/health && echo
docker compose exec app composer test
```

> _**Note:** All `composer <command>` commands mentioned below can be run in the container with `docker compose exec app composer <command>`._


## Documentation

### SMS Service Example

When an order is successfully placed, an SMS confirmation is sent to the customer.
The system uses the `RateLimitedSmsManager`, which delegates to `SmsProviderA` until
five messages per minute are reached, then automatically switches to `SmsProviderB`.

Example usage:

```php
use App\Service\SMS\RateLimitedSmsManager;
use App\Service\SMS\SmsProviderA;
use App\Service\SMS\SmsProviderB;

$manager = new RateLimitedSmsManager(new SmsProviderA(), new SmsProviderB());
$manager->sendSMS('+38612345678', 'Your order was successfully placed!');
```

In development, messages are logged via Spatie Ray and visible in the Ray desktop app.

### Testing

Run all tests (smoke, unit, integration, and feature) with coverage:

```bash
composer test
```

To run a specific group:

```bash
composer test:smoke
composer test:unit
composer test:integration
composer test:feature
```

Code coverage target: 100% (adjust in composer.json <del>if</del>when necessary)

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

We use **Pest** instead of PHPUnit for cleaner, expressive tests. All test files reside in `tests/` directory, except `./phpunit.dist.xml` on project root.
Symfony’s native test classes (`WebTestCase`, `KernelTestCase`) work perfectly with Pest.

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

pest()->extends(KernelTestCase::class)->in('Smoke')->group('smoke');
pest()->extends(KernelTestCase::class)->in('Unit')->group('unit');
pest()->extends(WebTestCase::class)->in('Feature')->group('feature');
pest()->extends(KernelTestCase::class)->in('Integration')->group('integration');
```

We initialized Pest:

```bash
./vendor/bin/pest --init
```

Preferably we create new Pest tests manually under `tests/{Smoke,Unit,Feature,Integration}` directories, but we can create new PHPUnit tests using the built-in Symfony Maker command and later convert them to Pest:

```bash
php bin/console make:test WebTestCase Feature\\ExampleApiTest          # Functional test
php bin/console make:test KernelTestCase Unit\\ExampleUnitTest --unit  # Unit test
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
# All tests + coverage
composer test
# or one of test groups w/o coverage
composer test:smoke
composer test:unit
composer test:feature
composer test:integration
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
# Dry run
composer lint
# Fix
composer lint:fix
```

#### Database Models

Make sure docker container is running - see [Getting started](#getting-started).

After creating/updating models, create new migration files:
```bash
composer make:migrations
```

Then sync migrations to database:
```bash
composer migrate
```
