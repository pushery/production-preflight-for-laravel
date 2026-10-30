<?php

declare(strict_types=1);

namespace Pushery\ProductionPreflight\Checks\Host;

use Illuminate\Contracts\Cache\Repository;
use Override;
use Pushery\ProductionPreflight\Checks\ProductionCheck;
use Pushery\ProductionPreflight\DeployRecord;
use Pushery\ProductionPreflight\Finding;
use Pushery\ProductionPreflight\Inspection;
use Pushery\ProductionPreflight\Scope;

/**
 * Queue workers that still run the code from before the last deploy.
 *
 * A worker loads the application once and keeps it until it hears the restart signal, which
 * `queue:restart` writes into the default cache store as a time. A deploy that forgets the command
 * leaves the workers on the old code for as long as they live, and nothing shows it: they process
 * jobs, just with yesterday's classes.
 *
 * The check holds that time against the time `preflight --deploy` recorded. So the deploy script runs
 * `preflight --deploy` before `queue:restart`; without a recorded deploy the check cannot tell and
 * says so. A queue that runs on `sync` or `null` has no worker to restart.
 */
final class QueueRestartCheck extends ProductionCheck
{
    /**
     * The cache key `queue:restart` writes and every worker reads.
     */
    public const string SIGNAL = 'illuminate:queue:restart';

    #[Override]
    public function key(): string
    {
        return 'queue-restart';
    }

    #[Override]
    public function scope(): Scope
    {
        return Scope::Host;
    }

    #[Override]
    protected function inspectProduction(): Inspection
    {
        if (in_array($this->defaultDriver('queue.default', 'queue.connections'), ['sync', 'null'], true)) {
            return Inspection::pass($this->message('queue-restart.no_worker'));
        }

        $deployed = DeployRecord::read($this->app);

        if ($deployed === null) {
            return Inspection::undetermined($this->message('queue-restart.no_deploy'));
        }

        $signal = $this->app->make(Repository::class)->get(self::SIGNAL);

        if (! is_numeric($signal)) {
            return Inspection::fail($this->message('queue-restart.fail'), [new Finding('queue:restart', $this->message('queue-restart.never'))]);
        }

        return (int) $signal < $deployed
            ? Inspection::fail($this->message('queue-restart.fail'), [new Finding('queue:restart', $this->message('queue-restart.stale'))])
            : Inspection::pass($this->message('queue-restart.pass'));
    }
}
