<?php

declare(strict_types=1);

namespace Pushery\ProductionPreflight;

use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Foundation\CachesConfiguration;
use Illuminate\Contracts\Translation\Loader;
use Illuminate\Support\ServiceProvider;
use Override;
use Pushery\ProductionPreflight\Console\EnvLintCommand;
use Pushery\ProductionPreflight\Console\PreflightCommand;
use Pushery\ProductionPreflight\Support\LanguageFallbackLoader;

final class ProductionPreflightServiceProvider extends ServiceProvider
{
    /**
     * The sections of the config that are catalogs, as dotted paths from the config key: maps whose
     * keys the host chooses, one entry per plan, per document or per signing key. A key that a
     * published catalog leaves out was removed by the host rather than added by a later release, so
     * the merge takes a published catalog as it stands. A catalog the host never published arrives
     * whole from the default. A package lists such a section here, for example
     * 'production-preflight.plans'.
     *
     * @var list<string>
     */
    private const array CATALOG_SECTIONS = [];

    #[Override]
    public function register(): void
    {
        $this->mergeConfigRecursivelyFrom(__DIR__.'/../config/production-preflight.php', 'production-preflight');

        // A regional locale reads this package's catalog of its language, `pt-BR` from `pt`, in this package's
        // namespace only. Registered here so it wraps the loader before the translator is built from it.
        $this->app->extend('translation.loader', static fn (Loader $loader): Loader => new LanguageFallbackLoader($loader, 'production-preflight'));
    }

    public function boot(): void
    {
        $this->loadTranslationsFrom(__DIR__.'/../lang', 'production-preflight');

        if ($this->app->runningInConsole()) {
            $this->commands([EnvLintCommand::class, PreflightCommand::class]);
            $this->registerPublishing();
        }
    }

    /**
     * Lay the shipped defaults under a published config file, all the way down.
     *
     * Laravel's own `mergeConfigFrom()` is a single `array_merge` at the top level, so it asks
     * one question per top-level key: is it there? A host that published the config file has
     * every top-level key, so a key that a later release adds inside one of those blocks would
     * never arrive, and the block the host published would win whole.
     *
     * Nothing would error or log: the new setting would read as `null` (or as `[]`). A feature
     * added in a minor release would be off for exactly the hosts that customized that area, and
     * a corrected security default would not take effect, for as long as the host keeps the file
     * it published.
     *
     * Recursion stops at a list on either side, because a list is a value, not a structure.
     * `array_merge_recursive()` concatenates lists and `array_replace_recursive()` merges them by
     * index: a host narrowing a shipped `['web', 'api']` to `['admin']` gets back
     * `['web', 'api', 'admin']` from the first and `['admin', 'api']` from the second. Both hand
     * back an entry the host removed, which on an allowlist is a security regression. Here the
     * published list stands as written.
     *
     * `array_is_list([])` is true, which is the behavior wanted here: an empty array is a host
     * saying "none", and descending into it could only re-introduce what it emptied.
     *
     * Recursion stops at a catalog as well, a section named in CATALOG_SECTIONS. Its keys are the
     * host's, so a key missing from the published catalog is one the host removed, and laying the
     * default under it would hand that entry back.
     *
     * A host with a cached config is not merged at runtime: the cache already holds the merge
     * `config:cache` did when it ran. A key a later release adds is missing from it until the host
     * caches again, so a package that reads a nested key needs its read-site fallback to agree with
     * the shipped default.
     */
    private function mergeConfigRecursivelyFrom(string $path, string $key): void
    {
        if ($this->app instanceof CachesConfiguration && $this->app->configurationIsCached()) {
            return;
        }

        $shipped = require $path;

        $repository = $this->app->make(Repository::class);
        $existing = $repository->get($key);

        // Both sides come off disk or out of the container, so neither is provably
        // string-keyed here — a config array is just an array. The recursion below is
        // written for exactly that: it asks whether a value is a LIST, never whether a key
        // is a string.
        $repository->set($key, $this->mergeConfigSections(
            is_array($shipped) ? $shipped : [],
            is_array($existing) ? $existing : [],
            $key.'.',
            self::CATALOG_SECTIONS,
        ));
    }

    /**
     * @param  array<array-key, mixed>  $shipped
     * @param  array<array-key, mixed>  $published
     * @param  string  $path  the dotted path of the two maps, so a catalog is recognized at any depth
     * @param  list<string>  $catalogs  the dotted paths of the catalogs, which the merge takes as published
     * @return array<array-key, mixed>
     */
    private function mergeConfigSections(array $shipped, array $published, string $path = '', array $catalogs = []): array
    {
        foreach ($shipped as $key => $value) {
            if (! array_key_exists($key, $published)) {
                $published[$key] = $value;

                continue;
            }

            // Recurse only where both sides are maps and the section is no catalog. If either is a
            // list, or the published value is a scalar or an explicit null, what the host wrote
            // stands. An explicit null is a value like any other here, which keeps "write => null
            // to switch this off" working exactly as a shipped config file describes it.
            if (is_array($value) && is_array($published[$key]) && ! array_is_list($value) && ! array_is_list($published[$key]) && ! in_array($path.$key, $catalogs, true)) {
                $published[$key] = $this->mergeConfigSections($value, $published[$key], $path.$key.'.', $catalogs);
            }
        }

        return $published;
    }

    private function registerPublishing(): void
    {
        // Resolve publish targets through the Application contract's path methods
        // (available via illuminate/contracts), NOT the config_path()/database_path()/
        // resource_path()/lang_path() global helpers. Those are Foundation helpers,
        // shipped ONLY with laravel/framework — which this lean package does not
        // require — so the helper form would freeze a wrong dependency contract and
        // fatal in a non-Foundation host. The method form is behavior-identical.
        //
        // Each group carries the bare 'production-preflight' umbrella tag on top of its
        // specific one, so `vendor:publish --tag="production-preflight"` publishes every
        // resource at once — the tag convention Laravel's official package skeleton
        // establishes.
        //
        // The umbrella covers what a host adapts by editing a file: config and lang. It has
        // two exceptions, and a package can grow into either without a warning, because
        // publishing more is not an error anywhere:
        //
        //   * Migration tags split per opt-in layer. A published migration runs. A package
        //     that separates its migration tags does so that a host which enabled one layer
        //     never creates the other layers' tables. One umbrella command would undo that,
        //     and the damage would show at the host's next `php artisan migrate`, not at
        //     publish time. This package ships no migrations, so the question does not arise.
        //   * Tags that write to the same destination and exclude each other, such as two
        //     variants of one view set of which the host picks one. Under a common tag the
        //     registration order decides which survives, and the result is undefined.
        $this->publishes([
            __DIR__.'/../config/production-preflight.php' => $this->app->configPath('production-preflight.php'),
        ], ['production-preflight', 'production-preflight-config']);

        $this->publishes([
            __DIR__.'/../lang' => $this->app->langPath('vendor/production-preflight'),
        ], ['production-preflight', 'production-preflight-lang']);
    }
}
