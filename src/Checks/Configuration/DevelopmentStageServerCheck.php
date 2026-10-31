<?php

declare(strict_types=1);

namespace Pushery\ProductionPreflight\Checks\Configuration;

use Override;
use Pushery\ProductionPreflight\Checks\PreflightCheck;
use Pushery\ProductionPreflight\Finding;
use Pushery\ProductionPreflight\Inspection;
use Pushery\ProductionPreflight\Scope;
use Pushery\ProductionPreflight\Stage;

/**
 * A server that declares a development stage: `APP_ENV=local` where the application is served under a public host.
 *
 * Laravel's `.env.example` sets `APP_ENV=local`, and a server whose environment began as a copy of it keeps that. The
 * stage is then development, every production check passes there, and `preflight --deploy` ends with 0: the one
 * misconfiguration that hides all the others would never be reported itself. So this check reads the development stage
 * and nothing else.
 *
 * A machine for development is served under its loopback name, `localhost`, under a name that ends in one of
 * `production-preflight.development_hosts` (`.test`, `.localhost` and `.local` unless set), or at a loopback or a private
 * address. Where `app.url` names any other host, the application runs on a server, and the check fails. An address
 * without a host leaves nothing to judge.
 */
final class DevelopmentStageServerCheck extends PreflightCheck
{
    /** The endings of the host names a machine for development is served under, unless the setting names others. */
    private const array DEVELOPMENT_HOSTS = ['.test', '.localhost', '.local'];

    #[Override]
    public function key(): string
    {
        return 'development-stage-server';
    }

    #[Override]
    public function scope(): Scope
    {
        return Scope::Configuration;
    }

    #[Override]
    protected function inspect(): Inspection
    {
        $stage = $this->stage();

        if ($stage !== Stage::Development) {
            return Inspection::pass($this->message('development-stage-server.not_development', ['stage' => $stage->value]));
        }

        $url = $this->config('app.url');
        $host = is_string($url) ? parse_url($url, PHP_URL_HOST) : null;

        if (! is_string($host) || $host === '') {
            return Inspection::undetermined($this->message('development-stage-server.no_host'));
        }

        $host = strtolower(trim($host, '[]'));

        if ($this->servesDevelopment($host)) {
            return Inspection::pass($this->message('development-stage-server.pass', ['host' => $host]));
        }

        $env = $this->config('app.env');

        return Inspection::fail(
            $this->message('development-stage-server.fail', ['host' => $host, 'env' => is_string($env) ? $env : '']),
            [new Finding('APP_URL', $this->message('development-stage-server.finding', ['host' => $host]))],
        );
    }

    /** Whether a machine for development is served under this host: its loopback name, an ending of the setting, or a loopback or private address. */
    private function servesDevelopment(string $host): bool
    {
        if ($host === 'localhost') {
            return true;
        }

        // A loopback and a private address are both reserved ranges to the filter, so an address it refuses with those
        // ranges taken out is one of them.
        if (filter_var($host, FILTER_VALIDATE_IP) !== false) {
            return filter_var($host, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) === false;
        }

        return array_any($this->developmentHosts(), fn (string $ending): bool => str_ends_with($host, $ending));
    }

    /**
     * The endings `production-preflight.development_hosts` names, or the shipped ones where it names none.
     *
     * @return list<string>
     */
    private function developmentHosts(): array
    {
        $configured = $this->config('production-preflight.development_hosts');
        $endings = [];

        foreach (is_array($configured) ? $configured : self::DEVELOPMENT_HOSTS as $ending) {
            if (is_string($ending) && trim($ending) !== '') {
                $endings[] = strtolower(trim($ending));
            }
        }

        return $endings;
    }
}
