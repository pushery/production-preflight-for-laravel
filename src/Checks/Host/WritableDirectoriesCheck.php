<?php

declare(strict_types=1);

namespace Pushery\ProductionPreflight\Checks\Host;

use Override;
use Pushery\ProductionPreflight\Checks\ProductionCheck;
use Pushery\ProductionPreflight\Finding;
use Pushery\ProductionPreflight\Inspection;
use Pushery\ProductionPreflight\Scope;

/**
 * The directories Laravel writes to, missing or not writable: the first user action that writes,
 * a session, a cached view, a log line, falls over.
 *
 * Asked as the user that runs the check. On most servers the scheduler and the web server run as the
 * same user; where they do not, the answer describes the scheduler's, and the finding says so.
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
        $findings = [];

        foreach ($this->directories() as $directory) {
            $problem = match (true) {
                ! is_dir($directory) => 'writable-directories.missing',
                ! is_writable($directory) => 'writable-directories.read_only',
                default => null,
            };

            if ($problem !== null) {
                $findings[] = new Finding($this->relative($directory), $this->message($problem));
            }
        }

        return $findings === []
            ? Inspection::pass($this->message('writable-directories.pass'))
            : Inspection::fail($this->message('writable-directories.fail', ['count' => count($findings)]), $findings);
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
