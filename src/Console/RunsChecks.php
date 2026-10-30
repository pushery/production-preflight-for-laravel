<?php

declare(strict_types=1);

namespace Pushery\ProductionPreflight\Console;

use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Contracts\Translation\Translator;
use Pushery\ProductionPreflight\Checks\PreflightCheck;
use Pushery\ProductionPreflight\Events\PreflightCompleted;
use Pushery\ProductionPreflight\Inspection;
use Pushery\ProductionPreflight\Verdict;
use Throwable;

/**
 * What both commands share: running checks, printing what they found, and the exit code.
 *
 * The console is the one recipient that may see values, and it sees them only on request, with
 * `--show-values`: the output of a command run by a deploy tool or a scheduler is kept, in a deploy
 * log or a mail, and a `.env` consists of credentials. Without the option the console gets what every
 * other channel gets, the summary and the findings.
 *
 * The exit codes are the deploy's assurance: 0 nothing found, 1 a finding, 2 something could not be
 * checked. A two is never a pass: "nothing found" and "not looked" must not read alike.
 */
trait RunsChecks
{
    /**
     * Run checks, each one isolated: a check that throws is reported as not checked, under the name
     * of the exception alone, since its message may hold a value.
     *
     * @param  list<PreflightCheck>  $checks
     * @return list<array{check: PreflightCheck, inspection: Inspection}>
     */
    private function inspect(array $checks): array
    {
        $done = [];

        foreach ($checks as $check) {
            try {
                $inspection = $check->inspection();
            } catch (Throwable $throwable) {
                $inspection = Inspection::undetermined($this->text('command.crashed', ['exception' => $throwable::class]));
            }

            $done[] = ['check' => $check, 'inspection' => $inspection];
        }

        return $done;
    }

    /**
     * Print one check: its verdict, label and summary, its findings, and its evidence on request.
     */
    private function report(PreflightCheck $check, Inspection $inspection, bool $values): void
    {
        $style = match ($inspection->verdict) {
            Verdict::Pass => 'info',
            Verdict::Warn => 'comment',
            Verdict::Fail => 'error',
            Verdict::Undetermined => 'question',
        };

        $this->line(sprintf('<%s>[%s]</%s> %s: %s', $style, $this->text('verdict.'.$inspection->verdict->value), $style, $check->getLabel(), $inspection->summary));

        foreach ($inspection->findings as $finding) {
            $this->line('  - '.$finding->describe());
        }

        if ($values) {
            foreach ($inspection->evidence as $evidence) {
                $this->line('    '.$this->text('command.evidence', ['evidence' => $evidence->describe()]));
            }
        }
    }

    /**
     * The exit code of a run: 1 when a check failed, or warned where warnings count; 2 when a check
     * could not look; 0 otherwise.
     *
     * @param  list<array{check: PreflightCheck, inspection: Inspection}>  $done
     */
    private function exitCode(array $done, bool $warningsCount): int
    {
        $verdicts = [];

        foreach ($done as $entry) {
            $verdicts[] = $entry['inspection']->verdict;
        }

        return match (true) {
            in_array(Verdict::Fail, $verdicts, true), $warningsCount && in_array(Verdict::Warn, $verdicts, true) => 1,
            in_array(Verdict::Undetermined, $verdicts, true) => 2,
            default => 0,
        };
    }

    /**
     * Tell the application what the run found, through laravel-health's results: no evidence.
     *
     * @param  list<array{check: PreflightCheck, inspection: Inspection}>  $done
     */
    private function announce(string $command, array $done, int $exitCode): void
    {
        $results = [];

        foreach ($done as $entry) {
            $results[$entry['check']->getName()] = $entry['check']->result($entry['inspection']);
        }

        $this->laravel->make(Dispatcher::class)->dispatch(new PreflightCompleted($command, $results, $exitCode));
    }

    /**
     * A line of this package's translations.
     *
     * @param  array<string, string|int>  $replace
     */
    private function text(string $key, array $replace = []): string
    {
        $line = $this->laravel->make(Translator::class)->get('production-preflight::messages.'.$key, $replace);

        return is_string($line) ? $line : $key;
    }
}
