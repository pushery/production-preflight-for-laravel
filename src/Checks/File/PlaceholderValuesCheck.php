<?php

declare(strict_types=1);

namespace Pushery\ProductionPreflight\Checks\File;

use Override;
use Pushery\ProductionPreflight\Checks\PreflightCheck;
use Pushery\ProductionPreflight\Env\Assignment;
use Pushery\ProductionPreflight\Env\EnvFile;
use Pushery\ProductionPreflight\Evidence;
use Pushery\ProductionPreflight\Finding;
use Pushery\ProductionPreflight\Inspection;
use Pushery\ProductionPreflight\Scope;
use Pushery\ProductionPreflight\Stage;

/**
 * A value of the `.env` that is still the example's placeholder, in production and on staging.
 *
 * Three findings: a value that matches a placeholder of `production-preflight.placeholders.values`
 * (`changeme`, `your-api-key-here`), a secret whose value is the one the committed example file
 * carries, and a key of `production-preflight.placeholders.required` that is missing or empty.
 * Every project has its own words, so the list is configuration.
 *
 * `null` is not on the shipped list, deliberately. Laravel's own example file sets
 * `REDIS_PASSWORD=null` and `MAIL_PASSWORD=null`, and a check that flagged every default
 * installation would be switched off at once. A project that never means it adds it.
 *
 * The stage is read from the file's own `APP_ENV` where it sets one, so a production file linted in
 * a pipeline that runs as `testing` is still held to the production rule.
 */
final class PlaceholderValuesCheck extends PreflightCheck
{
    /**
     * The names that hold a credential unless the configuration says otherwise.
     */
    public const string SECRET_KEYS = '/(?:^|_)(?:KEY|SECRET|PASSWORD|TOKEN)$/i';

    #[Override]
    public function key(): string
    {
        return 'placeholder-values';
    }

    #[Override]
    public function scope(): Scope
    {
        return Scope::File;
    }

    #[Override]
    protected function inspect(): Inspection
    {
        $file = $this->envFile();

        if (! $file instanceof EnvFile) {
            return $this->unreadable($this->envPath());
        }

        $stage = Stage::named($file->last('APP_ENV')?->value() ?? $this->config('app.env'), $this->config('production-preflight.stages'));

        if (! $stage->isProductionLike()) {
            return $stage === Stage::Undetermined
                ? Inspection::undetermined($this->message('stage.undetermined'))
                : Inspection::pass($this->message('stage.development'));
        }

        $example = EnvFile::read($this->examplePath());
        $findings = [];
        $evidence = [];

        foreach ($file->keys() as $key) {
            $value = $file->last($key)?->value() ?? '';
            $reason = match (true) {
                $value === '' => null,
                $this->isPlaceholder($value) => 'placeholder-values.placeholder',
                $example instanceof EnvFile && $this->isSecret($key) && ! $this->isLiteral($value) && $example->last($key)?->value() === $value => 'placeholder-values.from_example',
                default => null,
            };

            if ($reason !== null) {
                $findings[] = new Finding($key, $this->message($reason));
                $evidence[] = new Evidence($key, $value);
            }
        }

        foreach ($this->strings('production-preflight.placeholders.required') as $key) {
            $assignment = $file->last($key);

            if (! $assignment instanceof Assignment || $assignment->value() === '') {
                $findings[] = new Finding($key, $this->message('placeholder-values.required'));
            }
        }

        return $findings === []
            ? Inspection::pass($this->message('placeholder-values.pass'))
            : Inspection::fail($this->message('placeholder-values.fail', ['count' => count($findings)]), $findings, $evidence);
    }

    /**
     * Whether a value is one of the configured placeholders: compared without case, and an entry
     * that ends in `*` matching every value it begins.
     */
    private function isPlaceholder(string $value): bool
    {
        $value = strtolower($value);

        foreach ($this->strings('production-preflight.placeholders.values') as $placeholder) {
            $placeholder = strtolower($placeholder);
            $matches = str_ends_with($placeholder, '*')
                ? str_starts_with($value, substr($placeholder, 0, -1))
                : $value === $placeholder;

            if ($matches) {
                return true;
            }
        }

        return false;
    }

    /**
     * Whether a key's name says it holds a credential, by `production-preflight.placeholders.secret_keys`.
     */
    private function isSecret(string $key): bool
    {
        $pattern = $this->config('production-preflight.placeholders.secret_keys', self::SECRET_KEYS);

        return is_string($pattern) && @preg_match($pattern, $key) === 1;
    }

    /**
     * Whether a value is one of the words `env()` turns into null, an empty string or a boolean.
     * `REDIS_PASSWORD=null` stands in Laravel's example file and in most `.env` files, and it holds
     * no secret anyone could learn from the repository.
     */
    private function isLiteral(string $value): bool
    {
        return in_array(strtolower($value), ['null', '(null)', 'empty', '(empty)', 'true', '(true)', 'false', '(false)'], true);
    }

    /**
     * The strings of a list setting, anything else left out.
     *
     * @return list<string>
     */
    private function strings(string $setting): array
    {
        $configured = $this->config($setting);

        return is_array($configured) ? array_values(array_filter($configured, is_string(...))) : [];
    }
}
