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
 * Bytes in the `.env` that do not show: a byte order mark, carriage returns, whitespace after a
 * value.
 *
 * Read from the bytes of the file, never from a line that has already swallowed them. Measured
 * against phpdotenv: it drops the mark, ends a line at a carriage return as well as at a line feed,
 * and trims an unquoted value. Laravel therefore reads such a file correctly, and the check warns
 * instead of failing. The warning is for everything else that reads the file as text: a shell that
 * sources it keeps the carriage return in every value and reads the mark as part of the first name,
 * and a tool that takes a line literally, such as Docker's `--env-file`, keeps the whitespace. The
 * value is then not the one that stands there, and every display shows the one that does.
 */
final class EnvBytesCheck extends PreflightCheck
{
    #[Override]
    public function key(): string
    {
        return 'env-bytes';
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

        $findings = [];

        if ($file->hasByteOrderMark()) {
            $findings[] = new Finding($this->message('env-bytes.mark'), $this->message('env-bytes.mark_statement'));
        }

        if ($file->carriageReturns() > 0) {
            $findings[] = new Finding($this->message('env-bytes.endings'), $this->message('env-bytes.endings_statement', ['count' => $file->carriageReturns()]));
        }

        foreach ($file->assignments as $assignment) {
            if ($assignment->quote() === null && preg_match('/[ \t]$/', $assignment->raw) === 1) {
                $findings[] = new Finding(
                    $this->message('line', ['key' => $assignment->key, 'line' => $assignment->line]),
                    $this->message('env-bytes.whitespace'),
                );
            }
        }

        return $findings === []
            ? Inspection::pass($this->message('env-bytes.pass'))
            : Inspection::warn($this->message('env-bytes.warn'), $findings);
    }
}
