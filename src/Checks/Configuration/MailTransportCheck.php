<?php

declare(strict_types=1);

namespace Pushery\ProductionPreflight\Checks\Configuration;

use Override;
use Pushery\ProductionPreflight\Checks\ProductionCheck;
use Pushery\ProductionPreflight\Finding;
use Pushery\ProductionPreflight\Inspection;
use Pushery\ProductionPreflight\Scope;
use Pushery\ProductionPreflight\Verdict;

/**
 * A default mailer through which no message leaves the server.
 *
 * Every mail then vanishes without a sound: the registration, the password reset, the invoice. The
 * `log` and `array` transports accept every message and deliver none, and SMTP to a mail catcher,
 * `mailpit` or anything on port 1025, keeps them in a box no customer reads. Those fail.
 *
 * SMTP to the local machine on another port warns. A relay there is a valid setup, Postfix on the
 * same server, and a check that failed every one of them would be switched off; but it is also where
 * a forgotten development setting points, and only the person who set it knows which.
 *
 * A `failover` mailer sends through its first mailer and moves on only when that one fails, which
 * `log` never does, so the first mailer decides. A `roundrobin` mailer spreads the messages over all
 * of its mailers, so any dead end swallows a share of them.
 *
 * This is the one finding the operating mode cannot deliver by itself: laravel-health notifies by
 * mail unless told otherwise. The deploy command is where it is caught, and the
 * `health-notifications` check asks for a second channel for the time after the deploy.
 */
final class MailTransportCheck extends ProductionCheck
{
    /**
     * Host names of mail catchers.
     *
     * @var list<string>
     */
    public const array CATCHERS = ['mailpit', 'mailhog', 'mailcatcher', 'mailtrap', 'smtp.mailtrap.io', 'sandbox.smtp.mailtrap.io'];

    /**
     * Host names of the machine itself.
     *
     * @var list<string>
     */
    public const array LOCAL_HOSTS = ['localhost', '127.0.0.1', '::1', '[::1]'];

    /**
     * The port mail catchers listen on for SMTP.
     */
    public const int CATCHER_PORT = 1025;

    /**
     * How deep `failover` and `roundrobin` mailers are followed into each other.
     */
    private const int DEPTH = 3;

    #[Override]
    public function key(): string
    {
        return 'mail-transport';
    }

    #[Override]
    public function scope(): Scope
    {
        return Scope::Configuration;
    }

    #[Override]
    protected function inspectProduction(): Inspection
    {
        $default = $this->config('mail.default');
        $mailers = $this->config('mail.mailers');

        if (! is_string($default) || ! is_array($mailers)) {
            return Inspection::fail($this->message('mail-transport.fail'), [new Finding('MAIL_MAILER', $this->message('mail-transport.unconfigured'))]);
        }

        $fails = $this->deadEnds($default, $mailers, 0, Verdict::Fail);
        $warnings = $this->deadEnds($default, $mailers, 0, Verdict::Warn);

        if ($fails !== []) {
            return Inspection::fail($this->message('mail-transport.fail'), [...$fails, ...$warnings]);
        }

        return $warnings === []
            ? Inspection::pass($this->message('mail-transport.pass'))
            : Inspection::warn($this->message('mail-transport.warn'), $warnings);
    }

    /**
     * Where a mailer lets mail end without delivery, the findings of one verdict.
     *
     * @param  array<array-key, mixed>  $mailers
     * @return list<Finding>
     */
    private function deadEnds(string $name, array $mailers, int $depth, Verdict $verdict): array
    {
        $mailer = $mailers[$name] ?? null;
        $subject = 'mail.mailers.'.$name;

        if (! is_array($mailer) || $depth > self::DEPTH) {
            return $verdict === Verdict::Fail ? [new Finding($subject, $this->message('mail-transport.unconfigured'))] : [];
        }

        $transport = $mailer['transport'] ?? null;
        $transport = is_string($transport) ? strtolower($transport) : '';
        $members = $mailer['mailers'] ?? null;
        $members = is_array($members) ? array_values(array_filter($members, is_string(...))) : [];

        if ($transport === 'log' || $transport === 'array') {
            return $verdict === Verdict::Fail ? [new Finding($subject, $this->message('mail-transport.'.$transport))] : [];
        }

        if ($transport === 'smtp') {
            return $this->smtp($subject, $mailer, $verdict);
        }

        if ($transport === 'failover') {
            return $members === [] ? [] : $this->deadEnds($members[0], $mailers, $depth + 1, $verdict);
        }

        $found = [];

        if ($transport === 'roundrobin') {
            foreach ($members as $member) {
                $found = [...$found, ...$this->deadEnds($member, $mailers, $depth + 1, $verdict)];
            }
        }

        return $found;
    }

    /**
     * Where an SMTP mailer sends: a catcher fails, the machine itself warns.
     *
     * @param  array<array-key, mixed>  $mailer
     * @return list<Finding>
     */
    private function smtp(string $subject, array $mailer, Verdict $verdict): array
    {
        $host = $mailer['host'] ?? null;
        $host = is_string($host) ? strtolower(trim($host)) : '';
        $port = $mailer['port'] ?? null;
        $port = is_numeric($port) ? (int) $port : 0;
        $catcher = in_array($host, self::CATCHERS, true) || $port === self::CATCHER_PORT;

        return match (true) {
            $catcher && $verdict === Verdict::Fail => [new Finding($subject, $this->message('mail-transport.catcher'))],
            ! $catcher && $verdict === Verdict::Warn && in_array($host, self::LOCAL_HOSTS, true) => [new Finding($subject, $this->message('mail-transport.local'))],
            default => [],
        };
    }
}
