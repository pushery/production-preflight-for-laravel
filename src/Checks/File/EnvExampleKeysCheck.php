<?php

declare(strict_types=1);

namespace Pushery\ProductionPreflight\Checks\File;

use Override;
use Pushery\ProductionPreflight\Checks\PreflightCheck;
use Pushery\ProductionPreflight\Env\EnvFile;
use Pushery\ProductionPreflight\Finding;
use Pushery\ProductionPreflight\Inspection;
use Pushery\ProductionPreflight\Scope;

/**
 * The keys of the `.env` against the keys of its example file, in both directions and at different
 * volumes.
 *
 * A key the example names and the `.env` lacks is the most frequent mistake of a go-live: a feature
 * ships, its key is missing, and it does nothing without a word, because `env()` answers null and
 * null is usually a valid value. That fails.
 *
 * A key the `.env` sets and the example lacks is drift, mostly an example nobody kept up. It warns,
 * more quietly, and it still belongs in the report: the next installation is set up from the example,
 * misses the key, and then the first direction fails against it. A check that knew only one direction
 * would produce the cause of the other one's finding.
 *
 * A key the application reads with a default of its own may be left out of a server's `.env` on
 * purpose. `production-preflight.optional_env_keys` declares such keys, and a missing one does not
 * fail: the check passes and names it. Every other missing key still fails.
 */
final class EnvExampleKeysCheck extends PreflightCheck
{
    #[Override]
    public function key(): string
    {
        return 'env-example-keys';
    }

    #[Override]
    public function scope(): Scope
    {
        return Scope::File;
    }

    #[Override]
    protected function inspect(): Inspection
    {
        $env = $this->envFile();

        if (! $env instanceof EnvFile) {
            return $this->unreadable($this->envPath());
        }

        $example = EnvFile::read($this->examplePath());

        if (! $example instanceof EnvFile) {
            return $this->unreadable($this->examplePath());
        }

        $absent = array_values(array_diff($example->keys(), $env->keys()));
        $optional = array_values(array_filter($absent, $this->declaredOptional(...)));
        $missing = array_values(array_diff($absent, $optional));
        $undocumented = array_values(array_diff($env->keys(), $example->keys()));
        $findings = [
            ...array_map(fn (string $key): Finding => new Finding($key, $this->message('env-example-keys.missing')), $missing),
            ...array_map(fn (string $key): Finding => new Finding($key, $this->message('env-example-keys.undocumented')), $undocumented),
        ];
        $replace = ['missing' => count($missing), 'undocumented' => count($undocumented)];

        if ($missing !== []) {
            return Inspection::fail($this->message('env-example-keys.fail', $replace), $findings);
        }

        if ($undocumented !== []) {
            return Inspection::warn($this->message('env-example-keys.warn', $replace), $findings);
        }

        return $optional === []
            ? Inspection::pass($this->message('env-example-keys.pass'))
            : Inspection::pass($this->message('env-example-keys.pass_optional', ['optional' => implode(', ', $optional)]));
    }

    /**
     * Whether `production-preflight.optional_env_keys` declares a key optional: by its name, or by an
     * entry that ends in `*` and begins it.
     */
    private function declaredOptional(string $key): bool
    {
        $declared = $this->config('production-preflight.optional_env_keys');

        foreach (is_array($declared) ? $declared : [] as $entry) {
            $entry = is_string($entry) ? trim($entry) : '';

            if ($entry !== '' && ($entry === $key || (str_ends_with($entry, '*') && str_starts_with($key, substr($entry, 0, -1))))) {
                return true;
            }
        }

        return false;
    }
}
