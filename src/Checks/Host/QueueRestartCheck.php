<?php

declare(strict_types=1);

namespace Pushery\ProductionPreflight\Checks\Host;

use Illuminate\Contracts\Cache\Repository;
use Illuminate\Support\Facades\Date;
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
 * `queue:restart` writes into the default cache store as a time, on its own or as part of
 * `php artisan reload`. A deploy that forgets both leaves the workers on the old code for as long as
 * they live, and nothing shows it: they process jobs, just with yesterday's classes.
 *
 * The check holds that time against the time `preflight --deploy` recorded. So the deploy script runs
 * `preflight --deploy` before `php artisan reload`, or `queue:restart`; without a recorded deploy the
 * check cannot tell and says so. A queue that runs on `sync`, `deferred`, `background` or `null` has
 * no worker to restart, and neither has a `failover` connection made only of such connections.
 *
 * A signal older than the deploy means the workers run the old code. A missing one has two causes
 * nothing tells apart: `queue:restart` never ran, or the cache lost the signal after it, cleared by
 * `cache:clear` or `optimize:clear`, restarted without persistence or evicted. A worker restarts when
 * the signal it started with changes, so in the second case the workers restarted when it
 * disappeared. The check fails, because the first case is the one it exists for, and its finding names
 * both.
 */
final class QueueRestartCheck extends ProductionCheck
{
    /**
     * The cache key `queue:restart` writes and every worker reads.
     */
    public const string SIGNAL = 'illuminate:queue:restart';

    /**
     * The minutes a deploy is given to restart the workers at its end, where the configuration names none.
     */
    public const int GRACE_MINUTES = 15;

    /**
     * Until `preflight --deploy` recorded a deploy there is no moment to hold the restart signal against.
     */
    #[Override]
    public function canLookHere(): bool
    {
        return DeployRecord::read($this->app) !== null;
    }

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
        if ($this->queueRunsWithoutWorker()) {
            return Inspection::pass($this->message('queue-restart.no_worker'));
        }

        $deployed = DeployRecord::read($this->app);

        if ($deployed === null) {
            return Inspection::undetermined($this->message('queue-restart.no_deploy'));
        }

        // preflight --deploy records the deploy before the releases switch, and the release still live reads the record
        // until the deploy script restarts the workers at its end. A record younger than the grace is a deploy that runs.
        // A record ahead of the clock has run for no time at all, so a grace of 0 reports it at once like any other.
        $grace = $this->graceMinutes();

        if (max(0, Date::now()->getTimestamp() - $deployed) < $grace * 60) {
            return Inspection::pass($this->message('queue-restart.within_grace', ['minutes' => $grace]));
        }

        $signal = $this->app->make(Repository::class)->get(self::SIGNAL);

        if (! is_numeric($signal)) {
            return Inspection::fail($this->message('queue-restart.fail_missing'), [new Finding('queue:restart', $this->message('queue-restart.missing'))]);
        }

        return (int) $signal < $deployed
            ? Inspection::fail($this->message('queue-restart.fail'), [new Finding('queue:restart', $this->message('queue-restart.stale'))])
            : Inspection::pass($this->message('queue-restart.pass'));
    }

    /**
     * The minutes from `production-preflight.queue_restart.grace_minutes`, or the shipped 15.
     */
    private function graceMinutes(): int
    {
        $minutes = $this->config('production-preflight.queue_restart.grace_minutes');

        return is_numeric($minutes) && (int) $minutes >= 0 ? (int) $minutes : self::GRACE_MINUTES;
    }
}
