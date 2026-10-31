<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Production Preflight for Laravel
    |--------------------------------------------------------------------------
    |
    | Published to config/production-preflight.php with
    | `php artisan vendor:publish --tag=production-preflight-config`.
    |
    | Environment values are read with env() here and nowhere else: outside a config file env()
    | answers null as soon as the configuration is cached.
    |
    */

    /*
    | The files the file checks read. Null means the `.env` and `.env.example` in the base path of
    | the application. `php artisan env:lint <file>` lints another file for one run.
    */
    'env' => [
        'file' => env('PRODUCTION_PREFLIGHT_ENV_FILE'),
        'example' => env('PRODUCTION_PREFLIGHT_ENV_EXAMPLE'),
    ],

    /*
    | Keys of the example file a server may leave out of its `.env`, because the application reads a
    | default for each. An entry that ends in `*` matches every key it begins. `env-example-keys`
    | passes where only such keys are missing, and names them.
    */
    'optional_env_keys' => [],

    /*
    | The names of `app.env` that declare each stage, compared without case. The production rules
    | apply to production and staging. A name none of the lists holds is undetermined, and a check
    | that depends on the stage then says it could not tell instead of passing.
    */
    'stages' => [
        'production' => ['production', 'prod', 'live'],
        'staging' => ['staging', 'stage', 'preprod', 'uat'],
        'development' => ['local', 'testing', 'test', 'dev', 'development', 'ci'],
    ],

    /*
    | The endings of the host names a machine for development is served under. Where `app.env`
    | declares a development stage and `app.url` names a host that ends in none of them, other than
    | `localhost` and a loopback or private address, the application runs on a server, and
    | `development-stage-server` fails.
    */
    'development_hosts' => ['.test', '.localhost', '.local'],

    /*
    | An endpoint of the application's own that a monitor pulls the health results from, such as
    | `/health/state`. `health-notifications` counts it as a second road for alerts, as it counts
    | the endpoint of Oh Dear.
    */
    'health_endpoint' => null,

    /*
    | Checks this application acknowledges, by key, each with its reason. An acknowledged check
    | passes and names the reason in every report, so the decision stays visible:
    |
    |     'queue-connection' => 'One person uses this installation; jobs run in the request on purpose.',
    */
    'acknowledged' => [],

    /*
    | What the placeholder check takes for a value nobody replaced. `values` are compared without
    | case, and an entry that ends in `*` matches every value it begins. `required` names the keys
    | that must be set and not empty. A key whose name matches `secret_keys` also fails when its
    | value is the one the committed example file carries, unless the value only refers to other
    | keys. A `VITE_` key is left out of `secret_keys`: Vite compiles it into the page.
    */
    'placeholders' => [
        'values' => [
            'changeme', 'change-me', 'change_me', 'changethis', 'change-this',
            'replaceme', 'replace-me', 'todo', 'tbd', 'xxx*', 'your-*', 'your_*', '<*',
        ],
        'required' => [],
        'secret_keys' => '/^(?!VITE_)(?:.*_)?(?:KEY|SECRET|PASSWORD|TOKEN)$/i',
    ],

    /*
    | The env-over-http check requests `.env` and `.git/config` from `app.url`, and only when it is
    | switched on: a health check that requests its own site on every run is a decision to make. Off,
    | the check says it did not look.
    |
    | The switch is read typed: only `true`, `1`, `yes` and `on` switch it on. The timeout in seconds
    | is held to at least 1, and a value that is no such number keeps the 5 shipped here.
    */
    'http_probe' => [
        'enabled' => in_array(
            is_string($probe = env('PRODUCTION_PREFLIGHT_HTTP_PROBE')) ? strtolower(trim($probe)) : $probe,
            [true, '1', 'true', 'yes', 'on'],
            true,
        ),
        'timeout' => is_numeric($probeTimeout = env('PRODUCTION_PREFLIGHT_HTTP_PROBE_TIMEOUT')) && (int) $probeTimeout >= 1
            ? (int) $probeTimeout
            : 5,
    ],

    /*
    | The PHP binary of the web server, for the php-version-parity check, which runs it with `-v`
    | and compares its version with the command line's. Null leaves the check undetermined. The
    | timeout in seconds is held to at least 1, and a value that is no such number keeps the 10
    | shipped here.
    */
    'php' => [
        'fpm' => env('PRODUCTION_PREFLIGHT_PHP_FPM'),
        'timeout' => is_numeric($phpTimeout = env('PRODUCTION_PREFLIGHT_PHP_TIMEOUT')) && (int) $phpTimeout >= 1
            ? (int) $phpTimeout
            : 10,
    ],

];
