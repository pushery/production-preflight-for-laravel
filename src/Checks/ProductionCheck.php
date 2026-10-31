<?php

declare(strict_types=1);

namespace Pushery\ProductionPreflight\Checks;

use Override;
use Pushery\ProductionPreflight\Inspection;
use Pushery\ProductionPreflight\Stage;

/**
 * A check whose rule holds in production and on staging, and nowhere else.
 *
 * On a development stage it passes and says why. On a stage nobody mapped it does not pass: it
 * says it could not tell, so a server that declares an unknown name is not taken for a laptop. A
 * check whose rule holds in production alone answers for staging itself, in {@see self::onStaging()}.
 */
abstract class ProductionCheck extends PreflightCheck
{
    abstract protected function inspectProduction(): Inspection;

    /**
     * What the check answers on staging, or null where its rule holds there as in production, which
     * is the case for nearly every check.
     */
    protected function onStaging(): ?Inspection
    {
        return null;
    }

    #[Override]
    final protected function inspect(): Inspection
    {
        $stage = $this->stage();
        $staging = $stage === Stage::Staging ? $this->onStaging() : null;

        if ($staging instanceof Inspection) {
            return $staging;
        }

        if ($stage->isProductionLike()) {
            return $this->inspectProduction();
        }

        return $stage === Stage::Undetermined
            ? Inspection::undetermined($this->message('stage.undetermined'))
            : Inspection::pass($this->message('stage.development'));
    }
}
