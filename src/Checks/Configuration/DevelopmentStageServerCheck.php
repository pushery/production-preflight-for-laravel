<?php

declare(strict_types=1);

namespace Pushery\ProductionPreflight\Checks\Configuration;

use Override;
use Pushery\ProductionPreflight\Checks\PreflightCheck;
use Pushery\ProductionPreflight\Evidence;
use Pushery\ProductionPreflight\Finding;
use Pushery\ProductionPreflight\Inspection;
use Pushery\ProductionPreflight\Scope;
use Pushery\ProductionPreflight\Stage;
use Pushery\ProductionPreflight\Support\DevelopmentHosts;

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
 * address, the measure of DevelopmentHosts. Where `app.url` names any other host, the application runs on a server, and the check fails. An address
 * without a host leaves nothing to judge. The host and the value of `APP_ENV` are evidence, which only the console
 * prints: the summary and the finding name the settings alone.
 */
final class DevelopmentStageServerCheck extends PreflightCheck
{
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

        if (DevelopmentHosts::serves($host, $this->config('production-preflight.development_hosts'))) {
            return Inspection::pass($this->message('development-stage-server.pass'));
        }

        $env = $this->config('app.env');

        return Inspection::fail(
            $this->message('development-stage-server.fail'),
            [new Finding('APP_URL', $this->message('development-stage-server.finding'))],
            [new Evidence('APP_URL', $host), new Evidence('APP_ENV', is_string($env) ? $env : '')],
        );
    }
}
