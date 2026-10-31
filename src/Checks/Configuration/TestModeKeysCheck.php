<?php

declare(strict_types=1);

namespace Pushery\ProductionPreflight\Checks\Configuration;

use Illuminate\Contracts\Config\Repository;
use Override;
use Pushery\ProductionPreflight\Checks\ProductionCheck;
use Pushery\ProductionPreflight\Finding;
use Pushery\ProductionPreflight\Inspection;
use Pushery\ProductionPreflight\Scope;

/**
 * Payment and API credentials still in test mode, in production.
 *
 * No real money moves, and nobody notices on the day of the launch: a test key answers like a live
 * one. The check walks the whole configuration, because a provider's keys stand wherever its package
 * put them, and reports the setting by its path, never its value. A test key is a credential too.
 *
 * What counts as test mode:
 *
 * - a Stripe key that starts with `sk_test_`, `pk_test_` or `rk_test_`
 * - a Mollie key, `test_` followed by at least 30 letters and digits
 * - a setting named `sandbox` that is switched on
 * - a setting named `mode` or `environment` whose value is `sandbox`
 *
 * On staging the check passes: a staging server takes its payments with test keys, and a live key
 * there would move real money with every test purchase.
 */
final class TestModeKeysCheck extends ProductionCheck
{
    #[Override]
    public function key(): string
    {
        return 'test-mode-keys';
    }

    #[Override]
    public function scope(): Scope
    {
        return Scope::Configuration;
    }

    #[Override]
    protected function onStaging(): Inspection
    {
        return Inspection::pass($this->message('test-mode-keys.staging'));
    }

    #[Override]
    protected function inspectProduction(): Inspection
    {
        $findings = $this->walk($this->app->make(Repository::class)->all(), '');

        return $findings === []
            ? Inspection::pass($this->message('test-mode-keys.pass'))
            : Inspection::fail($this->message('test-mode-keys.fail', ['count' => count($findings)]), $findings);
    }

    /**
     * The settings below a path that are in test mode.
     *
     * @param  array<array-key, mixed>  $settings
     * @return list<Finding>
     */
    private function walk(array $settings, string $path): array
    {
        $found = [];

        foreach ($settings as $name => $value) {
            $setting = $path === '' ? (string) $name : $path.'.'.$name;

            if (is_array($value)) {
                $found = [...$found, ...$this->walk($value, $setting)];

                continue;
            }

            $reason = $this->testMode((string) $name, $value);

            if ($reason !== null) {
                $found[] = new Finding($setting, $this->message($reason));
            }
        }

        return $found;
    }

    /**
     * The reason a single setting is in test mode, or null.
     */
    private function testMode(string $name, mixed $value): ?string
    {
        $name = strtolower($name);

        if ($name === 'sandbox') {
            return in_array($value, [true, 1, '1', 'true', 'on', 'yes'], true) ? 'test-mode-keys.sandbox' : null;
        }

        if (! is_string($value)) {
            return null;
        }

        return match (true) {
            preg_match('/^(?:sk|pk|rk)_test_/', $value) === 1 => 'test-mode-keys.stripe',
            preg_match('/^test_[A-Za-z\d]{30,}$/', $value) === 1 => 'test-mode-keys.mollie',
            ($name === 'mode' || $name === 'environment') && strtolower(trim($value)) === 'sandbox' => 'test-mode-keys.sandbox',
            default => null,
        };
    }
}
