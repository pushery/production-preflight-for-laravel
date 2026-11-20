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
 * inside the request that dispatches it, so every response waits for the work. The `deferred` and
 * `background` drivers run each job after the response, in the same PHP process or in one started for
 * it: once, since a failed job is not tried again, and a job that has not finished is lost when its
 * process ends. On a one-person installation each of these three is a decision rather than a defect,
 * and failing it there would get the check switched off; so it warns, and an installation that chose
 * it acknowledges it, where the reason then stands in every report.
 *
 * A `failover` connection is judged by its first connection, which takes the jobs for as long as it
 * works.
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
        $driver = $this->queueDrivers()[0] ?? null;

        if ($driver !== null && ! in_array($driver, self::QUEUE_DRIVERS_WITHOUT_WORKER, true)) {
            return Inspection::pass($this->message('queue-connection.pass'));
        }

        return match ($driver) {
            null => Inspection::fail($this->message('queue-connection.fail'), [new Finding('QUEUE_CONNECTION', $this->message('queue-connection.unconfigured'))]),
            'null' => Inspection::fail($this->message('queue-connection.fail'), [new Finding('QUEUE_CONNECTION', $this->message('queue-connection.null'))]),
            'sync' => Inspection::warn($this->message('queue-connection.warn'), [new Finding('QUEUE_CONNECTION', $this->message('queue-connection.sync'))]),
            default => Inspection::warn($this->message('queue-connection.unattended'), [new Finding('QUEUE_CONNECTION', $this->message('queue-connection.'.$driver))]),
        };
    }
}
