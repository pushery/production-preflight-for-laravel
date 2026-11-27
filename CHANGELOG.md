# Changelog

All notable changes to `pushery/production-preflight-for-laravel` are documented here. The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/), and the project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

A release carries only the sections that have an entry, in this order: `### ⚠️ Breaking`, `### ✨ Added`, `### 🔧 Changed`, `### ⚡ Performance`, `### 🐛 Fixed`, `### 🔒 Security`, `### 🧹 Deprecated`, `### 🗑️ Removed` and `### 📚 Documentation`.

## [0.8.1] - 2026-11-27

### ⚡ Performance

- **A start of the console builds none of the package's commands.** `env:lint` and `preflight` carry `#[AsCommand]` with the name of their signature, so Laravel keeps each in the map of its loader and builds it only when it runs. Before, every start of a host's console built both through the container, for each run of the schedule, each queue worker and each `artisan` call, whatever ran.

### 🐛 Fixed

- **`queue-restart` keeps its verdict after a deploy that fails after `preflight --deploy`.** The record of the last deploy held one time for every release, so a deploy that passed its preflight and stopped later, at a migration or at the switch, made the live release report its workers as running the code from before it, until a deploy came through. Each release now keeps its own line, under the real path of its directory, and the check reads the line of the release it runs in. A record of a single time, from before, still counts for every release, and the line of a release no longer on the disk goes when the next deploy is recorded.

### 📚 Documentation

- **The checks page names what neither check sees: a `config:cache` outside a deploy.** After a change of `.env` the workers keep the configuration they started with until they restart, while `config-cache-fresh` and `queue-restart` both pass. `queue:restart` belongs after such a `config:cache` as well.

## [0.8.0] - 2026-11-26

### 🔧 Changed

- **`PRODUCTION_PREFLIGHT_HTTP_PROBE` set to a spelling that says neither on nor off stops the boot.** A value such as `ture` kept the probe off. The boot and `config:cache` now stop with an `InvalidArgumentException` that names the variable, the value as written and every spelling for on and off. A missing or empty line and `empty`, `(empty)`, `null` and `(null)` keep it off, as before.

  **Upgrade:** a host whose `.env` gives the variable another spelling corrects it, or deletes the line to keep the probe off.

### 🐛 Fixed

- **A limit per token or per key is no credential to `placeholder-values`.** `SANCTUM_HARDENING_PER_TOKEN=300` with the value of the committed example file failed the check, because the name ends in `TOKEN`, so `preflight --deploy` held back a deploy that kept the shipped limit. A key whose name ends in `PER_TOKEN` or `PER_KEY` now counts as a limit, whatever `production-preflight.placeholders.secret_keys` matches, and that holds for a published configuration as well. A token with the value of the example file still fails.

## [0.7.2] - 2026-11-25

### 🐛 Fixed

- **`queue-restart` no longer fails while a deploy runs.** `preflight --deploy` records the deploy before the releases switch, and the release still live read the record from the shared `storage` until the deploy script restarted the workers at its end. Its health check reported workers older than the deploy in that gap, and laravel-health mailed the operator on every deploy that did everything right. The check now passes for 15 minutes after a deploy was recorded, `queue_restart.grace_minutes` or `PRODUCTION_PREFLIGHT_QUEUE_RESTART_GRACE`, as `config-cache-fresh` does for a changed `.env`, and reports only after that. 0 reports at once.

### 📚 Documentation

- **The requirements name the versions the package is tested on.** PHP 8.4.27, 8.5.x and 8.6.x, PostgreSQL 18.7 and MySQL 8.4.13, as the lanes run them, in the README and on the start page of the documentation.

## [0.7.1] - 2026-11-23

### 🔧 Changed

- **`symfony/console` is a declared dependency.** `preflight` and `env:lint` escape what they print with its output formatter. Every Laravel version the package supports installs the component already, so no installation resolves differently.

### 🐛 Fixed

- **`preflight` and `env:lint` print every finding, piece of evidence and path as it stands.** Symfony's console formatter reads a part that looks like a style, such as `<comment>` or `</>`, as formatting and drops it, so a value from the `.env` that `--show-values` prints, or a path with such a part, arrived changed. The two commands now pass each of them through `OutputFormatter::escape()`, and the verdict keeps its color.
- **The finding on carriage returns names two lines in the plural under a regional locale.** Each form of `endings_statement` names its count, `{1} …|[2,*] …`, in all seven catalogs. Laravel picks a form without a count by the name of the locale, from a table that knows `de_AT` but no locale written with a hyphen, so an application on `de-AT` worded two lines as one.

## [0.7.0] - 2026-11-21

### ✨ Added

- **`DevelopmentHosts::serves()` holds a host to the measure of the `development-stage-server` check.** The check reads `app.url`, which comes from the same `.env` as `APP_ENV`, so a server that kept the whole `.env.example` passes it. An application that answers requests holds the host of a real request to the same measure, `localhost`, the endings of `production-preflight.development_hosts` and loopback or private addresses, instead of rebuilding it: it hands over the value of that setting, `DevelopmentHosts::serves($host, config('production-preflight.development_hosts'))`, and without it the shipped endings count. The checks page names the limit and shows the call.

## [0.6.1] - 2026-11-20

### 🐛 Fixed

- **`preflight --deploy` records only a deploy it lets through.** It wrote the time of the deploy before it ran the checks, so a deploy they stopped was recorded as well. On a zero-downtime deploy the old release kept serving with its workers, and `queue-restart` then reported those workers as running the code from before the deploy, on every run until a deploy passed. A deploy stopped with exit code 1 or 2 now leaves the record of the deploy before it. On a deploy in place the new code is already on the disk when the script stops, and workers still on the old code go unreported until the next deploy passes.
- **`session-driver` fails a session driver that is not set.** `SESSION_DRIVER=null` loads as null and an empty `SESSION_DRIVER=` line as an empty string, and Laravel's session manager resolves neither, so the first request that starts a session raised an error while the check passed. Both fail now, with a finding on `SESSION_DRIVER`, and the summary of a failure, which covers both cases, now says that sessions do not outlive the request.
- **`app-key` fails a previous key that others know.** Laravel decrypts cookies and encrypted payloads with every key in `app.previous_keys`, which it fills from `APP_PREVIOUS_KEYS`, and accepts a URL signed with any of them, but the check read `app.key` alone. A server that rotated away from the key of the committed example file and kept it as a previous key passed, while whoever reads the repository could still forge what the application accepts. A previous key that is a placeholder, or a key of the example file's `APP_KEY` or `APP_PREVIOUS_KEYS`, now fails with a finding on `APP_PREVIOUS_KEYS` that names its position in the list, and the key itself is held to the example file's previous keys as well. **Upgrade:** a server that fails removes the key from `APP_PREVIOUS_KEYS`, which ends the sessions and cookies made with it.
- **`env-over-http` no longer passes a site that redirects a file to the same path at another address.** The probe follows no redirect, and it took every redirect for a file the site does not serve, so a site whose `app.url` is `http` and redirects to `https`, or a bare domain that redirects to `www`, passed while the address that would answer was never asked. A redirect that keeps the path at another scheme, host or port now answers "not checked", and `--show-values` names the address. **Upgrade:** set `app.url` to the address the site redirects to. A redirect to another path, such as a login page, still passes.
- **`placeholder-values` keeps the shipped `secret_keys` pattern where the configured one is no pattern.** A `production-preflight.placeholders.secret_keys` with a typo PHP cannot compile, or a setting that is no text, matched no key at all, so a secret copied from the committed example file passed unseen and the check reported that no value is a placeholder. Such a setting now keeps the shipped pattern, as a timeout that is no number keeps its default.
- **`queue-restart` and `queue-connection` know the `deferred` and `background` drivers.** Laravel 13 ships both connections in `config/queue.php`, and neither runs a worker: `deferred` runs each job in the process that dispatched it once the response is sent, `background` in a PHP process started for the job. With either as the default connection, `queue-restart` failed after every recorded deploy, because no restart signal was ever sent, and `queue-connection` passed with the summary that the connection hands jobs to workers. `queue-restart` now passes them as a connection without a worker, and `queue-connection` warns for them as it does for `sync`, since a failed job is not tried again and a job that has not finished is lost with its process. A `failover` connection is read through the connections it names: `queue-restart` asks for the signal as soon as one of them runs workers, and `queue-connection` judges the first. **Upgrade:** an application that runs its queue on `deferred` or `background` on purpose acknowledges `queue-connection` under `production-preflight.acknowledged`.
- **`mail-transport` reads a mailer through its `url`.** Laravel lays the host, port and credentials of a mailer's `url`, which its configuration fills from `MAIL_URL`, over the settings beside it, and the scheme of the URL becomes the transport, but the check judged the transport, host and port beside it. A `MAIL_URL` pointing at a mail catcher, with `MAIL_HOST` still at its default, warned about SMTP to the machine itself, which `preflight --deploy` lets through; one pointing at a remote relay warned on every run; and one with the scheme `log` was judged as SMTP. The URL now decides, as it does for Laravel. A URL that names no transport, an empty `MAIL_URL=` line among them, fails, because Laravel then has none to send with.
- **`writable-directories` asks the directories the configuration points Laravel to.** It asked a fixed list under `storage` and `bootstrap/cache`, so a server whose `VIEW_COMPILED_PATH` named a directory it cannot write to passed until the first view was compiled, and a server that moved its bootstrap caches with `APP_CONFIG_CACHE` and its siblings failed over a `bootstrap/cache` nothing writes to. The check now asks `view.compiled`, the `path` of a default cache store on the `file` driver, `session.files` under the `file` session driver, and the directories the cached configuration, services, packages, routes and events are written to. A configured directory Laravel creates on its first write, the compiled views or the files of the cache store, passes while it is missing and the directory above it is writable. `storage/framework/sessions` is asked only where sessions are kept in files.
- **`queue-restart` and the deploy order name `php artisan reload` beside `queue:restart`.** Laravel 13 ends a deploy with `php artisan reload`, which runs `queue:restart` and writes the same restart signal, but the findings of `queue-restart` advised adding `queue:restart`, and the deploy page, the Boost skill and the answer without a recorded deploy placed `preflight --deploy` before `queue:restart` alone, so a deploy that follows Laravel's documentation was not told that the order holds for `reload` as well. Each now names both commands, in every language the package ships.
- **`pending-migrations` leaves out a migration `migrate` skips.** `migrate` asks every migration it is handed whether it should run, skips one whose `shouldRun()` answers false without recording it, and is never handed one the application names in `Migrator::withoutMigrations()`, but the check counted both as pending, so an application that holds a migration back on purpose, for a feature not yet switched on, failed on every run. Both now pass, and the summary names them. A migration that answers false behind one that has not run still counts, because running that one may change its answer.
- **No result names the host of `app.url`, the value of `APP_ENV` or the endpoint in `production-preflight.health_endpoint`.** `development-stage-server` wrote the host and the stage name into its summary and its finding, `health-notifications` wrote the endpoint into its summary, where it may carry the token that guards it, and a check that could not read the `.env` named the directories above the application, which carry the name of the site or of a user on most servers, while the package promises that no value of the application reaches a stored result or a notification. The summaries and the finding now name the settings, a file within the application is named from there, and `preflight --show-values` prints the host and the value of `APP_ENV`. A file the configuration places elsewhere keeps its path.
- **`cache-store` fails the `session` store and reads a `failover` store through the stores it names.** Laravel 13 ships a `session` cache store, which keeps the cache in the session of whoever sent the request: a throttle counts per visitor and starts over for a client without a cookie, `Cache::lock()` and a unique job end in an error because the store holds no lock, and a command, `queue:restart` among them, writes into a session nobody saves, so no worker restarts. The check passed it as a store that keeps what it is given, and it passed a `failover` store whatever that falls back to. The `session` store now fails as `array` does. A `failover` store is judged by its first store, which serves while it works, and warns where a store it falls back to keeps nothing past the request or lives in the session, which holds for the `failover` store Laravel ships, from `database` to `array`. **Upgrade:** give such a store a fallback that keeps what it is given, or acknowledge `cache-store` under `production-preflight.acknowledged`.
- **`queue-restart` names both causes of a missing restart signal.** It reported a missing signal as one that was never sent, but the cache loses it as well: `cache:clear` and `optimize:clear` empty the default store, a Redis instance without persistence comes back empty after a restart, and an eviction can remove the key, which `queue:restart` writes without an expiry. A worker restarts when the signal it started with changes, so in those cases the workers did restart, while the finding advised adding `queue:restart` to a deploy script that already runs it. The check still fails, with a summary that says the workers may run the code from before the deploy, and its finding names both causes; a signal older than the deploy is reported as before. The deploy page, the checks page and the Boost skill place `cache:clear` and `optimize:clear` before `php artisan reload` or `queue:restart`.
- **`php-version-parity` no longer passes in a web request.** The check compares the web server's binary with the PHP it runs in, which is the command line's only on the command line. laravel-health's Oh Dear endpoint runs the checks inside a request when it sends fresh results, which is its default, and there both sides were the web server's PHP, so the check passed whatever version the command line runs, while the scheduled run failed. In a web request the check now says it could not look, and `Preflight::healthChecks()` leaves it out there, as it leaves out `opcache` on the command line.
- **`exception-arguments` names the process whose php.ini it judged.** The check reads `zend.exception_ignore_args` in the PHP it runs in, and the command line and PHP-FPM each read a php.ini of their own, but its summary said only that the traces of exceptions keep no arguments. A server with the setting on for the command line and off for PHP-FPM read that every minute from the scheduled run, while every exception of a web request logged its arguments. The summaries of a pass and of a failure now say whether the command line or the web server was judged, and a pass on the command line adds that PHP-FPM is judged when the check runs in a web request.
- **`health-notifications` counts Oh Dear once laravel-health serves its endpoint.** laravel-health registers the Oh Dear endpoint only when `health.oh_dear_endpoint.enabled` comes with a secret and a url, but the check passed on `enabled` alone, so a server whose `.env` lacked `OH_DEAR_HEALTH_CHECK_SECRET` answered Oh Dear with a 404 and was told that Oh Dear pulls the results. Switched on without a secret or a url, the check now judges the other roads as before and warns in any case, with a finding on the setting that is missing.

### 📚 Documentation

- **The start page shows the package at work.** `preflight --deploy` reports on an application in production with debug mode on, mail written only to the log and a queue that runs in the request: two failures, two warnings and the checks that pass, as a deploy script prints them. The same run stands as text under the picture, for a reader who zooms, searches or copies.
- **The listener example imports `RuntimeException`.** The example that forwards `PreflightCompleted` to the application's own monitoring created a `RuntimeException` without importing it, so in the service provider the page names as its place, the name resolved in the provider's namespace and the listener failed with "Class not found" on the first run that found something.
- **The scheduled `env:lint` mails its report when the run fails.** The operation page showed a daily `env:lint` as silent unless it finds something, but Laravel's scheduler sends the output of a command it runs to `/dev/null`, so a finding of that run reached nobody. The schedule on the page names a reader now, `emailOutputOnFailure()`, and the page adds that the `PreflightCompleted` listener hears the scheduled run as well.
- **`env:lint` names every reason for its exit code 2.** The deploy page, the list of commands, the Boost skill and the description of the command said it exits with 2 when the file cannot be read, or when nothing could be checked. It exits with 2 whenever nothing was found and a check could not look, which also covers a missing example file and a stage, read from the file's `APP_ENV`, that none of the lists in `production-preflight.stages` names. A pipeline that read a 2 as an unreadable file looked in the wrong place.
- **The quick start runs `preflight --deploy` after `config:cache`, with `set -e`.** The start page placed the command after `composer install` alone. Run before `config:cache` on a server that keeps the previous deploy's cached configuration, the checks judge that configuration instead of the one about to serve, and without `set -e` the script prints the finding and deploys anyway. The page now says what the README and the deploy page say.
- **The schedule on the operation page prunes the results laravel-health keeps.** laravel-health stores a row for each check on every run in its database store, its default, and deletes those rows only through a `model:prune` that names its model, since a plain `model:prune` looks for models in `app/Models` alone. The page scheduled `health:check` every minute and nothing that removes what it writes, so the table grew by a row per check every minute. The schedule now runs `model:prune` for `HealthCheckResultHistoryItem` daily, which keeps the days `keep_history_for_days` sets, the page says why the model is named, and the Boost skill shows the same line.
- **The README and the start page no longer count the checks.** They said twenty-eight, one fewer than the package ships, and a number beside a list that grows goes stale with every check added. They now say what the checks cover, and the checks page lists each one.
- **The README names the databases Laravel Cloud runs and claims no more.** Its requirements said the package was tested on PostgreSQL and MySQL 8.4 and concluded that it would run on Laravel Cloud without further setup. A test run against a database server is no run on Laravel Cloud's serverless Postgres, so the sentence keeps the fact and drops the conclusion.

## [0.6.0] - 2026-11-11

### 🔧 Changed

- **The switch reads its rule from the package, so a fix to it reaches a published config.** `config/production-preflight.php` spelled out how a value of the `.env` turns it on or off, and a host that published the file kept that rule as it was on the day it published it, so a later fix to the rule, such as the one for an empty `.env` line, never reached the copy. The config calls `EnvSwitch::read()` now, and the rule ships in `src/Support/EnvSwitch.php`: `false`, `0`, `no` and `off` switch off, `true`, `1`, `yes` and `on` switch on, trimmed and in any case, and anything else keeps the value the package ships. **Upgrade:** in a published copy, replace the expression of it once with its call and import the class: `http_probe.enabled` with `EnvSwitch::read(env('PRODUCTION_PREFLIGHT_HTTP_PROBE'), false)`, and `use Pushery\ProductionPreflight\Support\EnvSwitch;` at the top.

## [0.5.0] - 2026-11-09

### ✨ Added

- **`exception-arguments` fails a production or staging server whose PHP keeps the arguments of every call in the trace of an exception.** With `zend.exception_ignore_args` off, as `php.ini-development` ships it, Laravel logs the trace of every reported exception with each argument in it, and PHP shortens a string there to 15 characters, so a PIN or a short code stands in the log whole wherever its parameter lacks `#[\SensitiveParameter]`. The check reads the setting of the process it runs in, and its finding names the php.ini of the command line and of PHP-FPM. **Upgrade:** a server built from `php.ini-production` passes. One that fails sets `zend.exception_ignore_args = On` in both and reloads PHP.

### 🐛 Fixed

- **`env-bytes` words a single carriage return in the singular.** A `.env` with one line ending in a carriage return was reported as "1 lines end in a carriage return", and the same held in every language the package ships. The finding now reads "one line ends in a carriage return", and from two lines on it counts them as before.

## [0.4.0] - 2026-11-07

### 🐛 Fixed

- **`config-cache-fresh` no longer reports a deploy that changed `.env`.** On a zero-downtime deploy the releases share one `.env`, and each caches its own configuration, so for the minutes a deploy takes, the release still live ran on a cache older than the `.env` the deploy had just changed, and the check failed until the new release took over. It now reports a newer `.env` once it has been the newer one for 15 minutes. `production-preflight.config_cache.grace_minutes`, or `PRODUCTION_PREFLIGHT_CONFIG_CACHE_GRACE`, sets the wait, and 0 reports at once, as before.

## [0.3.0] - 2026-11-03

### ✨ Added

- **`writable-directories` names the user it asks as, and looks for the newest log file one level down.** A failure names the user the process runs as, read from the system's user database and not from `USER`, which under `sudo -u`, a process supervisor or a container entrypoint names whoever started the process. Where the system cannot name the user, the failure says so, and otherwise the user is carried as `user` evidence beside `server`. The newest log file is looked for one level below `storage/logs` as well, where a channel with a directory of its own writes its file of the day, and that directory is asked too, because the file of the next day is created in it. Naming the user takes the POSIX extension, which `composer.json` now suggests.

### 🐛 Fixed

- **`env-example-keys` takes a key the example file names on a commented line as named.** An example file writes an optional key commented, with the default it leaves in force, as in `# FEED_BATCH=50`. A server that set such a key was told it was missing from the example file, one warning per key, and since `env:lint` counts a warning as a finding, its run ended with exit code 1 every day. The commented line now names the key in that direction. A commented line still asks nothing of `.env`, and a sentence in a comment that happens to hold an equals sign names no key.

## [0.2.0] - 2026-10-31

### ✨ Added

- **`development-stage-server` finds a server that declares a development stage.** Laravel's `.env.example` sets `APP_ENV=local`, and a server whose environment began as a copy of it keeps that: every production check then passes, and `preflight --deploy` ends with 0. The new check, in the `configuration` scope, reads a development stage against the host of `app.url` and fails where it is not `localhost`, a loopback or private address, or a name ending in one of `development_hosts` (`.test`, `.localhost` and `.local` unless set). An address without a host is undetermined, and production and staging pass.
- **`writable-directories` asks the newest log file as well, and names the server.** A log line is appended to the file that exists, and that file keeps the permissions of the process that opened it first, so a directory writable through its group can hold a current log only its owner may write to. The check reads the newest `*.log` in the log directory and fails where the user that runs it cannot write to it. A failure names the server and carries it as `server` evidence, so a finding on one of several servers says which.
- **`env-example-keys` passes a key the application declares optional.** `production-preflight.optional_env_keys` names keys of `.env.example` a server may leave out, because the application reads a default for each, by name or by a prefix that ends in `*`. The summary names the keys left out, and every other missing key still fails.
- **`health-notifications` reads every notification class, and an endpoint of the application's own.** laravel-health sends each class in `health.notifications.notifications`, so a class of the application's own that alerts through Slack or any other channel besides mail now passes, where only `CheckFailedNotification` counted. `production-preflight.health_endpoint` names an endpoint a monitor pulls the results from, and it counts as the endpoint of Oh Dear does.
- **`Preflight::healthChecks()` registers the checks laravel-health can run in its process.** laravel-health runs its checks from the scheduler, on the command line, and stores a check that could not look as skipped, which its default `treat_skipped_as_failure` counts as a failure. Registered through `Preflight::checks()`, as the README said, `opcache` could never look there, and `php-version-parity`, `env-over-http` and `queue-restart` could not with the shipped configuration, so every server reported a failing check. `healthChecks()` leaves out each check whose `canLookHere()` says it cannot look in this process or with this configuration, and the README, the docs and the Boost skill register it. `Preflight::checks()` still returns every check.

### 🔧 Changed

- **`ext-filter` and `illuminate/filesystem` are declared in `require`.** `development-stage-server` reads the host of `app.url` with `filter_var()`, and `writable-directories` asks Laravel's filesystem about the newest log file. Every Laravel installation already has both, since `laravel/framework` requires the extension and carries the component.

### 🐛 Fixed

- **`placeholder-values` passes a value that only refers to another key, and a `VITE_` key.** Laravel's example file sets `VITE_REVERB_APP_KEY="${REVERB_APP_KEY}"`, a server keeps the line as it is, and the check read it as a secret everyone knows from the repository, so every deploy of an application with Reverb stopped. A value made only of `${…}` references is no longer compared with the example, since the key it names is checked itself, and the shipped `secret_keys` pattern leaves `VITE_` keys out, because Vite compiles them into the page. A pattern an application sets in its own configuration keeps what it says.
- **`test-mode-keys` passes on staging.** A staging server takes its payments with test keys, and the check failed on every one of them, so `preflight --deploy` stopped each deploy there and laravel-health kept the check on failed. It holds in production alone now, and a production server still fails with the same key. A check of your own that extends `ProductionCheck` can answer for staging in `onStaging()`.

## [0.1.0] - 2026-10-30

Initial release. The package stops a Laravel deploy on a production misconfiguration and keeps checking once the application runs. `php artisan preflight --deploy` runs the checks a deploy can answer, `php artisan env:lint` lints a `.env` file as text, and twenty-seven checks run as laravel-health checks over the `.env` file, the configuration and the installation. No finding carries a value of the application.

[Unreleased]: https://github.com/pushery/production-preflight-for-laravel/compare/v0.8.1...HEAD
[0.8.1]: https://github.com/pushery/production-preflight-for-laravel/compare/v0.8.0...v0.8.1
[0.8.0]: https://github.com/pushery/production-preflight-for-laravel/compare/v0.7.2...v0.8.0
[0.7.2]: https://github.com/pushery/production-preflight-for-laravel/compare/v0.7.1...v0.7.2
[0.7.1]: https://github.com/pushery/production-preflight-for-laravel/compare/v0.7.0...v0.7.1
[0.7.0]: https://github.com/pushery/production-preflight-for-laravel/compare/v0.6.1...v0.7.0
[0.6.1]: https://github.com/pushery/production-preflight-for-laravel/compare/v0.6.0...v0.6.1
[0.6.0]: https://github.com/pushery/production-preflight-for-laravel/compare/v0.5.0...v0.6.0
[0.5.0]: https://github.com/pushery/production-preflight-for-laravel/compare/v0.4.0...v0.5.0
[0.4.0]: https://github.com/pushery/production-preflight-for-laravel/compare/v0.3.0...v0.4.0
[0.3.0]: https://github.com/pushery/production-preflight-for-laravel/compare/v0.2.0...v0.3.0
[0.2.0]: https://github.com/pushery/production-preflight-for-laravel/compare/v0.1.0...v0.2.0
[0.1.0]: https://github.com/pushery/production-preflight-for-laravel/releases/tag/v0.1.0
