<?php

declare(strict_types=1);

namespace Pushery\ProductionPreflight\Checks\Host;

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Migrations\Migrator;
use Override;
use Pushery\ProductionPreflight\Checks\ProductionCheck;
use Pushery\ProductionPreflight\Finding;
use Pushery\ProductionPreflight\Inspection;
use Pushery\ProductionPreflight\Scope;

/**
 * Migrations that have not run: the code is new, the schema is not.
 *
 * Read the way `migrate` reads them, from the migrations directory and the paths packages
 * registered, against the migrations table of the default connection. A migration `migrate` would
 * not run is not pending, and a pass names it: one the application lists in
 * `Migrator::withoutMigrations()`, and one whose `shouldRun()` answers false, unless a migration that
 * has not run comes before it, since running that one may change the answer. A database the check
 * cannot reach is an error laravel-health reports as a crashed check, not a pass.
 */
final class PendingMigrationsCheck extends ProductionCheck
{
    #[Override]
    public function key(): string
    {
        return 'pending-migrations';
    }

    #[Override]
    public function scope(): Scope
    {
        return Scope::Host;
    }

    #[Override]
    protected function inspectProduction(): Inspection
    {
        $migrator = $this->app->bound('migrator') ? $this->app->make('migrator') : null;

        if (! $migrator instanceof Migrator) {
            return Inspection::undetermined($this->message('pending-migrations.no_migrator'));
        }

        $files = $migrator->getMigrationFiles([$this->app->databasePath('migrations'), ...$migrator->paths()]);
        $repository = $migrator->getRepository();
        $ran = $repository->repositoryExists() ? $repository->getRan() : [];
        // The files `migrate` is handed. The migrator selects them in a protected method, which also
        // leaves out the names given to Migrator::withoutMigrations(), a list it offers no way to read.
        $handedOver = (fn (): array => $this->pendingMigrations($files, $ran))->call($migrator);
        $pending = [];
        $skipped = [];

        foreach ($files as $name => $file) {
            if (in_array($name, $ran, true)) {
                continue;
            }

            if (! in_array($file, $handedOver, true) || ($pending === [] && $this->skipsItself($migrator, $file))) {
                $skipped[] = $name;
            } else {
                $pending[] = $name;
            }
        }

        if ($pending !== []) {
            return Inspection::fail(
                $this->message('pending-migrations.fail', ['count' => count($pending)]),
                array_map(fn (string $migration): Finding => new Finding($migration, $this->message('pending-migrations.pending')), $pending),
            );
        }

        return $skipped === []
            ? Inspection::pass($this->message('pending-migrations.pass'))
            : Inspection::pass($this->message('pending-migrations.pass_skipped', ['migrations' => implode(', ', $skipped)]));
    }

    /**
     * Whether a migration answers that it should not run, which `migrate` asks of every migration it
     * is handed, and skips one that says no without recording it. The migration is resolved from its
     * file the way the migrator resolves it.
     */
    private function skipsItself(Migrator $migrator, string $file): bool
    {
        $migration = (fn (): object => $this->resolvePath($file))->call($migrator);

        return $migration instanceof Migration && ! $migration->shouldRun();
    }
}
