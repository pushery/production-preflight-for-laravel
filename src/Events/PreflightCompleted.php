<?php

declare(strict_types=1);

namespace Pushery\ProductionPreflight\Events;

use Spatie\Health\Checks\Result;
use Spatie\Health\Enums\Status;

/**
 * A run of `env:lint` or `preflight` has finished, with what each check found.
 *
 * This is the way into an application's own monitoring: a listener forwards the results wherever the
 * application reports its scheduled runs, so the alert names the keys instead of carrying an exit code
 * alone. The results are laravel-health's, built from the summary and the findings of each check, so
 * no value of the application reaches a listener.
 */
final readonly class PreflightCompleted
{
    /**
     * @param  string  $command  `env:lint` or `preflight`
     * @param  array<string, Result>  $results  by check name, in the order of the report
     * @param  int  $exitCode  0 nothing found, 1 a finding, 2 something not checked
     */
    public function __construct(
        public string $command,
        public array $results,
        public int $exitCode,
    ) {}

    /**
     * Every result that is not a pass, one paragraph each, ready to forward.
     */
    public function message(): string
    {
        $paragraphs = [];

        foreach ($this->results as $name => $result) {
            if (! $result->status->equals(Status::ok())) {
                $paragraphs[] = $name.': '.$result->notificationMessage;
            }
        }

        return implode("\n\n", $paragraphs);
    }
}
