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
 *
 * The `session` store fails for the same reasons. It keeps the cache in the session of whoever sent
 * the request, so a throttle counts per visitor and starts over for a client without a cookie; it
 * holds no lock, so `Cache::lock()` and a unique job end in an error; and a command, `queue:restart`
 * among them, writes into a session nobody saves.
 *
 * A `failover` store is judged by the stores it names: by the first, which serves while it works, as
 * a store of its own, and with a warning where a later one it falls back to keeps nothing past the
 * request or lives in the session.
 */
final class CacheStoreCheck extends ProductionCheck
{
    /**
     * The drivers whose store keeps nothing past the request.
     *
     * @var list<string>
     */
    private const array TRANSIENT_DRIVERS = ['array', 'null'];

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
        $drivers = $this->cacheDrivers();
        $first = $drivers[0] ?? null;

        if ($first === null) {
            return Inspection::fail($this->message('cache-store.fail'), [new Finding('CACHE_STORE', $this->message('cache-store.unconfigured'))]);
        }

        if (in_array($first, self::TRANSIENT_DRIVERS, true)) {
            return Inspection::fail($this->message('cache-store.fail'), [new Finding('CACHE_STORE', $this->message('cache-store.transient'))]);
        }

        if ($first === 'session') {
            return Inspection::fail($this->message('cache-store.fail_session'), [new Finding('CACHE_STORE', $this->message('cache-store.session'))]);
        }

        $fallbacks = array_slice($drivers, 1);

        if (array_intersect($fallbacks, [...self::TRANSIENT_DRIVERS, 'session']) !== []) {
            return Inspection::warn($this->message('cache-store.warn'), [new Finding('CACHE_STORE', $this->message('cache-store.falls_back'))]);
        }

        return Inspection::pass($this->message('cache-store.pass'));
    }
}
