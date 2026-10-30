<?php

declare(strict_types=1);

namespace Pushery\ProductionPreflight\Checks\Host;

use Override;
use Pushery\ProductionPreflight\Checks\ProductionCheck;
use Pushery\ProductionPreflight\Evidence;
use Pushery\ProductionPreflight\Finding;
use Pushery\ProductionPreflight\Inspection;
use Pushery\ProductionPreflight\Scope;

/**
 * Development dependencies installed on a production server: test runners, debuggers and code
 * generators that the application never needs and an attacker might.
 *
 * Read from Composer's record of the last install, `vendor/composer/installed.json`, which says
 * whether development packages were installed and which they are.
 */
final class DevDependenciesCheck extends ProductionCheck
{
    #[Override]
    public function key(): string
    {
        return 'dev-dependencies';
    }

    #[Override]
    public function scope(): Scope
    {
        return Scope::Host;
    }

    #[Override]
    protected function inspectProduction(): Inspection
    {
        $path = $this->app->basePath('vendor/composer/installed.json');
        $json = is_file($path) ? file_get_contents($path) : false;
        $installed = is_string($json) ? json_decode($json, true) : null;

        if (! is_array($installed) || ! array_key_exists('dev', $installed)) {
            return Inspection::undetermined($this->message('dev-dependencies.unreadable', ['path' => $this->relative($path)]));
        }

        $names = $installed['dev-package-names'] ?? null;
        $names = is_array($names) ? array_values(array_filter($names, is_string(...))) : [];

        if ($installed['dev'] !== true || $names === []) {
            return Inspection::pass($this->message('dev-dependencies.pass'));
        }

        return Inspection::fail(
            $this->message('dev-dependencies.fail', ['count' => count($names)]),
            [new Finding('composer install', $this->message('dev-dependencies.finding'))],
            [new Evidence('composer install', implode(', ', $names))],
        );
    }
}
