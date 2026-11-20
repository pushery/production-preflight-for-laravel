<?php

declare(strict_types=1);

namespace Pushery\ProductionPreflight\Checks\Host;

use Illuminate\Contracts\Foundation\Application;
use Override;
use Pushery\ProductionPreflight\Checks\ProductionCheck;
use Pushery\ProductionPreflight\Evidence;
use Pushery\ProductionPreflight\Finding;
use Pushery\ProductionPreflight\Inspection;
use Pushery\ProductionPreflight\Scope;
use Symfony\Component\Process\Process;
use Throwable;

/**
 * The command line on another PHP version than the web server: migrations and scheduled commands
 * then run under a different version than the application they serve.
 *
 * The command line's side is the PHP the check runs in, so the check answers on the command line,
 * laravel-health's scheduled run for one. In a web request that PHP is the web server's own, which
 * would be compared with itself, so there it says it could not look. The web server's side is a
 * binary it runs with `-v`, the one `production-preflight.php.fpm` names, compared by minor version.
 * Without a named binary, or on a host that switched off `proc_open`, it says it could not look: a
 * missing function is not a matching version.
 */
final class PhpVersionParityCheck extends ProductionCheck
{
    /**
     * @param  bool|null  $processes  whether processes may be started; null asks the host
     * @param  string  $sapi  the server API the check runs under
     */
    public function __construct(Application $app, private readonly ?bool $processes = null, private readonly string $sapi = PHP_SAPI)
    {
        parent::__construct($app);
    }

    /**
     * In a web request the PHP the check runs in is the web server's own, and without the web server's
     * binary named there is no second version to compare.
     */
    #[Override]
    public function canLookHere(): bool
    {
        $binary = $this->config('production-preflight.php.fpm');

        return $this->onCommandLine() && is_string($binary) && trim($binary) !== '';
    }

    #[Override]
    public function key(): string
    {
        return 'php-version-parity';
    }

    #[Override]
    public function scope(): Scope
    {
        return Scope::Host;
    }

    #[Override]
    protected function inspectProduction(): Inspection
    {
        if (! $this->onCommandLine()) {
            return Inspection::undetermined($this->message('php-version-parity.web_request'));
        }

        $binary = $this->config('production-preflight.php.fpm');

        if (! is_string($binary) || trim($binary) === '') {
            return Inspection::undetermined($this->message('php-version-parity.unnamed'));
        }

        if (! ($this->processes ?? function_exists('proc_open'))) {
            return Inspection::undetermined($this->message('php-version-parity.no_processes'));
        }

        $web = $this->version(trim($binary));

        if ($web === null) {
            return Inspection::undetermined($this->message('php-version-parity.unanswered'));
        }

        $cli = PHP_MAJOR_VERSION.'.'.PHP_MINOR_VERSION;
        $replace = ['cli' => $cli, 'web' => $web];

        return $web === $cli
            ? Inspection::pass($this->message('php-version-parity.pass', $replace))
            : Inspection::fail(
                $this->message('php-version-parity.fail', $replace),
                [new Finding('production-preflight.php.fpm', $this->message('php-version-parity.finding', $replace))],
                [new Evidence('production-preflight.php.fpm', $binary)],
            );
    }

    /**
     * Whether the check runs on the command line, where the PHP it runs in is the command line's.
     */
    private function onCommandLine(): bool
    {
        return $this->sapi === 'cli' || $this->sapi === 'phpdbg';
    }

    /**
     * The minor version a PHP binary reports, `8.4`; null when it does not answer with one.
     */
    private function version(string $binary): ?string
    {
        $timeout = $this->config('production-preflight.php.timeout');

        try {
            $process = new Process([$binary, '-v'], null, null, null, is_numeric($timeout) ? (float) $timeout : 10.0);
            $process->run();
            $output = $process->getOutput();
        } catch (Throwable) {
            return null;
        }

        return preg_match('/^PHP (\d+\.\d+)\./', $output, $matches) === 1 ? $matches[1] : null;
    }
}
