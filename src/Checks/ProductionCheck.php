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
 * says it could not tell, so a server that declares an unknown name is not taken for a laptop.
 */
abstract class ProductionCheck extends PreflightCheck
{
    abstract protected function inspectProduction(): Inspection;

    #[Override]
    final protected function inspect(): Inspection
    {
        $stage = $this->stage();

        if ($stage->isProductionLike()) {
            return $this->inspectProduction();
        }

        return $stage === Stage::Undetermined
            ? Inspection::undetermined($this->message('stage.undetermined'))
            : Inspection::pass($this->message('stage.development'));
    }
}
