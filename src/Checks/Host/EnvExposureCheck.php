<?php

declare(strict_types=1);

namespace Pushery\ProductionPreflight\Checks\Host;

use Override;
use Pushery\ProductionPreflight\Checks\ProductionCheck;
use Pushery\ProductionPreflight\Finding;
use Pushery\ProductionPreflight\Inspection;
use Pushery\ProductionPreflight\Scope;

/**
 * Secrets that the file system hands out: a `.env` every user of the server may read or write, and a
 * `.env` or `.git` inside the web root.
 *
 * Answered from the file system only. Whether a web server actually serves a file is a question about
 * its configuration, which this check never sees; the env-over-http check asks it with a request
 * instead of guessing. The permission bits are read as POSIX bits.
 */
final class EnvExposureCheck extends ProductionCheck
{
    #[Override]
    public function key(): string
    {
        return 'env-exposure';
    }

    #[Override]
    public function scope(): Scope
    {
        return Scope::Host;
    }

    #[Override]
    protected function inspectProduction(): Inspection
    {
        $findings = [];
        $env = $this->envPath();
        $permissions = is_file($env) ? fileperms($env) : false;

        if ($permissions !== false && ($permissions & 0o004) !== 0) {
            $findings[] = new Finding($this->relative($env), $this->message('env-exposure.world_readable'));
        }

        if ($permissions !== false && ($permissions & 0o002) !== 0) {
            $findings[] = new Finding($this->relative($env), $this->message('env-exposure.world_writable'));
        }

        foreach (['.env', '.git'] as $name) {
            $path = $this->app->publicPath($name);

            if (file_exists($path)) {
                $findings[] = new Finding($this->relative($path), $this->message('env-exposure.web_root'));
            }
        }

        return $findings === []
            ? Inspection::pass($this->message('env-exposure.pass'))
            : Inspection::fail($this->message('env-exposure.fail', ['count' => count($findings)]), $findings);
    }
}
