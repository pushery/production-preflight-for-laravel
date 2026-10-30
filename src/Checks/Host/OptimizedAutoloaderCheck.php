<?php

declare(strict_types=1);

namespace Pushery\ProductionPreflight\Checks\Host;

use Override;
use Pushery\ProductionPreflight\Checks\ProductionCheck;
use Pushery\ProductionPreflight\Finding;
use Pushery\ProductionPreflight\Inspection;
use Pushery\ProductionPreflight\Scope;

/**
 * An autoloader that is not optimized: every class the application loads is looked up on the disk
 * first, request after request.
 *
 * An optimized autoloader lists the application's own classes in Composer's class map, so the check
 * looks for one of them there: a file below one of the PSR-4 directories `composer.json` declares.
 */
final class OptimizedAutoloaderCheck extends ProductionCheck
{
    #[Override]
    public function key(): string
    {
        return 'optimized-autoloader';
    }

    #[Override]
    public function scope(): Scope
    {
        return Scope::Host;
    }

    #[Override]
    protected function inspectProduction(): Inspection
    {
        $directories = $this->ownDirectories();
        $classMap = $this->app->basePath('vendor/composer/autoload_classmap.php');
        $classes = $directories !== [] && is_file($classMap) ? require $classMap : null;

        if (! is_array($classes)) {
            return Inspection::undetermined($this->message('optimized-autoloader.unreadable'));
        }

        foreach ($classes as $file) {
            if (is_string($file) && array_any($directories, static fn (string $directory): bool => str_starts_with($file, $directory))) {
                return Inspection::pass($this->message('optimized-autoloader.pass'));
            }
        }

        return Inspection::warn($this->message('optimized-autoloader.warn'), [
            new Finding('composer install', $this->message('optimized-autoloader.finding')),
        ]);
    }

    /**
     * The directories of the application's own PSR-4 namespaces, as absolute paths with a trailing
     * slash.
     *
     * @return list<string>
     */
    private function ownDirectories(): array
    {
        $path = $this->app->basePath('composer.json');
        $json = is_file($path) ? file_get_contents($path) : false;
        $manifest = is_string($json) ? json_decode($json, true) : null;
        $autoload = is_array($manifest) ? ($manifest['autoload'] ?? null) : null;
        $namespaces = is_array($autoload) ? ($autoload['psr-4'] ?? null) : null;
        $directories = [];

        foreach (is_array($namespaces) ? $namespaces : [] as $paths) {
            foreach ((array) $paths as $directory) {
                $resolved = is_string($directory) ? realpath($this->app->basePath($directory)) : false;

                if (is_string($resolved)) {
                    $directories[] = rtrim($resolved, '/').'/';
                }
            }
        }

        return $directories;
    }
}
