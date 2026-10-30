<?php

declare(strict_types=1);

namespace Pushery\ProductionPreflight;

/**
 * What a person at the terminal needs to act on a finding, values included: which line holds which
 * value, and which one is in effect.
 *
 * Only the console prints it. It never reaches a laravel-health result, a log entry or a
 * notification, and the guard over the shipped code holds that: outside the checks that make it and
 * the commands that print it, no class refers to it.
 */
final readonly class Evidence
{
    public function __construct(
        public string $subject,
        public string $detail,
    ) {}

    /**
     * The evidence as one line, subject first.
     */
    public function describe(): string
    {
        return $this->subject.': '.$this->detail;
    }
}
