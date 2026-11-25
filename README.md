<p align="center">
  <a href="https://github.com/pushery/production-preflight-for-laravel">
    <img src="https://raw.githubusercontent.com/pushery/production-preflight-for-laravel/main/art/header.png" alt="Production Preflight for Laravel" width="100%">
  </a>
</p>

# Production Preflight for Laravel

[![Latest Version](https://img.shields.io/packagist/v/pushery/production-preflight-for-laravel.svg)](https://packagist.org/packages/pushery/production-preflight-for-laravel)
[![PHP Version](https://img.shields.io/packagist/dependency-v/pushery/production-preflight-for-laravel/php.svg)](https://packagist.org/packages/pushery/production-preflight-for-laravel)
[![Laravel Versions](https://badge.laravel.cloud/badge/pushery/production-preflight-for-laravel?style=flat)](https://packagist.org/packages/pushery/production-preflight-for-laravel)
[![License](https://img.shields.io/packagist/l/pushery/production-preflight-for-laravel.svg)](LICENSE)

[![Tests: Pest 5](https://img.shields.io/badge/tests-Pest%205-8BC34A.svg)](https://pestphp.com)
![Coverage: 100%](https://img.shields.io/badge/coverage-100%25-brightgreen.svg)
![Type coverage: 100%](https://img.shields.io/badge/types-100%25-brightgreen.svg)
[![PHPStan: level 10](https://img.shields.io/badge/PHPStan-10-blue.svg)](https://phpstan.org)
[![Code style: Pint](https://img.shields.io/badge/code%20style-pint-orange.svg)](https://laravel.com/docs/pint)

Laravel production checks for what passes every test: unsafe settings, a .env that loads other values than it shows, and installs running on stale state.

## Documentation

**Full documentation at
[docs.pushery.com/production-preflight-for-laravel](https://docs.pushery.com/production-preflight-for-laravel/).**
This README is the quick start; the guides and the configuration reference live there.

## Requirements

- PHP 8.4+ (tested on 8.4.27, 8.5.x and 8.6.x)
- Laravel 13+

Tested on PostgreSQL 18.7 and MySQL 8.4.13, the databases Laravel Cloud runs (serverless Postgres
and MySQL 8.4 LTS). The fast suite runs on SQLite.

Tested on the floor of the declared PHP range, because a `>=` promise is only proven
by running on the boundary, and on the minor version above it, so the range above the
floor is exercised here rather than first by a consumer.

## Installation

```bash
composer require pushery/production-preflight-for-laravel
```

The service provider is registered automatically through package discovery. The package builds on [laravel-health](https://spatie.be/docs/laravel-health), which Composer installs alongside.

## Usage

A misconfigured production application rarely fails: `APP_DEBUG=true` serves pages, `MAIL_MAILER=log` accepts every message, and a key set twice in `.env` is valid syntax. This package checks for those states in the deploy, so they never go live, and on a schedule, because half of them appear afterwards.

Stop a deploy on a finding. In the deploy script, with `set -e`, after `composer install` and `config:cache`:

```bash
php artisan preflight --deploy
```

It exits with 0 when no check failed, 1 when one did, and 2 when a check could not look. Warnings are printed and let the deploy through.

Report in operation through laravel-health, in the `boot` method of a service provider:

```php
use Pushery\ProductionPreflight\Preflight;
use Spatie\Health\Facades\Health;

Health::checks(Preflight::healthChecks());
```

`healthChecks()` leaves out a check that cannot look in the process laravel-health runs it in: `opcache` on the command line, `php-version-parity` in a web request, and on the command line until the web server's binary is named, `env-over-http` until the probe is switched on, and `queue-restart` until `preflight --deploy` recorded a deploy. laravel-health counts a skipped check as a failure unless `health.treat_skipped_as_failure` says otherwise, so each of them would report a correctly configured server as failing.

Lint a `.env` file on its own, in a pipeline or on a schedule:

```bash
php artisan env:lint .env.production
```

The checks cover the `.env` file (keys set twice, keys missing against the example, placeholders, a cut-off `#`), the configuration (debug mode, the application key, mail that goes nowhere, test-mode payment keys, queue, session and cache drivers) and the installation (a stale configuration cache, workers that missed `queue:restart`, pending migrations, exposed secrets, arguments kept in the traces of exceptions). No check passes when it could not look, and no value of the application reaches a log, a notification or a stored result. The [documentation](https://docs.pushery.com/production-preflight-for-laravel/) lists every check and its verdict.

## Configuration

Publish the configuration file:

```bash
php artisan vendor:publish --tag=production-preflight-config
```

This writes `config/production-preflight.php`, where every option is documented inline. A check that does not fit an application is acknowledged there by its key, with the reason, which then stands in every report:

```php
'acknowledged' => [
    'queue-connection' => 'One person uses this installation; jobs run in the request on purpose.',
],
```

To change the package's translations, publish them and edit the copies:

```bash
php artisan vendor:publish --tag=production-preflight-lang
```

The translations land in `lang/vendor/production-preflight`, where Laravel reads them before the
package's own. `--tag=production-preflight` publishes the configuration and the translations at once.

## Testing

The test suite is not part of the published package: the tests and their PHPUnit
configuration stay in the development repository, so a clone of this repository has no
`composer test`. [CONTRIBUTING.md](https://github.com/pushery/production-preflight-for-laravel/blob/main/CONTRIBUTING.md) says how a change is tested on its way in.

## Security

Please review the [security policy](SECURITY.md) and report vulnerabilities
privately rather than opening a public issue.

## Built by PUSHERY

This package is built and maintained by [PUSHERY](https://www.pushery.com) — a
Berlin-based studio building Laravel applications, SaaS products, and open-source
tools.

Building a Laravel UI? [WireKit](https://www.wirekit.app) is PUSHERY's open-source
Livewire component kit.
Browse the rest of our work at [pushery.com](https://www.pushery.com).

## License

The MIT License (MIT). See [LICENSE](LICENSE) for details.
