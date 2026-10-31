<?php

declare(strict_types=1);

namespace Pushery\ProductionPreflight\Checks\Host;

use Override;
use Pushery\ProductionPreflight\Checks\ProductionCheck;
use Pushery\ProductionPreflight\Evidence;
use Pushery\ProductionPreflight\Finding;
use Pushery\ProductionPreflight\Inspection;
use Pushery\ProductionPreflight\Scope;

/**
 * Whether the site serves `.env` or the Git configuration to anyone who asks.
 *
 * The application cannot know this: it depends on how the web server is configured, which no file
 * of the application describes. So the check asks the site itself, with a request to `app.url`, and
 * it does so only when switched on, since a health check that requests its own site every minute
 * is something to decide rather than to receive. Switched off, it does not guess from the file
 * system: it says it did not look.
 *
 * A file counts as served only when the answer is 200 and the body looks like the file. A site that
 * answers every path with its start page, as a single-page application does, therefore passes.
 * TLS is verified as everywhere else.
 */
final class EnvOverHttpCheck extends ProductionCheck
{
    /**
     * The paths requested, each with the pattern its body has to match to count as the file.
     *
     * @var array<string, string>
     */
    public const array PROBES = [
        '.env' => '/^[A-Z][A-Z\d_]*=/m',
        '.git/config' => '/^\[core\]/m',
    ];

    /**
     * How many bytes of an answer are read.
     */
    private const int READ = 65536;

    /**
     * The probe asks the site over HTTP only where it is switched on.
     */
    #[Override]
    public function canLookHere(): bool
    {
        return (bool) $this->config('production-preflight.http_probe.enabled');
    }

    #[Override]
    public function key(): string
    {
        return 'env-over-http';
    }

    #[Override]
    public function scope(): Scope
    {
        return Scope::Host;
    }

    #[Override]
    protected function inspectProduction(): Inspection
    {
        if (! (bool) $this->config('production-preflight.http_probe.enabled')) {
            return Inspection::undetermined($this->message('env-over-http.off'));
        }

        $url = $this->config('app.url');

        if (! is_string($url) || ! in_array(parse_url($url, PHP_URL_SCHEME), ['http', 'https'], true)) {
            return Inspection::undetermined($this->message('env-over-http.no_url'));
        }

        $findings = [];
        $unanswered = [];

        foreach (self::PROBES as $path => $pattern) {
            [$body, $reason] = $this->fetch(rtrim($url, '/').'/'.$path);

            if ($body === null) {
                // The reason names the address that was asked, a value of the application, so it is
                // evidence for the console rather than part of the summary.
                $unanswered[] = new Evidence($path, $reason);
            } elseif (preg_match($pattern, $body) === 1) {
                $findings[] = new Finding($path, $this->message('env-over-http.served'));
            }
        }

        if ($findings !== []) {
            return Inspection::fail($this->message('env-over-http.fail'), $findings);
        }

        return $unanswered === []
            ? Inspection::pass($this->message('env-over-http.pass'))
            : Inspection::undetermined($this->message('env-over-http.unanswered'), $unanswered);
    }

    /**
     * The body a URL answers with status 200, an empty string for any other status, or null when no
     * answer came at all, together with the reason PHP gave for that.
     *
     * @return array{?string, string}
     */
    private function fetch(string $url): array
    {
        $timeout = $this->config('production-preflight.http_probe.timeout');
        $context = stream_context_create(['http' => [
            'method' => 'GET',
            'timeout' => is_numeric($timeout) ? (float) $timeout : 5.0,
            'ignore_errors' => true,
            'follow_location' => 0,
        ]]);

        // A refused connection is a warning in PHP, and a framework turns it into an exception. Here
        // it is an answer: no answer, with the warning as its reason.
        $reason = 'file_get_contents() returned false';

        set_error_handler(static function (int $level, string $message) use (&$reason): bool {
            $reason = $message;

            return true;
        });

        try {
            $body = file_get_contents($url, false, $context, 0, self::READ);
        } finally {
            restore_error_handler();
        }

        if ($body === false) {
            return [null, $reason];
        }

        $status = http_get_last_response_headers()[0] ?? '';

        return [preg_match('#^HTTP/\S+\s+200\b#', $status) === 1 ? $body : '', ''];
    }
}
