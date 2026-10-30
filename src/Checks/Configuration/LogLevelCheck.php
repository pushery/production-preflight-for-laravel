<?php

declare(strict_types=1);

namespace Pushery\ProductionPreflight\Checks\Configuration;

use Override;
use Pushery\ProductionPreflight\Checks\ProductionCheck;
use Pushery\ProductionPreflight\Finding;
use Pushery\ProductionPreflight\Inspection;
use Pushery\ProductionPreflight\Scope;

/**
 * The default log channel at debug level.
 *
 * Every detail lands on the disk, and it fills at a pace one can calculate. That is a report, not a
 * reason to stop a deploy, so it warns. A channel without a level logs at debug, as Laravel's log
 * manager reads it, and a `stack` channel is read through the channels it names.
 */
final class LogLevelCheck extends ProductionCheck
{
    #[Override]
    public function key(): string
    {
        return 'log-level';
    }

    #[Override]
    public function scope(): Scope
    {
        return Scope::Configuration;
    }

    #[Override]
    protected function inspectProduction(): Inspection
    {
        $default = $this->config('logging.default');
        $channels = $this->config('logging.channels');
        $entry = is_string($default) && is_array($channels) ? ($channels[$default] ?? null) : null;

        if (! is_string($default) || ! is_array($channels) || ! is_array($entry)) {
            return Inspection::undetermined($this->message('log-level.unconfigured'));
        }

        $stacked = $entry['channels'] ?? null;
        $names = ($entry['driver'] ?? null) === 'stack' && is_array($stacked)
            ? array_values(array_filter($stacked, is_string(...)))
            : [$default];
        $findings = [];

        foreach ($names as $name) {
            $channel = $channels[$name] ?? null;
            $level = is_array($channel) ? ($channel['level'] ?? 'debug') : null;

            if (is_string($level) && strtolower($level) === 'debug') {
                $findings[] = new Finding('logging.channels.'.$name, $this->message('log-level.debug'));
            }
        }

        return $findings === []
            ? Inspection::pass($this->message('log-level.pass'))
            : Inspection::warn($this->message('log-level.warn'), $findings);
    }
}
