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
 * and reports even when the application is down, and so is an endpoint of the application's own that a
 * monitor pulls, named in `production-preflight.health_endpoint`. A Slack webhook is another, and so is
 * any channel besides mail. Every notification class in `health.notifications.notifications` counts,
 * since laravel-health sends each of them: laravel-health's own sends Slack through its webhook, and a
 * class of the application's own routes Slack itself. Without a second road, it warns. It never fails:
 * mail alone is laravel-health's default, and a check that stopped every standard installation would
 * be switched off.
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

        $endpoint = $this->config('production-preflight.health_endpoint');

        if (is_string($endpoint) && trim($endpoint) !== '') {
            return Inspection::pass($this->message('health-notifications.pulled_own', ['endpoint' => trim($endpoint)]));
        }

        if (! (bool) $this->config('health.notifications.enabled')) {
            return Inspection::warn($this->message('health-notifications.warn'), [
                new Finding('health.notifications.enabled', $this->message('health-notifications.off')),
            ]);
        }

        $notifications = $this->config('health.notifications.notifications');
        $slack = $this->config('health.notifications.slack.webhook_url');
        $webhook = is_string($slack) && trim($slack) !== '';
        $channels = [];
        $reaches = false;

        foreach (is_array($notifications) ? $notifications : [] as $class => $configured) {
            $named = is_array($configured) ? array_values(array_filter($configured, is_string(...))) : [];
            $channels = [...$channels, ...$named];
            $own = $class === CheckFailedNotification::class;
            $beside = array_diff($named, $own ? ['mail', 'slack'] : ['mail']);
            $reaches = $reaches || $beside !== [] || ($own && $webhook && in_array('slack', $named, true));
        }

        if ($reaches) {
            return Inspection::pass($this->message('health-notifications.pass'));
        }

        return Inspection::warn($this->message('health-notifications.warn'), [
            new Finding('health.notifications.notifications', $this->message($channels === [] ? 'health-notifications.none' : 'health-notifications.mail_only')),
        ]);
    }
}
