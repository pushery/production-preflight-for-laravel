<?php

declare(strict_types=1);

namespace Pushery\ProductionPreflight\Env;

/**
 * A key a `.env` sets more than once, with every line that sets it.
 *
 * The lines and the values both belong to it: the lines say where to correct, the values whether
 * it makes a difference. Two equal values are untidiness, two different ones mean a setting stands
 * in the file and does not apply, and the report keeps the two apart instead of saying both alike.
 */
final readonly class DuplicateEnvKey
{
    /**
     * @param  non-empty-list<Assignment>  $assignments  in the order of the file
     */
    public function __construct(
        public string $key,
        public array $assignments,
    ) {}

    /**
     * The lines that set the key, in order.
     *
     * @return list<int>
     */
    public function lines(): array
    {
        return array_map(static fn (Assignment $assignment): int => $assignment->line, $this->assignments);
    }

    /**
     * The value in effect, the one of the LAST line: phpdotenv lets a later line of the same file
     * overwrite an earlier one, and protects only what the real environment held before loading.
     */
    public function effective(): Assignment
    {
        return $this->assignments[count($this->assignments) - 1];
    }

    /**
     * Whether the lines set different values, which separates untidy from dangerous.
     */
    public function valuesDiffer(): bool
    {
        return count(array_unique(array_map(static fn (Assignment $assignment): string => $assignment->value(), $this->assignments))) > 1;
    }
}
