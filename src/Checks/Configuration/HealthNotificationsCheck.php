<?php

declare(strict_types=1);

namespace Pushery\ProductionPreflight\Checks\Configuration;

use Override;
use Pushery\ProductionPreflight\Checks\ProductionCheck;
use Pushery\ProductionPreflight\Finding;
use Pushery\ProductionPreflight\Inspection;
use Pushery\ProductionPreflight\Scope;
use Spatie\Health\Notifications\CheckFailedNotification;

/**
 * Whether a failing check reaches anyone in operation when mail is the thing that broke.
 *
 * After the deploy, laravel-health delivers what the checks find, and its default delivers by mail
 * alone. One of the findings this package exists for is a mailer that delivers nothing, and in exactly
 * that case the message about it is the one that does not arrive. The deploy itself is covered: the
 * mail-transport check fails it, visible in the deploy tool and independent of the application's mail.
 *
 * So this check asks for a second road. Oh Dear's endpoint is one, a monitor that pulls the results
 * and reports even when the application is down; a Slack webhook is another, and so is any channel
 * besides mail. Without one, it warns. It never fails: mail alone is laravel-health's default, and a
 * check that stopped every standard installation would be switched off.
 */
final class HealthNotificationsCheck extends ProductionCheck
{
    #[Override]
    public function key(): string
    {
        return 'health-notifications';
    }

    #[Override]
    public function scope(): Scope
    {
        return Scope::Configuration;
    }

    #[Override]
    protected function inspectProduction(): Inspection
    {
        if ((bool) $this->config('health.oh_dear_endpoint.enabled')) {
            return Inspection::pass($this->message('health-notifications.pulled'));
        }

        if (! (bool) $this->config('health.notifications.enabled')) {
            return Inspection::warn($this->message('health-notifications.warn'), [
                new Finding('health.notifications.enabled', $this->message('health-notifications.off')),
            ]);
        }

        $notifications = $this->config('health.notifications.notifications');
        $configured = is_array($notifications) ? ($notifications[CheckFailedNotification::class] ?? []) : [];
        $channels = is_array($configured) ? array_values(array_filter($configured, is_string(...))) : [];
        $slack = $this->config('health.notifications.slack.webhook_url');
        $others = array_diff($channels, ['mail', 'slack']);

        if ($others !== [] || (in_array('slack', $channels, true) && is_string($slack) && trim($slack) !== '')) {
            return Inspection::pass($this->message('health-notifications.pass'));
        }

        return Inspection::warn($this->message('health-notifications.warn'), [
            new Finding('health.notifications.notifications', $this->message($channels === [] ? 'health-notifications.none' : 'health-notifications.mail_only')),
        ]);
    }
}
