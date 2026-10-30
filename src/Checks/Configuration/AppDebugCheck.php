<?php

declare(strict_types=1);

namespace Pushery\ProductionPreflight\Checks\Configuration;

use Override;
use Pushery\ProductionPreflight\Checks\ProductionCheck;
use Pushery\ProductionPreflight\Finding;
use Pushery\ProductionPreflight\Inspection;
use Pushery\ProductionPreflight\Scope;

/**
 * Debug mode in production: an error page then shows the stack trace and the environment, the
 * content of `.env` included, to whoever triggers the error.
 *
 * laravel-health ships a `DebugModeCheck` of its own. This one reads the stage, so the same
 * registration holds on every server, and it runs in the deploy command, which runs only this
 * package's checks. The setting is read the way Laravel reads it, as a truthy value.
 */
final class AppDebugCheck extends ProductionCheck
{
    #[Override]
    public function key(): string
    {
        return 'app-debug';
    }

    #[Override]
    public function scope(): Scope
    {
        return Scope::Configuration;
    }

    #[Override]
    protected function inspectProduction(): Inspection
    {
        return (bool) $this->config('app.debug')
            ? Inspection::fail($this->message('app-debug.fail'), [new Finding('APP_DEBUG', $this->message('app-debug.finding'))])
            : Inspection::pass($this->message('app-debug.pass'));
    }
}
