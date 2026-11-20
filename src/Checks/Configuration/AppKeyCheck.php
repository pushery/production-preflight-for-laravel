<?php

declare(strict_types=1);

namespace Pushery\ProductionPreflight\Checks\Configuration;

use Override;
use Pushery\ProductionPreflight\Checks\ProductionCheck;
use Pushery\ProductionPreflight\Env\EnvFile;
use Pushery\ProductionPreflight\Finding;
use Pushery\ProductionPreflight\Inspection;
use Pushery\ProductionPreflight\Scope;

/**
 * An application key that is missing or that others know.
 *
 * The key signs and encrypts sessions, cookies and every encrypted payload. A key somebody else knows
 * lets them forge each of those. Two keys are known without any leak: the placeholders old Laravel
 * releases shipped in their example file, and whatever key the project's own committed example file
 * carries, because everyone who reads the repository reads it.
 *
 * The previous keys of `app.previous_keys`, which Laravel fills from `APP_PREVIOUS_KEYS`, are held to
 * the same rule. Laravel decrypts with each of them and accepts a URL signed with any of them, so a
 * rotation that moved a known key there leaves cookies, encrypted payloads and signed URLs as open as
 * before. A finding names the position of such a key in the list, never the key.
 */
final class AppKeyCheck extends ProductionCheck
{
    /**
     * The placeholders Laravel's example file carried before `key:generate` wrote a real key.
     *
     * @var list<string>
     */
    public const array PLACEHOLDERS = ['SomeRandomString', 'SomeRandomStringWith32Characters', 'base64:'];

    #[Override]
    public function key(): string
    {
        return 'app-key';
    }

    #[Override]
    public function scope(): Scope
    {
        return Scope::Configuration;
    }

    #[Override]
    protected function inspectProduction(): Inspection
    {
        $key = $this->config('app.key');
        $committed = $this->committedKeys();

        $problem = match (true) {
            ! is_string($key) || trim($key) === '' => 'app-key.missing',
            in_array(trim($key), self::PLACEHOLDERS, true) => 'app-key.placeholder',
            in_array(trim($key), $committed, true) => 'app-key.committed',
            default => null,
        };

        $findings = $problem === null ? [] : [new Finding('APP_KEY', $this->message($problem))];

        foreach ($this->previousKeys() as $position => $previous) {
            $reason = match (true) {
                in_array($previous, self::PLACEHOLDERS, true) => 'app-key.previous_placeholder',
                in_array($previous, $committed, true) => 'app-key.previous_committed',
                default => null,
            };

            if ($reason !== null) {
                $findings[] = new Finding('APP_PREVIOUS_KEYS', $this->message($reason, ['position' => $position]));
            }
        }

        return $findings === []
            ? Inspection::pass($this->message('app-key.pass'))
            : Inspection::fail($this->message('app-key.fail'), $findings);
    }

    /**
     * The keys everyone who reads the repository knows: the `APP_KEY` of the committed example file
     * and each key of its `APP_PREVIOUS_KEYS`.
     *
     * @return list<string>
     */
    private function committedKeys(): array
    {
        $example = EnvFile::read($this->examplePath());

        if (! $example instanceof EnvFile) {
            return [];
        }

        $keys = [
            $example->last('APP_KEY')?->value() ?? '',
            ...explode(',', $example->last('APP_PREVIOUS_KEYS')?->value() ?? ''),
        ];

        return array_values(array_filter(array_map(trim(...), $keys), static fn (string $key): bool => $key !== ''));
    }

    /**
     * The previous keys of `app.previous_keys`, trimmed, by their position in the list counted from
     * 1. An entry that is no text or holds nothing but spaces is no key and is left out.
     *
     * @return array<int, string>
     */
    private function previousKeys(): array
    {
        $configured = $this->config('app.previous_keys');
        $keys = [];

        foreach (is_array($configured) ? array_values($configured) : [] as $index => $entry) {
            if (is_string($entry) && trim($entry) !== '') {
                $keys[$index + 1] = trim($entry);
            }
        }

        return $keys;
    }
}
