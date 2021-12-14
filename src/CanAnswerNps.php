<?php

namespace Maize\Nps;

use Illuminate\Support\Facades\Cache;
use Maize\Nps\Models\Nps;

trait CanAnswerNps
{
    public function hasAnsweredCurrentNps(): bool
    {
        $nps = $this->findCurrentNps();

        if (is_null($nps)) {
            return true;
        }

        return $this->hasAnsweredNps($nps);
    }

    public function hasAnsweredNps(Nps $nps): bool
    {
        $npsAnswerModelClass = config('nps.nps_answer_model');

        return Cache::remember(
            $npsAnswerModelClass::npsAnswerCacheKey($nps, $this),
            config('nps.cache.nps_answer_ttl'),
            fn () => $npsAnswerModelClass::query()
                ->where([
                    'nps_id' => $nps->getKey(),
                    'user_id' => $this->getKey(),
                ])
                ->exists()
        );
    }

    public function answerNps(Nps $nps, array $data): void
    {
        $npsAnswerModelClass = config('nps.nps_answer_model');

        $npsAnswerModelClass::firstOrCreate([
            'nps_id' => $nps->getKey(),
            'user_id' => $this->getKey(),
        ], $data);

        $this->markNpsAnswered($nps);
    }

    public function delayNps(Nps $nps): void
    {
        $this->markNpsAnswered($nps);
    }

    public function findCurrentNps(bool $fail = false): ?Nps
    {
        $finderClass = config('nps.nps_finder');

        return app($finderClass)->find($fail);
    }

    protected function markNpsAnswered(Nps $nps): void
    {
        $npsAnswerModelClass = config('nps.nps_answer_model');

        Cache::put(
            $npsAnswerModelClass::npsAnswerCacheKey($nps, $this),
            true,
            config('nps.cache.nps_answer_ttl')
        );
    }
}
