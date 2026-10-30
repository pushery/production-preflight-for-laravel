<?php

declare(strict_types=1);

namespace Pushery\ProductionPreflight\Checks\Host;

use Illuminate\Contracts\Foundation\CachesConfiguration;
use Override;
use Pushery\ProductionPreflight\Checks\ProductionCheck;
use Pushery\ProductionPreflight\Finding;
use Pushery\ProductionPreflight\Inspection;
use Pushery\ProductionPreflight\Scope;

/**
 * A cached configuration that is older than the `.env` beside it.
 *
 * Somebody changed a value on the server and forgot `config:cache`. With the configuration cached,
 * Laravel does not read `.env` at all, so the application runs on the old value, and nothing says
 * so. Two modification times answer it.
 *
 * This is one of the two states that arise after the go-live rather than at it, which is why a
 * deploy gate cannot catch them and this check runs on the server, on a schedule.
 */
final class ConfigCacheFreshnessCheck extends ProductionCheck
{
    #[Override]
    public function key(): string
    {
        return 'config-cache-fresh';
    }

    #[Override]
    public function scope(): Scope
    {
        return Scope::Host;
    }

    #[Override]
    protected function inspectProduction(): Inspection
    {
        $app = $this->app;

        // The file is asked, not configurationIsCached(): that one answers whether this process
        // booted from the cache and keeps its first answer, while every process started after
        // `config:cache` loads the file.
        if (! $app instanceof CachesConfiguration || ! is_file($app->getCachedConfigPath())) {
            return Inspection::pass($this->message('config-cache-fresh.not_cached'));
        }

        $env = $this->envPath();

        if (! is_file($env)) {
            return Inspection::pass($this->message('config-cache-fresh.no_env'));
        }

        // Both files exist at this point, so neither time is false short of a file removed in
        // between, and that case is answered on the next run.
        return (int) filemtime($env) > (int) filemtime($app->getCachedConfigPath())
            ? Inspection::fail($this->message('config-cache-fresh.fail'), [new Finding('.env', $this->message('config-cache-fresh.finding'))])
            : Inspection::pass($this->message('config-cache-fresh.pass'));
    }
}
