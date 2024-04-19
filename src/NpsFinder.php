<?php

namespace Maize\Nps;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Cache;
use Maize\Nps\Models\Nps;

abstract class NpsFinder
{
    abstract public function query(Builder $builder): Builder;

    public function find(bool $fail = false): ?Nps
    {
        $model = $this->getNpsModel();
        $builder = $model::query();

        /** @var ?Nps $nps */
        $nps = Cache::remember(
            $model::npsCacheKey(),
            config('nps.cache.nps_ttl'),
            fn () => $this
                ->query($builder)
                ->get()
                ->first(
                    fn (Nps $nps) => $nps->isVisible()
                )
        );

        if (is_null($nps) && $fail) {
            throw (new ModelNotFoundException())->setModel(Nps::class);
        }

        return $nps;
    }

    protected function getNpsModel(): Nps
    {
        $npsModelClass = (string) config('nps.nps_model');

        return new $npsModelClass();
    }
}
