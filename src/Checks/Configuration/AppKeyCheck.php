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
        $example = EnvFile::read($this->examplePath())?->last('APP_KEY')?->value();

        $problem = match (true) {
            ! is_string($key) || trim($key) === '' => 'app-key.missing',
            in_array(trim($key), self::PLACEHOLDERS, true) => 'app-key.placeholder',
            $example !== null && $example !== '' && $example === $key => 'app-key.committed',
            default => null,
        };

        return $problem === null
            ? Inspection::pass($this->message('app-key.pass'))
            : Inspection::fail($this->message('app-key.fail'), [new Finding('APP_KEY', $this->message($problem))]);
    }
}
