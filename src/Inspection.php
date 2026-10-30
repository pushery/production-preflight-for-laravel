<?php

declare(strict_types=1);

namespace Pushery\ProductionPreflight;

/**
 * What one check found: its verdict, a summary, the findings that carry no value, and the evidence
 * that does.
 *
 * The summary and the findings are what laravel-health stores, notifies and serves, so they are
 * written without a value of the application. The evidence goes to the console alone.
 */
final readonly class Inspection
{
    /**
     * @param  list<Finding>  $findings
     * @param  list<Evidence>  $evidence
     */
    private function __construct(
        public Verdict $verdict,
        public string $summary,
        public array $findings = [],
        public array $evidence = [],
        public ?string $acknowledgement = null,
    ) {}

    public static function pass(string $summary): self
    {
        return new self(Verdict::Pass, $summary);
    }

    /**
     * @param  list<Finding>  $findings
     * @param  list<Evidence>  $evidence
     */
    public static function warn(string $summary, array $findings, array $evidence = []): self
    {
        return new self(Verdict::Warn, $summary, $findings, $evidence);
    }

    /**
     * @param  list<Finding>  $findings
     * @param  list<Evidence>  $evidence
     */
    public static function fail(string $summary, array $findings, array $evidence = []): self
    {
        return new self(Verdict::Fail, $summary, $findings, $evidence);
    }

    /**
     * A check that could not look, with the reason: never a pass.
     *
     * @param  list<Evidence>  $evidence
     */
    public static function undetermined(string $reason, array $evidence = []): self
    {
        return new self(Verdict::Undetermined, $reason, [], $evidence);
    }

    /**
     * The same inspection after the application acknowledged what it found: it passes under a
     * summary that names the reason the application gave, and the findings stay, so the
     * acknowledgement shows in every report rather than turning into silence.
     */
    public function acknowledged(string $summary, string $reason): self
    {
        return new self(Verdict::Pass, $summary, $this->findings, $this->evidence, $reason);
    }

    /**
     * The summary and every finding, one line each, the way an alert carries them: a finding has to
     * stand in the alert, not in a log the alert points at.
     */
    public function message(): string
    {
        return implode("\n", [$this->summary, ...array_map(static fn (Finding $finding): string => '- '.$finding->describe(), $this->findings)]);
    }
}
