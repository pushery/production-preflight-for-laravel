<?php

declare(strict_types=1);

namespace Pushery\ProductionPreflight\Checks\Host;

use Override;
use Pushery\ProductionPreflight\Checks\ProductionCheck;
use Pushery\ProductionPreflight\Finding;
use Pushery\ProductionPreflight\Inspection;
use Pushery\ProductionPreflight\Scope;

/**
 * The links of `filesystems.links` that `storage:link` would create, missing or wrong: every file
 * stored on the public disk answers 404.
 *
 * A missing link over a directory that already holds files fails, because those files are broken
 * now. Over an empty one it warns: nothing is broken yet, and the first upload will be. A link that
 * points somewhere else fails, and a real directory where the link belongs warns, because a file
 * stored later never shows up in it.
 */
final class StorageLinkCheck extends ProductionCheck
{
    #[Override]
    public function key(): string
    {
        return 'storage-link';
    }

    #[Override]
    public function scope(): Scope
    {
        return Scope::Host;
    }

    #[Override]
    protected function inspectProduction(): Inspection
    {
        $links = $this->config('filesystems.links');
        $fails = [];
        $warnings = [];

        foreach (is_array($links) ? $links : [] as $link => $target) {
            if (! is_string($link) || ! is_string($target)) {
                continue;
            }

            $subject = $this->relative($link);

            if (is_link($link)) {
                if (realpath($link) !== realpath($target)) {
                    $fails[] = new Finding($subject, $this->message('storage-link.elsewhere'));
                }
            } elseif (is_dir($link)) {
                $warnings[] = new Finding($subject, $this->message('storage-link.directory'));
            } elseif ($this->holdsFiles($target)) {
                $fails[] = new Finding($subject, $this->message('storage-link.missing_with_files'));
            } else {
                $warnings[] = new Finding($subject, $this->message('storage-link.missing'));
            }
        }

        if ($fails !== []) {
            return Inspection::fail($this->message('storage-link.fail'), [...$fails, ...$warnings]);
        }

        return $warnings === []
            ? Inspection::pass($this->message('storage-link.pass'))
            : Inspection::warn($this->message('storage-link.warn'), $warnings);
    }

    /**
     * Whether a directory holds anything besides the `.gitignore` a fresh installation keeps there.
     */
    private function holdsFiles(string $directory): bool
    {
        $entries = is_dir($directory) ? scandir($directory) : false;

        return is_array($entries) && array_diff($entries, ['.', '..', '.gitignore']) !== [];
    }
}
