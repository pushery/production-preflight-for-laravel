<?php

declare(strict_types=1);

namespace Pushery\ProductionPreflight\Checks\Configuration;

use Override;
use Pushery\ProductionPreflight\Checks\ProductionCheck;
use Pushery\ProductionPreflight\Finding;
use Pushery\ProductionPreflight\Inspection;
use Pushery\ProductionPreflight\Scope;

/**
 * A session driver that keeps a session for one request: every user is signed out after each page.
 * Or no session driver at all, where the first request that starts a session raises an error.
 */
final class SessionDriverCheck extends ProductionCheck
{
    #[Override]
    public function key(): string
    {
        return 'session-driver';
    }

    #[Override]
    public function scope(): Scope
    {
        return Scope::Configuration;
    }

    #[Override]
    protected function inspectProduction(): Inspection
    {
        $driver = $this->config('session.driver');

        // `SESSION_DRIVER=null` loads as null and an empty line as '', and Laravel's session manager
        // resolves neither: the first request that starts a session raises an error.
        if (! is_string($driver) || trim($driver) === '') {
            return Inspection::fail($this->message('session-driver.fail'), [new Finding('SESSION_DRIVER', $this->message('session-driver.unconfigured'))]);
        }

        return strtolower($driver) === 'array'
            ? Inspection::fail($this->message('session-driver.fail'), [new Finding('SESSION_DRIVER', $this->message('session-driver.array'))])
            : Inspection::pass($this->message('session-driver.pass'));
    }
}
