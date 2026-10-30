<?php

declare(strict_types=1);

namespace Pushery\ProductionPreflight\Checks\File;

use Override;
use Pushery\ProductionPreflight\Checks\PreflightCheck;
use Pushery\ProductionPreflight\Env\Assignment;
use Pushery\ProductionPreflight\Env\EnvFile;
use Pushery\ProductionPreflight\Evidence;
use Pushery\ProductionPreflight\Finding;
use Pushery\ProductionPreflight\Inspection;
use Pushery\ProductionPreflight\Scope;

/**
 * A key the `.env` sets on more than one line.
 *
 * It is valid syntax, it raises no warning, and which line wins stands nowhere. Measured on the
 * production code path: the LAST one does. phpdotenv lets a later line of the same file overwrite an
 * earlier one and protects only what the real environment held before loading, so the first line has
 * no effect, and nobody sees whether it was the one that was meant. The report says so, because
 * whoever stands in front of the finding on a server looks nothing up.
 *
 * Two lines with equal values are untidiness and warn; two with different values mean a setting
 * stands in the file and does not apply, and fail. A check that said both alike would be switched off
 * for the frequent harmless case, and then be gone for the rare expensive one.
 *
 * The finding names the key, the lines and the verdict, never a value: it goes to every channel
 * laravel-health feeds. The values go to the console alone, as evidence. The rule came from a
 * scheduled run whose alert said "failed with exit code 1" 38 times without naming one key, and a
 * finding that cannot be acted on is as good as none.
 */
final class DuplicateEnvKeysCheck extends PreflightCheck
{
    #[Override]
    public function key(): string
    {
        return 'duplicate-env-keys';
    }

    #[Override]
    public function scope(): Scope
    {
        return Scope::File;
    }

    #[Override]
    protected function inspect(): Inspection
    {
        $file = $this->envFile();

        if (! $file instanceof EnvFile) {
            return $this->unreadable($this->envPath());
        }

        $duplicates = $file->duplicates();

        if ($duplicates === []) {
            return Inspection::pass($this->message('duplicate-env-keys.pass'));
        }

        $findings = [];
        $evidence = [];
        $differ = false;

        foreach ($duplicates as $duplicate) {
            $differ = $differ || $duplicate->valuesDiffer();
            $findings[] = new Finding($duplicate->key, $this->message('duplicate-env-keys.finding', [
                'lines' => implode(', ', $duplicate->lines()),
                'values' => $this->message($duplicate->valuesDiffer() ? 'duplicate-env-keys.different' : 'duplicate-env-keys.equal'),
            ]));
            $evidence[] = new Evidence($duplicate->key, $this->message('duplicate-env-keys.evidence', [
                'lines' => implode(', ', array_map(
                    fn (Assignment $assignment): string => $this->message('duplicate-env-keys.line', ['line' => $assignment->line, 'value' => $this->shown($assignment->value())]),
                    $duplicate->assignments,
                )),
                'value' => $this->shown($duplicate->effective()->value()),
            ]));
        }

        $summary = $this->message($differ ? 'duplicate-env-keys.fail' : 'duplicate-env-keys.warn', ['count' => count($duplicates)]);

        return $differ ? Inspection::fail($summary, $findings, $evidence) : Inspection::warn($summary, $findings, $evidence);
    }
}
