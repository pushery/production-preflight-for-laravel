<?php

declare(strict_types=1);

namespace Pushery\ProductionPreflight\Checks\Configuration;

use Override;
use Pushery\ProductionPreflight\Checks\ProductionCheck;
use Pushery\ProductionPreflight\Finding;
use Pushery\ProductionPreflight\Inspection;
use Pushery\ProductionPreflight\Scope;
use Pushery\ProductionPreflight\Verdict;
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
 *
 * laravel-health serves the Oh Dear endpoint only when it is switched on and has a secret and a url, so
 * Oh Dear counts once all three are set. Switched on without a secret or a url, there is no endpoint and
 * Oh Dear's request ends in a 404, so the check warns about it whatever other road there is.
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
        $ohDear = (bool) $this->config('health.oh_dear_endpoint.enabled');
        $unserved = $ohDear ? $this->unservedOhDearSettings() : [];

        if ($ohDear && $unserved === []) {
            return Inspection::pass($this->message('health-notifications.pulled'));
        }

        $roads = $this->otherRoads();

        if ($unserved === []) {
            return $roads;
        }

        return $roads->verdict === Verdict::Pass
            ? Inspection::warn($this->message('health-notifications.unserved'), $unserved)
            : Inspection::warn($roads->summary, [...$unserved, ...$roads->findings]);
    }

    /**
     * A finding for each setting laravel-health needs before it serves the Oh Dear endpoint, the secret
     * and the url, that is not set. It reads them as laravel-health does, so a value PHP takes for
     * false, `0` among them, is no setting either.
     *
     * @return list<Finding>
     */
    private function unservedOhDearSettings(): array
    {
        $findings = [];

        foreach (['secret', 'url'] as $setting) {
            if (! (bool) $this->config('health.oh_dear_endpoint.'.$setting)) {
                $findings[] = new Finding('health.oh_dear_endpoint.'.$setting, $this->message('health-notifications.unserved_'.$setting));
            }
        }

        return $findings;
    }

    /**
     * What the roads besides Oh Dear make of a failing check: an endpoint of the application's own that
     * a monitor pulls, or a channel besides mail.
     */
    private function otherRoads(): Inspection
    {
        $endpoint = $this->config('production-preflight.health_endpoint');

        if (is_string($endpoint) && trim($endpoint) !== '') {
            return Inspection::pass($this->message('health-notifications.pulled_own'));
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
