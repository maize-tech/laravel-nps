<?php

namespace Maize\Nps;

use Illuminate\Database\Eloquent\Builder;

class DefaultNpsFinder extends NpsFinder
{
    public function query(Builder $builder): Builder
    {
        $now = now();

        return $builder
            ->where('starts_at', '<=', $now)
            ->where('ends_at', '>', $now)
            ->oldest('ends_at');
    }
}
