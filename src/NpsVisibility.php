<?php

namespace Maize\Nps;

use Maize\Nps\Models\Nps;

abstract class NpsVisibility
{
    abstract public function __invoke(Nps $nps): bool;
}
