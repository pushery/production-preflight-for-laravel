<?php

declare(strict_types=1);

namespace Pushery\ProductionPreflight;

/**
 * One thing a check found, said without a value.
 *
 * A finding names its subject, a key of the `.env` or a configuration setting, and says what is
 * wrong with it in words that hold no value of the application. It has no field a value could go
 * into. That is what makes it the form that reaches every channel that keeps what it is given:
 * laravel-health's result store and notifications, the log, a monitor that polls the health
 * endpoint. A `.env` consists of credentials, and a value written into one of those channels
 * outlives the moment it was read.
 *
 * What a person at the terminal needs on top of it, the values themselves, is an Evidence, a
 * separate type that only the console prints.
 */
final readonly class Finding
{
    public function __construct(
        public string $subject,
        public string $statement,
    ) {}

    /**
     * The finding as one line, subject first.
     */
    public function describe(): string
    {
        return $this->subject.': '.$this->statement;
    }

    /**
     * The finding as the data laravel-health keeps beside a result.
     *
     * @return array{subject: string, statement: string}
     */
    public function toArray(): array
    {
        return ['subject' => $this->subject, 'statement' => $this->statement];
    }
}
