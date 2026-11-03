<?php

declare(strict_types=1);

namespace Pushery\ProductionPreflight\Support;

use Closure;

/**
 * The name of the user the current process runs as, from the system's user database.
 *
 * Read for the real user id of the process, never from `USER` or another environment variable: under
 * `sudo -u`, a process supervisor or a container entrypoint those name whoever started the process, not the
 * user whose permissions it has. Where the system cannot name the user, without the POSIX extension or
 * without an entry for the id, the answer is null.
 */
final readonly class ProcessUser
{
    /**
     * @param  (Closure(): ?string)|null  $lookup  answers in place of the system's user database, for an application
     *                                             that binds an instance of its own
     */
    public function __construct(private ?Closure $lookup = null) {}

    /** The user's name, or null where it cannot be named. */
    public function name(): ?string
    {
        if ($this->lookup instanceof Closure) {
            $name = ($this->lookup)();

            return $name === '' ? null : $name;
        }

        $entry = extension_loaded('posix') ? posix_getpwuid(posix_getuid()) : false;

        return is_array($entry) && $entry['name'] !== '' ? $entry['name'] : null;
    }
}
