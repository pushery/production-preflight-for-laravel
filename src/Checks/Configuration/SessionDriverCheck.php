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

        return is_string($driver) && strtolower($driver) === 'array'
            ? Inspection::fail($this->message('session-driver.fail'), [new Finding('SESSION_DRIVER', $this->message('session-driver.array'))])
            : Inspection::pass($this->message('session-driver.pass'));
    }
}
