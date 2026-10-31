<?php

declare(strict_types=1);

namespace Pushery\ProductionPreflight\Checks\Host;

use Illuminate\Filesystem\Filesystem;
use Override;
use Pushery\ProductionPreflight\Checks\ProductionCheck;
use Pushery\ProductionPreflight\Evidence;
use Pushery\ProductionPreflight\Finding;
use Pushery\ProductionPreflight\Inspection;
use Pushery\ProductionPreflight\Scope;

/**
 * The directories Laravel writes to, missing or not writable: the first user action that writes,
 * a session, a cached view, a log line, falls over.
 *
 * Asked as the user that runs the check. On most servers the scheduler and the web server run as the
 * same user; where they do not, the answer describes the scheduler's, and the finding says so.
 *
 * The newest log file is asked as well. A log line is appended to the file that exists, and that file
 * keeps the permissions of the process that opened it first. Where two users share the log directory
 * through their group, the directory takes new files from both while the current file takes lines from
 * its owner only: the directory reads as writable, and the next line of the other process is lost.
 *
 * A failure names the server, and carries it as `server` evidence. Where several servers run the
 * scheduler, the check runs on whichever one the minute picked, and a finding without the server leaves
 * open which machine has the wrong permissions.
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

        foreach ($this->directories() as $directory) {
            $problem = match (true) {
                ! $files->isDirectory($directory) => 'writable-directories.missing',
                ! $files->isWritable($directory) => 'writable-directories.read_only',
                default => null,
            };

            if ($problem !== null) {
                $findings[] = new Finding($this->relative($directory), $this->message($problem));
            }
        }

        $log = $this->newestLog($files);

        if ($log !== null && ! $files->isWritable($log)) {
            $findings[] = new Finding($this->relative($log), $this->message('writable-directories.log_read_only'));
        }

        if ($findings === []) {
            return Inspection::pass($this->message('writable-directories.pass'));
        }

        $server = gethostname() ?: php_uname('n');

        return Inspection::fail(
            $this->message('writable-directories.fail', ['count' => count($findings), 'server' => $server]),
            $findings,
            [new Evidence('server', $server)],
        );
    }

    /** The newest log file in the log directory, by its modification time, or null where there is none. */
    private function newestLog(Filesystem $files): ?string
    {
        $newest = null;
        $at = -1;

        foreach (array_filter($files->glob($this->app->storagePath('logs').'/*.log'), is_string(...)) as $log) {
            $modified = $files->isFile($log) ? $files->lastModified($log) : -1;

            if ($modified > $at) {
                [$newest, $at] = [$log, $modified];
            }
        }

        return $newest;
    }

    /**
     * @return list<string>
     */
    private function directories(): array
    {
        return [
            $this->app->storagePath(),
            $this->app->storagePath('framework/cache'),
            $this->app->storagePath('framework/sessions'),
            $this->app->storagePath('framework/views'),
            $this->app->storagePath('logs'),
            $this->app->bootstrapPath('cache'),
        ];
    }
}
