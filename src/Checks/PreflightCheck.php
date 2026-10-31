<?php

declare(strict_types=1);

namespace Pushery\ProductionPreflight\Checks;

use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Translation\Translator;
use Override;
use Pushery\ProductionPreflight\Env\EnvFile;
use Pushery\ProductionPreflight\Inspection;
use Pushery\ProductionPreflight\Preflight;
use Pushery\ProductionPreflight\Scope;
use Pushery\ProductionPreflight\Stage;
use Pushery\ProductionPreflight\Verdict;
use Spatie\Health\Checks\Check;
use Spatie\Health\Checks\Result;

/**
 * A check of this package, in the shape laravel-health runs.
 *
 * A subclass says what it found as an Inspection; this class turns that into the Result
 * laravel-health stores, notifies and serves. Only the summary and the findings go into the
 * Result, never the evidence: a finding has no field a value could go into, and every channel
 * laravel-health feeds keeps what it is given.
 *
 * An application acknowledges a check by its key under `production-preflight.acknowledged`, with
 * the reason. The check then passes, and its summary names the reason and what was found, so the
 * acknowledgement stands in every report instead of turning into silence.
 */
abstract class PreflightCheck extends Check
{
    public function __construct(protected readonly Application $app) {}

    /**
     * Whether the check can look in this process and with this configuration.
     *
     * A check that cannot answers undetermined every time it runs here. laravel-health stores that as
     * skipped, and its default `treat_skipped_as_failure` counts it as a failure, so
     * {@see Preflight::healthChecks()} leaves such a check out. One that
     * could have looked and did not still answers undetermined, and that is a finding.
     */
    public function canLookHere(): bool
    {
        return true;
    }

    /**
     * The name the check answers to, in laravel-health and under `production-preflight.acknowledged`.
     */
    abstract public function key(): string;

    /**
     * What the check needs in order to answer, which decides when it runs.
     */
    abstract public function scope(): Scope;

    abstract protected function inspect(): Inspection;

    /**
     * What the check found, after the acknowledgement the application declared for it.
     */
    final public function inspection(): Inspection
    {
        $inspection = $this->inspect();
        $reason = $this->acknowledgement();

        if ($reason === null || $inspection->verdict === Verdict::Pass) {
            return $inspection;
        }

        return $inspection->acknowledged(
            $this->message('acknowledged', ['reason' => $reason, 'summary' => $inspection->summary]),
            $reason,
        );
    }

    #[Override]
    final public function run(): Result
    {
        return $this->result($this->inspection());
    }

    /**
     * The Result laravel-health keeps for an inspection of this check: the summary and the findings,
     * and nothing of the evidence.
     */
    final public function result(Inspection $inspection): Result
    {
        $findings = [];

        foreach ($inspection->findings as $finding) {
            $findings[] = $finding->toArray();
        }

        $meta = ['findings' => $findings];

        if ($inspection->acknowledgement !== null) {
            $meta['acknowledged'] = $inspection->acknowledgement;
        }

        return new Result($inspection->verdict->status(), $inspection->message(), $this->message('verdict.'.$inspection->verdict->value))
            ->meta($meta);
    }

    #[Override]
    public function getName(): string
    {
        return $this->name ?? $this->key();
    }

    #[Override]
    public function getLabel(): string
    {
        return $this->label ?? $this->message($this->key().'.label');
    }

    /**
     * A setting of the application's configuration.
     */
    protected function config(string $key, mixed $default = null): mixed
    {
        return $this->app->make(Repository::class)->get($key, $default);
    }

    /**
     * A line of this package's translations, in the application's locale.
     *
     * @param  array<string, string|int>  $replace
     */
    protected function message(string $key, array $replace = []): string
    {
        $line = $this->app->make(Translator::class)->get('production-preflight::messages.'.$key, $replace);

        return is_string($line) ? $line : $key;
    }

    /**
     * The stage the application declares in `app.env`.
     */
    protected function stage(): Stage
    {
        return Stage::named($this->config('app.env'), $this->config('production-preflight.stages'));
    }

    /**
     * The `.env` the application loads, or the file `production-preflight.env.file` names.
     */
    protected function envPath(): string
    {
        return $this->path('production-preflight.env.file', '.env');
    }

    /**
     * The example file kept beside the `.env`, or the one `production-preflight.env.example` names.
     */
    protected function examplePath(): string
    {
        return $this->path('production-preflight.env.example', '.env.example');
    }

    protected function envFile(): ?EnvFile
    {
        return EnvFile::read($this->envPath());
    }

    /**
     * The answer of a check whose file cannot be read: it did not look, so it does not pass.
     */
    protected function unreadable(string $path): Inspection
    {
        return Inspection::undetermined($this->message('unreadable', ['path' => $path]));
    }

    /**
     * The driver of the entry a default setting names, `queue.default` within `queue.connections`
     * say, lower-cased; null when either is not configured.
     */
    protected function defaultDriver(string $default, string $entries): ?string
    {
        $name = $this->config($default);
        $configured = $this->config($entries);
        $entry = is_string($name) && is_array($configured) ? ($configured[$name] ?? null) : null;
        $driver = is_array($entry) ? ($entry['driver'] ?? null) : null;

        return is_string($driver) ? strtolower($driver) : null;
    }

    /**
     * A path below the application's base path, written from there; any other path as it is.
     */
    protected function relative(string $path): string
    {
        $base = rtrim($this->app->basePath(), '/').'/';

        return str_starts_with($path, $base) ? substr($path, strlen($base)) : $path;
    }

    /**
     * A value as the console shows it, where an empty one would print as nothing at all.
     */
    protected function shown(string $value): string
    {
        return $value === '' ? $this->message('empty') : $value;
    }

    private function path(string $setting, string $default): string
    {
        $configured = $this->config($setting);

        return is_string($configured) && $configured !== '' ? $configured : $this->app->basePath($default);
    }

    private function acknowledgement(): ?string
    {
        $acknowledged = $this->config('production-preflight.acknowledged');
        $reason = is_array($acknowledged) ? ($acknowledged[$this->key()] ?? null) : null;

        return is_string($reason) && trim($reason) !== '' ? trim($reason) : null;
    }
}
