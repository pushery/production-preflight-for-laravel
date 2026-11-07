# Changelog

All notable changes to `pushery/production-preflight-for-laravel` are documented here. The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/), and the project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

A release carries only the sections that have an entry, in this order: `### ⚠️ Breaking`, `### ✨ Added`, `### 🔧 Changed`, `### ⚡ Performance`, `### 🐛 Fixed`, `### 🔒 Security`, `### 🧹 Deprecated`, `### 🗑️ Removed` and `### 📚 Documentation`.

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

[Unreleased]: https://github.com/pushery/production-preflight-for-laravel/compare/v0.4.0...HEAD
[0.4.0]: https://github.com/pushery/production-preflight-for-laravel/compare/v0.3.0...v0.4.0
[0.3.0]: https://github.com/pushery/production-preflight-for-laravel/compare/v0.2.0...v0.3.0
[0.2.0]: https://github.com/pushery/production-preflight-for-laravel/compare/v0.1.0...v0.2.0
[0.1.0]: https://github.com/pushery/production-preflight-for-laravel/releases/tag/v0.1.0
