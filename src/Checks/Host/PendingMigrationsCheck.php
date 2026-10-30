<?php

declare(strict_types=1);

namespace Pushery\ProductionPreflight\Checks\Host;

use Illuminate\Database\Migrations\Migrator;
use Override;
use Pushery\ProductionPreflight\Checks\ProductionCheck;
use Pushery\ProductionPreflight\Finding;
use Pushery\ProductionPreflight\Inspection;
use Pushery\ProductionPreflight\Scope;

/**
 * Migrations that have not run: the code is new, the schema is not.
 *
 * Read the way `migrate:status` reads them, from the migrations directory and the paths packages
 * registered, against the migrations table of the default connection. A database the check cannot
 * reach is an error laravel-health reports as a crashed check, not a pass.
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

        $files = array_keys($migrator->getMigrationFiles([$this->app->databasePath('migrations'), ...$migrator->paths()]));
        $repository = $migrator->getRepository();
        $ran = $repository->repositoryExists() ? $repository->getRan() : [];
        $pending = array_values(array_diff($files, $ran));

        return $pending === []
            ? Inspection::pass($this->message('pending-migrations.pass'))
            : Inspection::fail(
                $this->message('pending-migrations.fail', ['count' => count($pending)]),
                array_map(fn (string $migration): Finding => new Finding($migration, $this->message('pending-migrations.pending')), $pending),
            );
    }
}
