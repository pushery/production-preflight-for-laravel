<?php

declare(strict_types=1);

namespace Pushery\ProductionPreflight\Checks\File;

use Override;
use Pushery\ProductionPreflight\Checks\PreflightCheck;
use Pushery\ProductionPreflight\Env\EnvFile;
use Pushery\ProductionPreflight\Evidence;
use Pushery\ProductionPreflight\Finding;
use Pushery\ProductionPreflight\Inspection;
use Pushery\ProductionPreflight\Scope;

/**
 * An unquoted value with a `#` in it, where the application reads less than the line shows.
 *
 * phpdotenv starts a comment at the first unquoted `#`, with or without a space in front of it. So
 * `APP_NAME=My #1 App` loads as `My`, `DB_PASSWORD=pa#ss` as `pa`, and `THEME=#ff0000` as nothing,
 * and nobody sees it: the file reads correctly.
 *
 * Three cases, told apart by what stands around the `#`:
 *
 * - Directly after the value or at its start, `pa#ss`: no one writes a comment that way, so the
 *   value is cut. That fails.
 * - After whitespace and directly in front of text, `My #1 App`: either text cut off or a comment
 *   written without a space after the `#`. It warns and asks for quotes.
 * - After whitespace and in front of whitespace, `true # local only`: an ordinary comment. Nothing.
 *
 * A quoted value keeps its `#`, which is the cure the finding names.
 */
final class UnquotedHashCheck extends PreflightCheck
{
    #[Override]
    public function key(): string
    {
        return 'unquoted-hash';
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

        $cut = [];
        $ambiguous = [];
        $evidence = [];

        foreach ($file->assignments as $assignment) {
            $hash = $assignment->quote() === null ? strpos($assignment->raw, '#') : false;

            if ($hash === false) {
                continue;
            }

            $before = $hash === 0 ? '' : $assignment->raw[$hash - 1];
            $after = substr($assignment->raw, $hash + 1, 1);
            $subject = $this->message('line', ['key' => $assignment->key, 'line' => $assignment->line]);

            if ($before !== ' ' && $before !== "\t") {
                $cut[] = new Finding($subject, $this->message('unquoted-hash.cut'));
            } elseif (! in_array($after, ['', ' ', "\t"], true)) {
                $ambiguous[] = new Finding($subject, $this->message('unquoted-hash.ambiguous'));
            } else {
                continue;
            }

            $evidence[] = new Evidence($subject, $this->message('unquoted-hash.evidence', [
                'value' => $this->shown($assignment->value()),
                'raw' => $assignment->raw,
            ]));
        }

        if ($cut !== []) {
            return Inspection::fail($this->message('unquoted-hash.fail', ['count' => count($cut)]), [...$cut, ...$ambiguous], $evidence);
        }

        return $ambiguous === []
            ? Inspection::pass($this->message('unquoted-hash.pass'))
            : Inspection::warn($this->message('unquoted-hash.warn', ['count' => count($ambiguous)]), $ambiguous, $evidence);
    }
}
