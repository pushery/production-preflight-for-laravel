<?php

declare(strict_types=1);

namespace Pushery\ProductionPreflight;

/**
 * Which stage an application declares itself in, read from a name such as `app.env`.
 *
 * Staging is a stage of its own, and the production rules apply to it: a staging installation
 * often holds real customer data, and a rule that listened only to `production` would keep quiet
 * exactly there. A name nobody mapped is `Undetermined`, never development: reading silence as
 * "not production" would pass a server nobody placed.
 */
enum Stage: string
{
    case Production = 'production';
    case Staging = 'staging';
    case Development = 'development';
    case Undetermined = 'undetermined';

    /**
     * The names each stage answers to unless `production-preflight.stages` says otherwise. The
     * shipped config file carries the same lists.
     *
     * @var array<string, list<string>>
     */
    public const array NAMES = [
        'production' => ['production', 'prod', 'live'],
        'staging' => ['staging', 'stage', 'preprod', 'uat'],
        'development' => ['local', 'testing', 'test', 'dev', 'development', 'ci'],
    ];

    /**
     * The stage a name declares, compared without case and surrounding whitespace.
     *
     * @param  mixed  $configured  the `production-preflight.stages` setting; a stage it does not
     *                             map keeps the names of NAMES
     */
    public static function named(mixed $name, mixed $configured = null): self
    {
        $declared = is_string($name) ? strtolower(trim($name)) : '';

        if ($declared === '') {
            return self::Undetermined;
        }

        foreach ([self::Production, self::Staging, self::Development] as $stage) {
            $mapped = is_array($configured) ? ($configured[$stage->value] ?? null) : null;
            $names = is_array($mapped) ? $mapped : self::NAMES[$stage->value];

            foreach ($names as $known) {
                if (is_string($known) && strtolower(trim($known)) === $declared) {
                    return $stage;
                }
            }
        }

        return self::Undetermined;
    }

    /**
     * Whether the production rules apply: in production and on staging.
     */
    public function isProductionLike(): bool
    {
        return $this === self::Production || $this === self::Staging;
    }
}
