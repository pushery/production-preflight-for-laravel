<?php

declare(strict_types=1);

namespace Pushery\ProductionPreflight\Env;

/**
 * One line of a `.env` that sets a key: the key, the value as written after the equals sign, and
 * the line it stands on.
 */
final readonly class Assignment
{
    public function __construct(
        public string $key,
        public string $raw,
        public int $line,
    ) {}

    /**
     * The quote the value is written in, a double or a single one, or null for an unquoted value.
     */
    public function quote(): ?string
    {
        $first = substr($this->raw, 0, 1);

        return $first === '"' || $first === "'" ? $first : null;
    }

    /**
     * The value as the application reads it, close enough to compare: a quoted value without its
     * quotes, an unquoted one up to its first `#`, which phpdotenv reads as the start of a comment,
     * and both without the whitespace around them.
     */
    public function value(): string
    {
        $quote = $this->quote();

        if ($quote !== null) {
            $closing = strpos($this->raw, $quote, 1);

            return $closing === false ? substr($this->raw, 1) : substr($this->raw, 1, $closing - 1);
        }

        $hash = strpos($this->raw, '#');

        return trim($hash === false ? $this->raw : substr($this->raw, 0, $hash));
    }
}
