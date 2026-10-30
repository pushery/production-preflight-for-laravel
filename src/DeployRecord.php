<?php

declare(strict_types=1);

namespace Pushery\ProductionPreflight;

use Illuminate\Contracts\Foundation\Application;

/**
 * When the last deploy ran, as `preflight --deploy` records it.
 *
 * The queue-restart check holds the workers' restart signal against this time: a signal older than
 * the deploy means the workers still run the code from before it. A file under `storage/framework`
 * rather than the cache, because a deploy that clears the cache would clear the record with it, and
 * because storage is shared between the releases of a zero-downtime deploy.
 */
final class DeployRecord
{
    public const string FILE = 'framework/production-preflight-deploy';

    public static function path(Application $app): string
    {
        return $app->storagePath(self::FILE);
    }

    /**
     * Record a deploy at a time, in seconds since the epoch; false when the file cannot be written.
     */
    public static function write(Application $app, int $time): bool
    {
        return @file_put_contents(self::path($app), (string) $time) !== false;
    }

    /**
     * The time of the last recorded deploy, or null when none is on record.
     */
    public static function read(Application $app): ?int
    {
        $path = self::path($app);
        $content = is_file($path) ? file_get_contents($path) : false;

        return is_string($content) && preg_match('/^\d+$/', trim($content)) === 1 ? (int) trim($content) : null;
    }
}
