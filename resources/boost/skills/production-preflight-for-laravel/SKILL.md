---
name: production-preflight-for-laravel
description: >
  Stop a Laravel deploy on a production misconfiguration and report one in operation with
  Production Preflight for Laravel: the deploy command, the laravel-health registration,
  env:lint, stages and acknowledgements.
license: MIT
metadata:
  author: pushery
---

# Production Preflight for Laravel

Use this skill when a Laravel application installs `pushery/production-preflight-for-laravel`, wires it into a deploy, or acts on one of its findings. The full reference is at <https://docs.pushery.com/production-preflight-for-laravel/>.

## Primary Goal

Make sure a production misconfiguration stops the deploy, and that the state which arises later is reported through laravel-health, without a value of the `.env` reaching a log or a notification.

## Workflow

### 1. Install

```bash
composer require pushery/production-preflight-for-laravel
```

The service provider is registered automatically through package discovery, and `spatie/laravel-health` is installed alongside.

### 2. Configure

```bash
php artisan vendor:publish --tag="production-preflight"
php artisan vendor:publish --tag="production-preflight-config"
php artisan vendor:publish --tag="production-preflight-lang"
```

The first publishes everything, the other two one group each. Every option in `config/production-preflight.php` is documented inline. Two need attention in most applications:

- `stages`: the names of `app.env` per stage. A name on none of the lists makes the stage undetermined, and the production checks then report "not checked". Add a custom name, `qa` or `prd`, to the right list.
- `acknowledged`: a check that is a deliberate choice for this application, by its key with the reason. It then passes and names the reason in every report.

### 3. Stop the deploy

Add the command to the deploy script, which needs `set -e` to stop on it, after `composer install` and `config:cache`, and before `queue:restart`:

```bash
php artisan preflight --deploy
```

Exit codes: 0 no check failed, 1 a check failed, 2 a check could not look. A 2 is not a pass; find the check that said "not checked" and give it what it needs, or acknowledge it.

### 4. Report in operation

```php
use Pushery\ProductionPreflight\Preflight;
use Spatie\Health\Facades\Health;

Health::checks(Preflight::checks());
```

laravel-health runs them with its scheduled `health:check`. Give it a channel besides mail, the Oh Dear endpoint or a Slack webhook in `config/health.php`: one of the findings is a mailer that delivers nothing, and that message would not arrive by mail.

### 5. Lint a `.env`

```bash
php artisan env:lint
php artisan env:lint .env.production
php artisan env:lint --quiet-when-clean
```

It exits with 1 on any finding, warnings included, and with 2 when the file cannot be read.

## Examples

A Forge deploy script that stops on a finding:

```bash
set -e
cd $FORGE_SITE_PATH
git pull origin $FORGE_SITE_BRANCH
$FORGE_COMPOSER install --no-dev --no-interaction --prefer-dist --optimize-autoloader
$FORGE_PHP artisan config:cache
$FORGE_PHP artisan preflight --deploy
$FORGE_PHP artisan migrate --force
$FORGE_PHP artisan queue:restart
```

Acknowledging a choice:

```php
// config/production-preflight.php
'acknowledged' => [
    'queue-connection' => 'One person uses this installation; jobs run in the request on purpose.',
],
```

## Anti-Patterns

- Running `preflight --deploy` without `set -e` in the script: the finding is printed and the deploy carries on. Put `set -e` at the top.
- Running it after `queue:restart`: the deploy is then recorded after the restart signal, and the `queue-restart` check reports workers on old code. Run it before.
- Running it before `config:cache` on a server with a cached configuration: the checks judge the configuration of the previous deploy. Cache first.
- Passing `--show-values` to a command a deploy tool or a scheduler runs: its output is kept, and `preflight --deploy` refuses the option for that reason. Use it on the server's terminal only.
- Treating exit code 2 as success: it means a check could not look. Make it look, or acknowledge it with the reason.
- Silencing a check by removing it from the laravel-health registration instead of acknowledging it: the reason then stands nowhere, and the deploy command still runs it.
