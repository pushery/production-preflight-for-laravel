<?php

declare(strict_types=1);

namespace Pushery\ProductionPreflight\Checks\Configuration;

use Override;
use Pushery\ProductionPreflight\Checks\ProductionCheck;
use Pushery\ProductionPreflight\Finding;
use Pushery\ProductionPreflight\Inspection;
use Pushery\ProductionPreflight\Scope;

/**
 * A default queue connection that runs no worker.
 *
 * The `null` driver discards every job it is given, so it fails. The `sync` driver runs each job
 * inside the request that dispatches it, so every response waits for the work. On a one-person
 * installation that is a decision rather than a defect, and failing it there would get the check
 * switched off; so it warns, and an installation that chose it acknowledges it, where the reason
 * then stands in every report.
 */
final class QueueConnectionCheck extends ProductionCheck
{
    #[Override]
    public function key(): string
    {
        return 'queue-connection';
    }

    #[Override]
    public function scope(): Scope
    {
        return Scope::Configuration;
    }

    #[Override]
    protected function inspectProduction(): Inspection
    {
        return match ($this->defaultDriver('queue.default', 'queue.connections')) {
            null => Inspection::fail($this->message('queue-connection.fail'), [new Finding('QUEUE_CONNECTION', $this->message('queue-connection.unconfigured'))]),
            'null' => Inspection::fail($this->message('queue-connection.fail'), [new Finding('QUEUE_CONNECTION', $this->message('queue-connection.null'))]),
            'sync' => Inspection::warn($this->message('queue-connection.warn'), [new Finding('QUEUE_CONNECTION', $this->message('queue-connection.sync'))]),
            default => Inspection::pass($this->message('queue-connection.pass')),
        };
    }
}
