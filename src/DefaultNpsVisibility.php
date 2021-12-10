<?php

namespace Maize\Nps;

use Maize\Nps\Models\Nps;

class DefaultNpsVisibility extends NpsVisibility
{
    public function __invoke(Nps $nps): bool
    {
        return true;
    }
}
