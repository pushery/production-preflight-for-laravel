<?php

declare(strict_types=1);

namespace Pushery\ProductionPreflight\Console;

use Illuminate\Console\Command;
use Illuminate\Support\InteractsWithTime;
use Pushery\ProductionPreflight\DeployRecord;
use Pushery\ProductionPreflight\Preflight;
use Pushery\ProductionPreflight\Scope;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Formatter\OutputFormatter;

/**
 * Run the checks of this package and report them, for a deploy or for a person.
 *
 * With `--deploy` it runs what a deploy can answer, the `.env` and the configuration, and stops the
 * deploy before harm is done: exit 1 when a check failed, 2 when a check could not look. A warning is
 * printed and lets the deploy pass. A deploy it lets pass is recorded, so the queue-restart check can
 * tell later whether the workers were restarted after it; so it runs before `php artisan reload`, or
 * `queue:restart`, in the deploy script. A deploy it stops is not recorded. The exit code is the
 * assurance here, not a notification: one of the findings is a mailer that delivers nothing.
 *
 * Without `--deploy` it runs every check, the state of the installation included, which a person on
 * the server wants to see at once and the scheduler reaches through laravel-health.
 */
#[AsCommand(name: 'preflight')]
final class PreflightCommand extends Command
{
    use InteractsWithTime;
    use RunsChecks;

    /**
     * @var string
     */
    protected $signature = 'preflight
        {--deploy : Run the checks a deploy can answer, fail the deploy on a finding, and record a deploy that passes}
        {--show-values : Print the values behind the findings; never in a deploy, whose output is kept}';

    /**
     * @var string
     */
    protected $description = 'Check the application for production settings that pass every test (exit 1 on a failed check, 2 when one could not look)';

    public function handle(): int
    {
        $deploy = $this->option('deploy') === true;
        $values = $this->option('show-values') === true;

        // A deploy keeps its output, so the values would outlive the run in the deploy log.
        if ($deploy && $values) {
            $this->error(OutputFormatter::escape($this->text('command.values_in_deploy')));

            return 2;
        }

        $done = $this->inspect($deploy ? Preflight::checks(Scope::File, Scope::Configuration) : Preflight::checks());
        $exitCode = $this->exitCode($done, false);

        // A deploy this run stops never reaches `reload` or `queue:restart`, and on a zero-downtime
        // deploy the release before it keeps serving, its workers with it. A record of it would make
        // the queue-restart check report those workers as running old code until a deploy passed.
        if ($deploy && $exitCode === 0 && ! DeployRecord::write($this->laravel, $this->currentTime())) {
            $this->warn(OutputFormatter::escape($this->text('command.unrecorded', ['path' => DeployRecord::path($this->laravel)])));
        }

        foreach ($done as $entry) {
            $this->report($entry['check'], $entry['inspection'], $values);
        }

        $this->newLine();
        $this->line(OutputFormatter::escape($this->text('command.exit.'.$exitCode)));
        $this->announce('preflight', $done, $exitCode);

        return $exitCode;
    }
}
