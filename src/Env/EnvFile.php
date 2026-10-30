<?php

declare(strict_types=1);

namespace Pushery\ProductionPreflight\Env;

/**
 * A `.env` file read as text, with the lines that set a key.
 *
 * A false positive costs more here than a missing check: a warning about a file that is in order
 * gets the guard switched off, and then it is missing at the moment it was built for. So the reader
 * takes the special cases one by one instead of trusting one regular expression:
 *
 * - A comment line, `# KEY=value`, sets nothing.
 * - The `export` prefix is not part of the key.
 * - A value may contain `=`, `DSN="a=b;c=d"`: the line is split at its FIRST equals sign.
 * - An empty value, `KEY=`, sets the key and counts.
 * - A quoted value that runs over several lines is skipped to its closing quote, or a line of an
 *   embedded certificate would read as a key of its own.
 * - Only a valid name is a key, so a line of prose with an equals sign in it sets nothing.
 *
 * Lines end where phpdotenv ends them, at a line feed, a carriage return or both, and a byte order
 * mark in front of the first key is dropped the way phpdotenv drops it. The bytes stay as they were
 * read, so the checks that care about the bytes themselves, the mark or a carriage return, look at
 * them rather than at a line that has already swallowed them.
 */
final readonly class EnvFile
{
    private const string BYTE_ORDER_MARK = "\xEF\xBB\xBF";

    /**
     * @param  list<Assignment>  $assignments  in the order of the file
     */
    private function __construct(
        public string $path,
        public string $bytes,
        public array $assignments,
    ) {}

    /**
     * The file at a path, or null when it cannot be read: a missing file is not an empty one.
     */
    public static function read(string $path): ?self
    {
        $bytes = is_file($path) && is_readable($path) ? file_get_contents($path) : false;

        return $bytes === false ? null : self::parse($path, $bytes);
    }

    public static function parse(string $path, string $bytes): self
    {
        $assignments = [];
        $openQuote = null;
        $text = str_starts_with($bytes, self::BYTE_ORDER_MARK) ? substr($bytes, 3) : $bytes;

        foreach (preg_split('/\r\n|\n|\r/', $text) ?: [] as $index => $line) {
            if ($openQuote !== null) {
                if (str_contains($line, $openQuote)) {
                    $openQuote = null;
                }

                continue;
            }

            $assignment = self::assignment($line, $index + 1);

            if ($assignment instanceof Assignment) {
                $assignments[] = $assignment;
                $openQuote = self::unclosedQuote($assignment->raw);
            }
        }

        return new self($path, $bytes, $assignments);
    }

    /**
     * Every key the file sets, once each, in the order of its first line.
     *
     * @return list<string>
     */
    public function keys(): array
    {
        return array_values(array_unique(array_map(static fn (Assignment $assignment): string => $assignment->key, $this->assignments)));
    }

    /**
     * The line in effect for a key, the last one that sets it; null when the file does not set it.
     */
    public function last(string $key): ?Assignment
    {
        $found = null;

        foreach ($this->assignments as $assignment) {
            if ($assignment->key === $key) {
                $found = $assignment;
            }
        }

        return $found;
    }

    /**
     * Every key set on more than one line, in the order of its first line.
     *
     * @return list<DuplicateEnvKey>
     */
    public function duplicates(): array
    {
        $duplicates = [];

        foreach ($this->keys() as $key) {
            $assignments = array_values(array_filter(
                $this->assignments,
                static fn (Assignment $assignment): bool => $assignment->key === $key,
            ));

            if (count($assignments) > 1) {
                $duplicates[] = new DuplicateEnvKey($key, $assignments);
            }
        }

        return $duplicates;
    }

    /**
     * Whether the file starts with a UTF-8 byte order mark.
     */
    public function hasByteOrderMark(): bool
    {
        return str_starts_with($this->bytes, self::BYTE_ORDER_MARK);
    }

    /**
     * How many lines end in a carriage return, alone or before a line feed.
     */
    public function carriageReturns(): int
    {
        return substr_count($this->bytes, "\r");
    }

    private static function assignment(string $line, int $number): ?Assignment
    {
        $trimmed = ltrim($line);

        // A comment would fail the name test below as well, so this line is redundant today. It
        // stays so that loosening the name test, to admit a hyphen say, cannot turn a commented
        // line into a key.
        if ($trimmed === '' || str_starts_with($trimmed, '#')) {
            return null;
        }

        if (str_starts_with($trimmed, 'export ')) {
            $trimmed = ltrim(substr($trimmed, 7));
        }

        $separator = strpos($trimmed, '=');

        if ($separator === false) {
            return null;
        }

        $key = rtrim(substr($trimmed, 0, $separator));

        if (preg_match('/^[A-Za-z_][\w.]*$/', $key) !== 1) {
            return null;
        }

        return new Assignment($key, ltrim(substr($trimmed, $separator + 1)), $number);
    }

    /**
     * The quote a value opens and does not close on its own line, which carries it on to the next.
     */
    private static function unclosedQuote(string $raw): ?string
    {
        $first = substr($raw, 0, 1);

        if ($first !== '"' && $first !== "'") {
            return null;
        }

        return str_contains(substr($raw, 1), $first) ? null : $first;
    }
}
