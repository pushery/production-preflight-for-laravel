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
 * OPcache switched off, or checking every file for changes: a multiple of the CPU, without any
 * symptom.
 *
 * Only the web server's PHP can answer this, because the command line has settings of its own. So
 * the check answers in a web request, laravel-health's endpoint with fresh results for one, and on
 * the command line it says it could not look. Switched off fails; checking timestamps warns, since an
 * in-place deploy that does not reload PHP relies on it.
 */
final class OpcacheCheck extends ProductionCheck
{
    /**
     * @param  string  $sapi  the server API the check runs under
     * @param  (Closure(string): (string|false))|null  $ini  reads a setting; null reads PHP's own
     */
    public function __construct(Application $app, private readonly string $sapi = PHP_SAPI, private readonly ?Closure $ini = null)
    {
        parent::__construct($app);
    }

    #[Override]
    public function key(): string
    {
        return 'opcache';
    }

    #[Override]
    public function scope(): Scope
    {
        return Scope::Host;
    }

    #[Override]
    protected function inspectProduction(): Inspection
    {
        if ($this->sapi === 'cli' || $this->sapi === 'phpdbg') {
            return Inspection::undetermined($this->message('opcache.command_line'));
        }

        $enabled = $this->setting('opcache.enable');

        if ($enabled === false || ! $this->isOn($enabled)) {
            return Inspection::fail($this->message('opcache.fail'), [
                new Finding('opcache.enable', $this->message($enabled === false ? 'opcache.not_loaded' : 'opcache.off')),
            ]);
        }

        $validates = $this->setting('opcache.validate_timestamps');

        return is_string($validates) && $this->isOn($validates)
            ? Inspection::warn($this->message('opcache.warn'), [new Finding('opcache.validate_timestamps', $this->message('opcache.timestamps'))])
            : Inspection::pass($this->message('opcache.pass'));
    }

    private function setting(string $name): string|false
    {
        return ($this->ini ?? ini_get(...))($name);
    }

    private function isOn(string $value): bool
    {
        return in_array(strtolower(trim($value)), ['1', 'on', 'true', 'yes'], true);
    }
}
