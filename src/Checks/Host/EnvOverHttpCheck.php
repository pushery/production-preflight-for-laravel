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
 *
 * The probe follows no redirect. A redirect that keeps the path at another scheme, host or port, as
 * one from `http` to `https` or from the bare domain to `www` does, means the file would be answered
 * at that address, which was not asked, so the check says it did not look and names the address as
 * evidence. A redirect away from the path, to a login page say, serves no file and passes.
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
        $redirected = [];

        foreach (self::PROBES as $path => $pattern) {
            [$body, $reason, $target] = $this->fetch(rtrim($url, '/').'/'.$path);

            if ($body === null) {
                // The reason names the address that was asked, a value of the application, so it is
                // evidence for the console rather than part of the summary.
                $unanswered[] = new Evidence($path, $reason);
            } elseif ($target !== null) {
                $redirected[] = new Evidence($path, $this->message('env-over-http.moved', ['target' => $target]));
            } elseif (preg_match($pattern, $body) === 1) {
                $findings[] = new Finding($path, $this->message('env-over-http.served'));
            }
        }

        if ($findings !== []) {
            return Inspection::fail($this->message('env-over-http.fail'), $findings);
        }

        if ($unanswered !== []) {
            return Inspection::undetermined($this->message('env-over-http.unanswered'), [...$unanswered, ...$redirected]);
        }

        return $redirected === []
            ? Inspection::pass($this->message('env-over-http.pass'))
            : Inspection::undetermined($this->message('env-over-http.redirected'), $redirected);
    }

    /**
     * The body a URL answers with status 200, an empty string for any other status, or null when no
     * answer came at all, together with the reason PHP gave for that, and the address a redirect
     * sends the same path to where it keeps it at another scheme, host or port.
     *
     * @return array{?string, string, ?string}
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
            return [null, $reason, null];
        }

        $headers = http_get_last_response_headers() ?? [];
        $status = $headers[0] ?? '';

        if (preg_match('#^HTTP/\S+\s+3\d\d\b#', $status) === 1) {
            return ['', '', $this->samePathElsewhere($url, $headers)];
        }

        return [preg_match('#^HTTP/\S+\s+200\b#', $status) === 1 ? $body : '', '', null];
    }

    /**
     * The address a redirect names, when it keeps the path of the URL asked and changes its scheme,
     * host or port. Null for a redirect to another path, to itself, or without a `Location`.
     *
     * @param  list<string>  $headers
     */
    private function samePathElsewhere(string $url, array $headers): ?string
    {
        $location = null;

        foreach ($headers as $header) {
            if (preg_match('/^Location:\s*(\S+)/i', $header, $match) === 1) {
                $location = $match[1];
            }
        }

        if ($location === null) {
            return null;
        }

        $target = $this->resolve($url, $location);

        return $this->path($target) === $this->path($url) && $this->origin($target) !== $this->origin($url) ? $target : null;
    }

    /**
     * A `Location` as the absolute address it names, read against the URL that answered with it: a
     * full address as it stands, one that begins with `//` on the scheme of the URL, a path from the
     * root on its scheme, host and port, and any other path beside the last segment of its path.
     */
    private function resolve(string $url, string $location): string
    {
        if (preg_match('#^[a-z][a-z\d+.-]*:#i', $location) === 1) {
            return $location;
        }

        $scheme = parse_url($url, PHP_URL_SCHEME);
        $scheme = is_string($scheme) ? $scheme : 'http';

        if (str_starts_with($location, '//')) {
            return $scheme.':'.$location;
        }

        $host = parse_url($url, PHP_URL_HOST);
        $port = parse_url($url, PHP_URL_PORT);
        $base = $scheme.'://'.(is_string($host) ? $host : '').(is_int($port) ? ':'.$port : '');

        return str_starts_with($location, '/')
            ? $base.$location
            : $base.(preg_replace('#[^/]*\z#', '', $this->path($url)) ?? '').$location;
    }

    /**
     * The path of an address, the empty string where it names none.
     */
    private function path(string $url): string
    {
        $path = parse_url($url, PHP_URL_PATH);

        return is_string($path) ? $path : '';
    }

    /**
     * The scheme, host and port of an address, compared without case, and with the port its scheme
     * implies where it names none.
     */
    private function origin(string $url): string
    {
        $scheme = parse_url($url, PHP_URL_SCHEME);
        $scheme = is_string($scheme) ? strtolower($scheme) : '';
        $host = parse_url($url, PHP_URL_HOST);
        $port = parse_url($url, PHP_URL_PORT);

        return $scheme.'://'.(is_string($host) ? strtolower($host) : '').':'.(is_int($port) ? $port : ($scheme === 'https' ? 443 : 80));
    }
}
