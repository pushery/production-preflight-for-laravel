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
    /**
     * The queue drivers that run a job without a worker: `sync` inside the request that dispatches
     * it, `deferred` in the same process once the response is sent, `background` in a PHP process
     * started for the job, and `null` not at all.
     *
     * @var list<string>
     */
    protected const array QUEUE_DRIVERS_WITHOUT_WORKER = ['sync', 'deferred', 'background', 'null'];

    /**
     * How deep `failover` queue connections and cache stores are followed into each other.
     */
    private const int FAILOVER_DEPTH = 3;

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
     *
     * @param  mixed  $default  what the caller takes where the key is unset, of the type it reads the setting as
     * @return mixed the setting as the configuration holds it, which a host may write as any type
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
     * A line of this package's translations with a form for one and a form for several, the one the
     * application's locale takes for a count. The count is offered to the line as `:count`.
     *
     * @param  array<string, string|int>  $replace
     */
    protected function messageChoice(string $key, int $count, array $replace = []): string
    {
        return $this->app->make(Translator::class)->choice('production-preflight::messages.'.$key, $count, ['count' => $count, ...$replace]);
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
     *
     * A file within the application is named from there, so the directories above it, which carry
     * the name of the site or of a user on most servers, stay out of the result. A file the
     * configuration places elsewhere keeps its path, the only name that tells which file it is.
     */
    protected function unreadable(string $path): Inspection
    {
        return Inspection::undetermined($this->message('unreadable', ['path' => $this->relative($path)]));
    }

    /**
     * The driver of the entry a default setting names, `cache.default` within `cache.stores` say,
     * lower-cased; null when either is not configured.
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
     * The drivers the default queue connection hands its jobs to, lower-cased, in the order it tries
     * them: its own, or for a `failover` connection those of the connections it names, each of which
     * takes a job when the ones before it fail. Empty when the default names no configured connection.
     *
     * @return list<string>
     */
    protected function queueDrivers(): array
    {
        $name = $this->config('queue.default');
        $connections = $this->config('queue.connections');

        return is_string($name) && is_array($connections) ? $this->failoverDrivers($name, $connections, 'connections', 0) : [];
    }

    /**
     * The drivers of the default cache store, lower-cased, in the order it tries them: its own, or for
     * a `failover` store those of the stores it names, each of which serves when the ones before it
     * fail. Empty when the default names no configured store.
     *
     * @return list<string>
     */
    protected function cacheDrivers(): array
    {
        $name = $this->config('cache.default');
        $stores = $this->config('cache.stores');

        return is_string($name) && is_array($stores) ? $this->failoverDrivers($name, $stores, 'stores', 0) : [];
    }

    /**
     * Whether the default queue connection runs its jobs without a worker. A `failover` connection
     * needs one as soon as one of its connections does, since a job reaches that one whenever the
     * connections before it fail.
     */
    protected function queueRunsWithoutWorker(): bool
    {
        $drivers = $this->queueDrivers();

        return $drivers !== [] && array_diff($drivers, self::QUEUE_DRIVERS_WITHOUT_WORKER) === [];
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

    /**
     * The drivers of one queue connection or cache store, one with the `failover` driver read through
     * the entries it names under its key of members, `connections` or `stores`, as deep as
     * FAILOVER_DEPTH.
     *
     * @param  array<array-key, mixed>  $entries
     * @return list<string>
     */
    private function failoverDrivers(string $name, array $entries, string $members, int $depth): array
    {
        $entry = $entries[$name] ?? null;

        if (! is_array($entry) || $depth > self::FAILOVER_DEPTH) {
            return [];
        }

        $driver = $entry['driver'] ?? null;

        if (! is_string($driver)) {
            return [];
        }

        $driver = strtolower($driver);

        if ($driver !== 'failover') {
            return [$driver];
        }

        $named = $entry[$members] ?? null;
        $drivers = [];

        foreach (is_array($named) ? $named : [] as $member) {
            if (is_string($member)) {
                $drivers = [...$drivers, ...$this->failoverDrivers($member, $entries, $members, $depth + 1)];
            }
        }

        return $drivers;
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
