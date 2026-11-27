<?php

declare(strict_types=1);

namespace Pushery\ProductionPreflight;

use Illuminate\Contracts\Foundation\Application;

/**
 * When the last deploy of each release ran, as `preflight --deploy` records it.
 *
 * The queue-restart check holds the workers' restart signal against this time: a signal older than
 * the deploy means the workers still run the code from before it. A file under `storage/framework`
 * rather than the cache, because a deploy that clears the cache would clear the record with it, and
 * because storage is shared between the releases of a zero-downtime deploy.
 *
 * Each release keeps its own line, under the real path of its base directory. `preflight --deploy`
 * runs in the release it deploys, before the releases switch, and the check reads the line of the
 * release it runs in. A deploy that fails after its preflight, at a migration or at the switch,
 * writes the line of a release that never goes live, and the live release keeps its own. A deploy in
 * place writes the line of the one directory it has. A record of a single time, written before each
 * release kept its own, counts for every release.
 */
final class DeployRecord
{
    public const string FILE = 'framework/production-preflight-deploy';

    /**
     * The releases the record keeps at most. The line of a release no longer on the disk goes when the
     * next deploy is recorded, so only a host that keeps every release ever needs the bound.
     */
    private const int RELEASES = 20;

    public static function path(Application $app): string
    {
        return $app->storagePath(self::FILE);
    }

    /**
     * Record a deploy of the release this process runs in, at a time in seconds since the epoch; false
     * when the file cannot be written.
     */
    public static function write(Application $app, int $time): bool
    {
        $release = self::release($app);
        $kept = array_filter(
            self::releases($app),
            static fn (string $other): bool => $other !== $release && is_dir($other),
            ARRAY_FILTER_USE_KEY,
        );
        $content = json_encode(array_slice([$release => $time] + $kept, 0, self::RELEASES, true), JSON_UNESCAPED_SLASHES);

        return $content !== false && @file_put_contents(self::path($app), $content) !== false;
    }

    /**
     * The time of the last recorded deploy of the release this process runs in, or null when none is
     * on record.
     */
    public static function read(Application $app): ?int
    {
        $content = self::content($app);

        if ($content !== null && preg_match('/^\d+\z/', $content) === 1) {
            return (int) $content;
        }

        return self::releases($app)[self::release($app)] ?? null;
    }

    /**
     * The release a process runs in, named by the real path of its base directory, so a link to the
     * live release and the release it points at name the same one.
     */
    private static function release(Application $app): string
    {
        $base = $app->basePath();
        $real = realpath($base);

        return $real === false ? $base : $real;
    }

    /**
     * The recorded time of each release, by its path; empty for a record of a single time or none.
     *
     * @return array<string, int>
     */
    private static function releases(Application $app): array
    {
        $content = self::content($app);
        $decoded = $content === null ? null : json_decode($content, true);

        if (! is_array($decoded)) {
            return [];
        }

        $releases = [];

        foreach ($decoded as $release => $time) {
            if (is_string($release) && is_int($time)) {
                $releases[$release] = $time;
            }
        }

        return $releases;
    }

    /**
     * The record with the whitespace around it trimmed, or null where there is none.
     */
    private static function content(Application $app): ?string
    {
        $path = self::path($app);
        $content = is_file($path) ? file_get_contents($path) : false;

        return is_string($content) ? trim($content) : null;
    }
}
