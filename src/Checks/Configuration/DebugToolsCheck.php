<?php

declare(strict_types=1);

namespace Pushery\ProductionPreflight\Checks\Configuration;

use Barryvdh\Debugbar\ServiceProvider;
use Laravel\Telescope\TelescopeServiceProvider;
use Override;
use Pushery\ProductionPreflight\Checks\ProductionCheck;
use Pushery\ProductionPreflight\Finding;
use Pushery\ProductionPreflight\Inspection;
use Pushery\ProductionPreflight\Scope;
use Spatie\LaravelIgnition\IgnitionServiceProvider;

/**
 * A debugging tool loaded in production: an open view into the application.
 *
 * Asked of the application, not of the disk: a tool counts when its service provider is registered,
 * which is what a `--no-dev` install leaves out.
 *
 * - Telescope fails while it records, which is its default: requests, queries and mail land in its
 *   tables, and its dashboard stands one gate away from the public.
 * - The Debugbar fails while it is enabled, which by default follows debug mode: it shows every
 *   visitor the queries, the session and the request of each page.
 * - Ignition warns. While debug mode is off it shows nothing, and the moment debug mode is on it is
 *   the open view the app-debug check fails for.
 */
final class DebugToolsCheck extends ProductionCheck
{
    /**
     * The service providers of the Debugbar, under its old and its new vendor.
     *
     * @var list<string>
     */
    public const array DEBUGBAR = [ServiceProvider::class, 'Fruitcake\LaravelDebugbar\ServiceProvider'];

    public const string TELESCOPE = TelescopeServiceProvider::class;

    public const string IGNITION = IgnitionServiceProvider::class;

    #[Override]
    public function key(): string
    {
        return 'debug-tools';
    }

    #[Override]
    public function scope(): Scope
    {
        return Scope::Configuration;
    }

    #[Override]
    protected function inspectProduction(): Inspection
    {
        $fails = [];
        $debugbar = $this->config('debugbar.enabled');

        if ($this->loaded(self::TELESCOPE) && $this->config('telescope.enabled', true) !== false) {
            $fails[] = new Finding('Telescope', $this->message('debug-tools.telescope'));
        }

        if (array_any(self::DEBUGBAR, $this->loaded(...)) && (bool) ($debugbar ?? $this->config('app.debug'))) {
            $fails[] = new Finding('Debugbar', $this->message('debug-tools.debugbar'));
        }

        $warnings = $this->loaded(self::IGNITION) ? [new Finding('Ignition', $this->message('debug-tools.ignition'))] : [];

        if ($fails !== []) {
            return Inspection::fail($this->message('debug-tools.fail'), [...$fails, ...$warnings]);
        }

        return $warnings === []
            ? Inspection::pass($this->message('debug-tools.pass'))
            : Inspection::warn($this->message('debug-tools.warn'), $warnings);
    }

    private function loaded(string $provider): bool
    {
        return $this->app->getProviders($provider) !== [];
    }
}
