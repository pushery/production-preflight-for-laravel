<?php

declare(strict_types=1);

namespace Pushery\ProductionPreflight\Checks\Configuration;

use Override;
use Pushery\ProductionPreflight\Checks\ProductionCheck;
use Pushery\ProductionPreflight\Finding;
use Pushery\ProductionPreflight\Inspection;
use Pushery\ProductionPreflight\Scope;

/**
 * A session cookie without the Secure flag on a site that runs over HTTPS.
 *
 * A browser then sends the cookie over plain HTTP as well, for instance on the first request to an
 * `http://` address before the redirect, and anyone on the network can read it. Laravel's shipped
 * config leaves `session.secure` unset, which means no flag, so a default installation fails here
 * until `SESSION_SECURE_COOKIE=true` is set.
 *
 * Whether the site runs over HTTPS is read from `app.url`. When that names `http`, the flag would
 * stop the browser from sending the cookie at all, so the check passes and says so; when it names no
 * scheme, the check cannot tell.
 */
final class SecureCookieCheck extends ProductionCheck
{
    #[Override]
    public function key(): string
    {
        return 'secure-cookie';
    }

    #[Override]
    public function scope(): Scope
    {
        return Scope::Configuration;
    }

    #[Override]
    protected function inspectProduction(): Inspection
    {
        $url = $this->config('app.url');
        $scheme = is_string($url) ? parse_url($url, PHP_URL_SCHEME) : null;

        if (! is_string($scheme)) {
            return Inspection::undetermined($this->message('secure-cookie.no_scheme'));
        }

        if (strtolower($scheme) !== 'https') {
            return Inspection::pass($this->message('secure-cookie.http'));
        }

        return (bool) $this->config('session.secure')
            ? Inspection::pass($this->message('secure-cookie.pass'))
            : Inspection::fail($this->message('secure-cookie.fail'), [new Finding('SESSION_SECURE_COOKIE', $this->message('secure-cookie.finding'))]);
    }
}
