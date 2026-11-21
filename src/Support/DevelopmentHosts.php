<?php

declare(strict_types=1);

namespace Pushery\ProductionPreflight\Support;

/**
 * The hosts a machine for development is served under: its loopback name `localhost`, a name that ends
 * in one of `production-preflight.development_hosts` (`.test`, `.localhost` and `.local` unless the
 * setting names others), and a loopback or a private address.
 *
 * The `development-stage-server` check holds the host of `app.url` to it. `APP_URL` comes from the same
 * environment file as `APP_ENV`, so a server whose `.env` began as a copy of `.env.example` passes that
 * check. An application that answers requests holds the host of a request to the same measure, in a
 * health endpoint for instance: under a development stage, a request to a public host reached a server.
 * It hands over the value of the setting, `config('production-preflight.development_hosts')`, so both
 * read the same endings.
 */
final class DevelopmentHosts
{
    /** The endings of the host names a machine for development is served under, unless the setting names others. */
    public const array SHIPPED = ['.test', '.localhost', '.local'];

    /**
     * Whether a machine for development is served under this host, bare or in the brackets of an IPv6 address.
     *
     * @param  mixed  $endings  the value of `production-preflight.development_hosts` as the configuration holds it, which any type can reach from a published config file
     */
    public static function serves(string $host, mixed $endings = null): bool
    {
        $host = strtolower(trim(trim($host), '[]'));

        if ($host === '') {
            return false;
        }

        if ($host === 'localhost') {
            return true;
        }

        // A loopback and a private address are both reserved ranges to the filter, so an address it refuses with those
        // ranges taken out is one of them.
        if (filter_var($host, FILTER_VALIDATE_IP) !== false) {
            return filter_var($host, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) === false;
        }

        return array_any(self::endings($endings), static fn (string $ending): bool => str_ends_with($host, $ending));
    }

    /**
     * The endings a value of `production-preflight.development_hosts` names, or the shipped ones where it names none.
     *
     * @param  mixed  $configured  the value of the setting as the configuration holds it, which any type can reach from a published config file
     * @return list<string>
     */
    public static function endings(mixed $configured = null): array
    {
        $endings = [];

        foreach (is_array($configured) ? $configured : self::SHIPPED as $ending) {
            if (is_string($ending) && trim($ending) !== '') {
                $endings[] = strtolower(trim($ending));
            }
        }

        return $endings;
    }
}
