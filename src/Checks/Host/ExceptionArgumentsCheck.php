<?php

declare(strict_types=1);

namespace Pushery\ProductionPreflight\Checks\Host;

use Closure;
use Illuminate\Contracts\Foundation\Application;
use Override;
use Pushery\ProductionPreflight\Checks\ProductionCheck;
use Pushery\ProductionPreflight\Finding;
use Pushery\ProductionPreflight\Inspection;
use Pushery\ProductionPreflight\Scope;

/**
 * The arguments of every call kept in the trace of an exception, and with them in the log.
 *
 * With `zend.exception_ignore_args` off, as `php.ini-development` ships it, PHP keeps the arguments
 * of every call in a trace, and Laravel writes the trace of every reported exception to the log. PHP
 * shortens a string there to `zend.exception_string_param_max_len`, 15 characters unless set, so a
 * PIN, a code or a short token stands in the log whole wherever its parameter lacks
 * `#[\SensitiveParameter]`. `php.ini-production` switches the arguments off.
 *
 * The command line and PHP-FPM each read a php.ini of their own, so the check answers for the
 * process it runs in, and its finding names both.
 */
final class ExceptionArgumentsCheck extends ProductionCheck
{
    /**
     * @param  (Closure(string): (string|false))|null  $ini  reads a setting; null reads PHP's own
     */
    public function __construct(Application $app, private readonly ?Closure $ini = null)
    {
        parent::__construct($app);
    }

    #[Override]
    public function key(): string
    {
        return 'exception-arguments';
    }

    #[Override]
    public function scope(): Scope
    {
        return Scope::Host;
    }

    #[Override]
    protected function inspectProduction(): Inspection
    {
        $ignored = ($this->ini ?? ini_get(...))('zend.exception_ignore_args');

        return is_string($ignored) && in_array(strtolower(trim($ignored)), ['1', 'on', 'true', 'yes'], true)
            ? Inspection::pass($this->message('exception-arguments.pass'))
            : Inspection::fail($this->message('exception-arguments.fail'), [
                new Finding('zend.exception_ignore_args', $this->message('exception-arguments.finding')),
            ]);
    }
}
