<?php

declare(strict_types=1);

namespace Pushery\ProductionPreflight;

use Spatie\Health\Enums\Status;

/**
 * What a check concludes.
 *
 * Four values, because "nothing found" and "not looked" must never read alike. A check that could
 * not look, a missing file or a fact the command line cannot see, says `Undetermined` instead of
 * passing. A deploy reads it as unchecked, and laravel-health shows it as skipped.
 */
enum Verdict: string
{
    case Pass = 'pass';
    case Warn = 'warn';
    case Fail = 'fail';
    case Undetermined = 'undetermined';

    /**
     * The status laravel-health stores and reports for this verdict.
     */
    public function status(): Status
    {
        return match ($this) {
            self::Pass => Status::ok(),
            self::Warn => Status::warning(),
            self::Fail => Status::failed(),
            self::Undetermined => Status::skipped(),
        };
    }
}
