<?php

declare(strict_types=1);

namespace Pushery\ProductionPreflight\Checks\Configuration;

use Override;
use Pushery\ProductionPreflight\Checks\ProductionCheck;
use Pushery\ProductionPreflight\Finding;
use Pushery\ProductionPreflight\Inspection;
use Pushery\ProductionPreflight\Scope;

/**
 * A default cache store that keeps nothing past the request.
 *
 * More hangs on the cache than speed. The rate limiters count in it, so a login throttle counts to one
 * and starts over; atomic locks live in it, so two processes both get the lock; and `queue:restart`
 * leaves its signal there, so no worker ever hears it. That is why the `array` store fails here, not
 * only the `null` one.
 */
final class CacheStoreCheck extends ProductionCheck
{
    #[Override]
    public function key(): string
    {
        return 'cache-store';
    }

    #[Override]
    public function scope(): Scope
    {
        return Scope::Configuration;
    }

    #[Override]
    protected function inspectProduction(): Inspection
    {
        return match ($this->defaultDriver('cache.default', 'cache.stores')) {
            null => Inspection::fail($this->message('cache-store.fail'), [new Finding('CACHE_STORE', $this->message('cache-store.unconfigured'))]),
            'array', 'null' => Inspection::fail($this->message('cache-store.fail'), [new Finding('CACHE_STORE', $this->message('cache-store.transient'))]),
            default => Inspection::pass($this->message('cache-store.pass')),
        };
    }
}
