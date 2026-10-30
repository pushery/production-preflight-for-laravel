<?php

declare(strict_types=1);

namespace Pushery\ProductionPreflight;

use Pushery\ProductionPreflight\Checks\Configuration\AppDebugCheck;
use Pushery\ProductionPreflight\Checks\Configuration\AppKeyCheck;
use Pushery\ProductionPreflight\Checks\Configuration\CacheStoreCheck;
use Pushery\ProductionPreflight\Checks\Configuration\DebugToolsCheck;
use Pushery\ProductionPreflight\Checks\Configuration\HealthNotificationsCheck;
use Pushery\ProductionPreflight\Checks\Configuration\LogLevelCheck;
use Pushery\ProductionPreflight\Checks\Configuration\MailTransportCheck;
use Pushery\ProductionPreflight\Checks\Configuration\QueueConnectionCheck;
use Pushery\ProductionPreflight\Checks\Configuration\SecureCookieCheck;
use Pushery\ProductionPreflight\Checks\Configuration\SessionDriverCheck;
use Pushery\ProductionPreflight\Checks\Configuration\TestModeKeysCheck;
use Pushery\ProductionPreflight\Checks\File\DuplicateEnvKeysCheck;
use Pushery\ProductionPreflight\Checks\File\EnvBytesCheck;
use Pushery\ProductionPreflight\Checks\File\EnvExampleKeysCheck;
use Pushery\ProductionPreflight\Checks\File\PlaceholderValuesCheck;
use Pushery\ProductionPreflight\Checks\File\UnquotedHashCheck;
use Pushery\ProductionPreflight\Checks\Host\ConfigCacheFreshnessCheck;
use Pushery\ProductionPreflight\Checks\Host\DevDependenciesCheck;
use Pushery\ProductionPreflight\Checks\Host\EnvExposureCheck;
use Pushery\ProductionPreflight\Checks\Host\EnvOverHttpCheck;
use Pushery\ProductionPreflight\Checks\Host\OpcacheCheck;
use Pushery\ProductionPreflight\Checks\Host\OptimizedAutoloaderCheck;
use Pushery\ProductionPreflight\Checks\Host\PendingMigrationsCheck;
use Pushery\ProductionPreflight\Checks\Host\PhpVersionParityCheck;
use Pushery\ProductionPreflight\Checks\Host\QueueRestartCheck;
use Pushery\ProductionPreflight\Checks\Host\StorageLinkCheck;
use Pushery\ProductionPreflight\Checks\Host\WritableDirectoriesCheck;
use Pushery\ProductionPreflight\Checks\PreflightCheck;

/**
 * The checks of this package, in the order a report lists them.
 *
 * Register them with laravel-health in the application, `Health::checks(Preflight::checks())`, for
 * the scheduled runs. The two commands of this package run the same classes on their own.
 */
final class Preflight
{
    /**
     * @var list<class-string<PreflightCheck>>
     */
    public const array CHECKS = [
        DuplicateEnvKeysCheck::class,
        EnvExampleKeysCheck::class,
        PlaceholderValuesCheck::class,
        UnquotedHashCheck::class,
        EnvBytesCheck::class,
        AppDebugCheck::class,
        AppKeyCheck::class,
        MailTransportCheck::class,
        HealthNotificationsCheck::class,
        TestModeKeysCheck::class,
        QueueConnectionCheck::class,
        SessionDriverCheck::class,
        CacheStoreCheck::class,
        SecureCookieCheck::class,
        DebugToolsCheck::class,
        LogLevelCheck::class,
        ConfigCacheFreshnessCheck::class,
        QueueRestartCheck::class,
        PendingMigrationsCheck::class,
        WritableDirectoriesCheck::class,
        StorageLinkCheck::class,
        EnvExposureCheck::class,
        EnvOverHttpCheck::class,
        DevDependenciesCheck::class,
        OptimizedAutoloaderCheck::class,
        PhpVersionParityCheck::class,
        OpcacheCheck::class,
    ];

    /**
     * Every check, or the checks of the scopes named.
     *
     * @return list<PreflightCheck>
     */
    public static function checks(Scope ...$scopes): array
    {
        $checks = [];

        foreach (self::CHECKS as $class) {
            $check = $class::new();

            if ($scopes === [] || in_array($check->scope(), $scopes, true)) {
                $checks[] = $check;
            }
        }

        return $checks;
    }
}
