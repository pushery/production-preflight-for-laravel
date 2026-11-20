<?php

declare(strict_types=1);

namespace Pushery\ProductionPreflight\Checks\Host;

use Illuminate\Contracts\Foundation\CachesConfiguration;
use Illuminate\Contracts\Foundation\CachesRoutes;
use Illuminate\Filesystem\Filesystem;
use Override;
use Pushery\ProductionPreflight\Checks\ProductionCheck;
use Pushery\ProductionPreflight\Evidence;
use Pushery\ProductionPreflight\Finding;
use Pushery\ProductionPreflight\Inspection;
use Pushery\ProductionPreflight\Scope;
use Pushery\ProductionPreflight\Support\ProcessUser;

/**
 * The directories Laravel writes to, missing or not writable: the first user action that writes,
 * a session, a cached view, a log line, falls over.
 *
 * Where the configuration points Laravel elsewhere, the check asks there: `view.compiled`, the `path`
 * of a default cache store on the `file` driver, `session.files` under the `file` session driver, and
 * the directories of the cached configuration, services, packages, routes and events, which
 * `APP_CONFIG_CACHE` and its siblings move out of `bootstrap/cache`. Laravel creates the compiled views
 * and the directories of a file cache store on their first write, so a configured one may be missing
 * as long as the directory above it is writable; every other directory has to exist.
 *
 * Asked as the user that runs the check. On most servers the scheduler and the web server run as the
 * same user; where they do not, the answer describes the scheduler's, and the finding says so.
 *
 * The newest log file is asked as well. A log line is appended to the file that exists, and that file
 * keeps the permissions of the process that opened it first. Where two users share the log directory
 * through their group, the directory takes new files from both while the current file takes lines from
 * its owner only: the directory reads as writable, and the next line of the other process is lost.
 *
 * The newest file is looked for one level below the log directory as well, where a channel with a directory
 * of its own writes its file of the day. There that directory is asked too, because the file of the next day is
 * created in it.
 *
 * A failure names the server and the user, and carries both as evidence, `server` and `user`. Where several
 * servers run the scheduler, the check runs on whichever one the minute picked, and a finding without the server
 * leaves open which machine has the wrong permissions. The user comes from the system's user database
 * ({@see ProcessUser}), because the one an environment variable names is often somebody else, and where the
 * system cannot name it, the failure says so.
 */
final class WritableDirectoriesCheck extends ProductionCheck
{
    #[Override]
    public function key(): string
    {
        return 'writable-directories';
    }

    #[Override]
    public function scope(): Scope
    {
        return Scope::Host;
    }

    #[Override]
    protected function inspectProduction(): Inspection
    {
        $files = $this->app->make(Filesystem::class);
        $findings = [];

        foreach ($this->directories() as $directory => $createdOnWrite) {
            $problem = match (true) {
                $files->isDirectory($directory) => $files->isWritable($directory) ? null : 'writable-directories.read_only',
                ! $createdOnWrite => 'writable-directories.missing',
                default => $files->isWritable(dirname($directory)) ? null : 'writable-directories.not_creatable',
            };

            if ($problem !== null) {
                $findings[] = new Finding($this->relative($directory), $this->message($problem));
            }
        }

        $log = $this->newestLog($files);

        if ($log !== null && ! $files->isWritable($log)) {
            $findings[] = new Finding($this->relative($log), $this->message('writable-directories.log_read_only'));
        }

        $channel = $log === null ? null : dirname($log);

        if ($channel !== null && $channel !== $this->app->storagePath('logs') && ! $files->isWritable($channel)) {
            $findings[] = new Finding($this->relative($channel), $this->message('writable-directories.log_directory_read_only'));
        }

        if ($findings === []) {
            return Inspection::pass($this->message('writable-directories.pass'));
        }

        $server = gethostname() ?: php_uname('n');
        $user = $this->app->make(ProcessUser::class)->name();
        $evidence = [new Evidence('server', $server)];

        if ($user !== null) {
            $evidence[] = new Evidence('user', $user);
        }

        return Inspection::fail(
            $user === null
                ? $this->message('writable-directories.fail_unknown_user', ['count' => count($findings), 'server' => $server])
                : $this->message('writable-directories.fail', ['count' => count($findings), 'server' => $server, 'user' => $user]),
            $findings,
            $evidence,
        );
    }

    /**
     * The newest log file, by its modification time, in the log directory or in a directory one level below it,
     * or null where there is none.
     */
    private function newestLog(Filesystem $files): ?string
    {
        $logs = $this->app->storagePath('logs');
        $newest = null;
        $at = -1;

        foreach (array_filter([...$files->glob($logs.'/*.log'), ...$files->glob($logs.'/*/*.log')], is_string(...)) as $log) {
            $modified = $files->isFile($log) ? $files->lastModified($log) : -1;

            if ($modified > $at) {
                [$newest, $at] = [$log, $modified];
            }
        }

        return $newest;
    }

    /**
     * The directories Laravel writes to, each with whether Laravel creates it on its first write. A
     * setting that holds no path leaves the directory Laravel uses by default.
     *
     * @return array<string, bool>
     */
    private function directories(): array
    {
        $directories = [$this->app->storagePath() => false, $this->app->storagePath('framework/cache') => false];
        $store = $this->config('cache.default');
        $storePath = is_string($store) && $this->defaultDriver('cache.default', 'cache.stores') === 'file' ? $this->configuredPath('cache.stores.'.$store.'.path') : null;
        $session = $this->config('session.driver');
        $views = $this->configuredPath('view.compiled');

        if ($storePath !== null) {
            $directories += [$storePath => true];
        }

        if (is_string($session) && strtolower($session) === 'file') {
            $directories += [$this->configuredPath('session.files') ?? $this->app->storagePath('framework/sessions') => false];
        }

        $directories += $views === null ? [$this->app->storagePath('framework/views') => false] : [$views => true];
        $directories += [$this->app->storagePath('logs') => false];

        foreach ($this->bootstrapCaches() as $cache) {
            $directories += [dirname($cache) => false];
        }

        return $directories;
    }

    /**
     * The files `config:cache`, `route:cache`, `event:cache` and package discovery write, where the
     * application names them.
     *
     * @return list<string>
     */
    private function bootstrapCaches(): array
    {
        $app = $this->app;
        $caches = [];

        if ($app instanceof CachesConfiguration) {
            $caches[] = $app->getCachedConfigPath();
            $caches[] = $app->getCachedServicesPath();
        }

        if ($app instanceof CachesRoutes) {
            $caches[] = $app->getCachedRoutesPath();
        }

        if (method_exists($app, 'getCachedPackagesPath')) {
            $caches[] = $app->getCachedPackagesPath();
        }

        if (method_exists($app, 'getCachedEventsPath')) {
            $caches[] = $app->getCachedEventsPath();
        }

        return array_values(array_filter($caches, is_string(...)));
    }

    /**
     * The path a setting holds; null where it holds none.
     */
    private function configuredPath(string $key): ?string
    {
        $path = $this->config($key);

        return is_string($path) && $path !== '' ? $path : null;
    }
}
