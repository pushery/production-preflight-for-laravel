<?php

declare(strict_types=1);

namespace Pushery\ProductionPreflight;

/**
 * What a check needs in order to answer, which decides when it can run.
 */
enum Scope: string
{
    /** The `.env` file itself, read as text: answers before a deploy, without booting. */
    case File = 'file';

    /** The configuration the application loaded: answers in a deploy, before any harm is done. */
    case Configuration = 'configuration';

    /** The state of the installation on its server: answers only there, on a schedule. */
    case Host = 'host';

    /**
     * Whether a deploy runs checks of this scope. The state of the installation is the server's,
     * and half of it changes after a deploy, which is why those checks run on a schedule.
     */
    public function runsInADeploy(): bool
    {
        return $this !== self::Host;
    }
}
